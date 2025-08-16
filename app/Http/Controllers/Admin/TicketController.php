<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->input('q', ''));
        $status  = trim((string) $request->input('status', ''));
        $rows = Ticket::with(['user:id,name,email', 'order:id'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('tickets.subject', 'like', "%{$q}%")
                      ->orWhere('tickets.id', $q)
                      ->orWhere('tickets.order_id', $q)
                      ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('admin.tickets.index', [
            'rows'    => $rows,
            'filters' => ['q' => $q, 'status' => $status],
        ]);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user:id,name,email', 'order:id', 'messages.user:id,name,email']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        abort_if($ticket->status === 'closed', 422, 'Tiket sudah ditutup.');

        DB::transaction(function () use ($request, $ticket, $data) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $request->user()->id,
                'is_admin'  => true,
                'body'      => $data['message'],
            ]);
            $ticket->update(['last_message_at' => now()]);
        });

        return back()->with('status', 'Balasan terkirim.');
    }

    public function setStatus(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ]);

        $ticket->update(['status' => $data['status']]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $request->user()->id,
            'is_admin'  => true,
            'body'      => 'Status diubah menjadi: ' . $data['status'],
            'meta'      => ['system' => true],
        ]);

        return back()->with('status', 'Status tiket diperbarui.');
    }
}
