<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $rows = Ticket::with(['order:id', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('tickets.index', compact('rows'));
    }

    public function create(Request $request)
    {
        // Tidak ada dropdown/auto-list order. Hanya tampilkan form kosong.
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'message'  => ['required', 'string', 'max:5000'],
        ]);

        // Jika user mengisi order_id, pastikan order tersebut milik user
        if (!empty($data['order_id'])) {
            $own = Order::where('id', $data['order_id'])
                ->where('user_id', $request->user()->id)
                ->exists();
            abort_unless($own, 422, 'Order tidak valid atau bukan milik Anda.');
        }

        $ticket = null;

        DB::transaction(function () use ($request, $data, &$ticket) {
            $ticket = Ticket::create([
                'user_id'         => $request->user()->id,
                'subject'         => $data['subject'],
                'order_id'        => $data['order_id'] ?? null,
                'status'          => Ticket::STATUS_OPEN,
                'last_message_at' => now(),
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $request->user()->id,
                'is_admin'  => false,
                'body'      => $data['message'],
            ]);
        });

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Tiket dibuat. Tim kami akan menindaklanjuti.');
    }

    public function show(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);
        $ticket->load(['order', 'messages.user:id,name']);
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);
        abort_if(!$ticket->isOpen(), 422, 'Tiket sudah ditutup.');

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($request, $ticket, $data) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $request->user()->id,
                'is_admin'  => false,
                'body'      => $data['message'],
            ]);

            $ticket->update(['last_message_at' => now()]);
        });

        return back()->with('status', 'Balasan terkirim.');
    }

    public function close(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== $request->user()->id, 403);

        $ticket->update(['status' => Ticket::STATUS_CLOSED]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => null,
            'is_admin'  => false,
            'body'      => 'Ticket ditutup oleh pemilik.',
            'meta'      => ['system' => true],
        ]);

        return redirect()->route('tickets.index')->with('status', 'Tiket ditutup.');
    }
}