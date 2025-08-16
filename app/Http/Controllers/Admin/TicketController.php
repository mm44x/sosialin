<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string) $request->input('q', '')); // id / subject / order_id / email
        $status   = trim((string) $request->input('status', '')); // open|pending|closed
        $from     = $request->date('from');
        $to       = $request->date('to');
        $perPage  = max(10, min(50, (int) $request->integer('per_page', 20)));

        $rows = Ticket::query()
            ->with(['user:id,name,email'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) {
                        $w->orWhere('tickets.id', (int) $q)
                          ->orWhere('tickets.order_id', (int) $q);
                    }
                    $w->orWhere('tickets.subject', 'like', "%{$q}%")
                      ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")
                                                      ->orWhere('name',  'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('tickets.status', $status))
            ->when($from, fn($qq) => $qq->whereDate('tickets.created_at', '>=', $from))
            ->when($to,   fn($qq) => $qq->whereDate('tickets.created_at', '<=', $to))
            ->orderByDesc('tickets.last_message_at')
            ->orderByDesc('tickets.id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.tickets.index', [
            'rows'    => $rows,
            'filters' => [
                'q'        => $q,
                'status'   => $status,
                'from'     => optional($from)?->format('Y-m-d'),
                'to'       => optional($to)?->format('Y-m-d'),
                'per_page' => $perPage,
            ],
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user:id,name,email']);

        // Ambil thread (terbaru di bawah)
        $messages = TicketMessage::where('ticket_id', $ticket->id)
            ->orderBy('id') // kronologis
            ->get();

        return view('admin.tickets.show', compact('ticket', 'messages'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        // Admin balas: minimal ada teks ATAU lampiran
        $data = $request->validate([
            'message'    => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'status'     => ['nullable', 'in:open,pending,closed'],
        ]);

        if ((!$data['message'] || trim($data['message']) === '') && !$request->hasFile('attachment')) {
            return back()->with('status', 'Isi pesan atau lampirkan berkas.');
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('tickets', 'public');
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => null, // Admin message
            'is_admin'  => true,
            'body'      => $data['message'] ?? '',
            'meta'      => $path ? ['attachment_path' => $path] : null,
        ]);

        // Update metadata tiket
        $ticket->update([
            'last_message_at' => now(),
            // 'last_message_by' column doesn't exist yet
            'status'          => $data['status'] ?? $ticket->status, // bisa sekaligus ubah status
        ]);

        return back()->with('status', 'Balasan terkirim.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,pending,closed'],
        ]);

        $ticket->update(['status' => $data['status']]);

        return back()->with('status', 'Status tiket diperbarui.');
    }

    /** Opsional: download lampiran aman */
    public function download(TicketMessage $message)
    {
        $path = $message->meta['attachment_path'] ?? null;
        if (!$path) {
            abort(404);
        }
        return Storage::disk('public')->download($path);
    }
}