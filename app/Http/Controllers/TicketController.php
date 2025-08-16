<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /** List tiket milik user */
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', ''));      // id / subject / order_id
        $status   = trim((string) $request->input('status', '')); // open|pending|closed
        $perPage  = max(10, min(50, (int) $request->integer('per_page', 20)));
        $uid      = (int) $request->user()->id;

        $rows = Ticket::query()
            ->where('user_id', $uid)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('tickets.id', (int) $q)
                          ->orWhere('tickets.order_id', (int) $q);
                    }
                    $w->orWhere('tickets.subject', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('tickets.status', $status))
            ->orderByDesc('tickets.last_message_at')
            ->orderByDesc('tickets.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('tickets.index', [
            'rows'    => $rows,
            'filters' => ['q' => $q, 'status' => $status, 'per_page' => $perPage],
        ]);
    }

    /** Form buat tiket */
    public function create()
    {
        return view('tickets.create');
    }

    /** Simpan tiket baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'    => ['required', 'string', 'max:150'],
            'order_id'   => ['nullable', 'integer', 'min:1'],
            'message'    => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'user_id'         => $request->user()->id,
            'subject'         => $data['subject'],
            'order_id'        => $data['order_id'] ?? null,
            'status'          => 'open',
            'last_message_at' => now(),
            'last_message_by' => 'user',
            'meta'            => ['ua' => substr((string)$request->userAgent(), 0, 255)],
        ]);

        TicketMessage::create([
            'ticket_id'       => $ticket->id,
            'sender'          => 'user',
            'user_id'         => $request->user()->id,
            'message'         => $data['message'],
            'attachment_path' => $path,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Tiket berhasil dibuat.');
    }

    /** Detail & thread tiket (hanya pemilik) */
    public function show(Ticket $ticket, Request $request)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);

        $messages = TicketMessage::where('ticket_id', $ticket->id)
            ->orderBy('id')
            ->get();

        return view('tickets.show', compact('ticket', 'messages'));
    }

    /** Balas tiket (user) */
    public function reply(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'message'    => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        if ((!$data['message'] || trim($data['message']) === '') && !$request->hasFile('attachment')) {
            return back()->with('status', 'Isi pesan atau lampirkan berkas.');
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id'       => $ticket->id,
            'sender'          => 'user',
            'user_id'         => $request->user()->id,
            'message'         => $data['message'] ?? '',
            'attachment_path' => $path,
        ]);

        // Balasan user akan “membuka kembali” tiket jika status closed
        $ticket->update([
            'status'          => $ticket->status === 'closed' ? 'open' : $ticket->status,
            'last_message_at' => now(),
            'last_message_by' => 'user',
        ]);

        return back()->with('status', 'Balasan terkirim.');
    }

    /** Download lampiran (user hanya boleh miliknya) */
    public function download(TicketMessage $message, Request $request)
    {
        $ticket = Ticket::findOrFail($message->ticket_id);
        abort_if($ticket->user_id !== $request->user()->id, 403);

        if (!$message->attachment_path) abort(404);

        return Storage::disk('public')->download($message->attachment_path);
    }
}
