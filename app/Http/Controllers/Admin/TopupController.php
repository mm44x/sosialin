<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopupController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string)$request->input('q', ''));     // ref/id/email
        $status  = trim((string)$request->input('status', '')); // pending|approved|rejected
        $perPage = max(10, min(50, (int)$request->integer('per_page', 20)));

        $rows = Topup::query()
            ->with(['user:id,name,email'])
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    if (ctype_digit($q)) $w->orWhere('id', (int)$q);
                    $w->orWhere('reference', 'like', "%{$q}%")
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"));
                });
            })
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.topups.index', [
            'rows'    => $rows,
            'filters' => ['q' => $q, 'status' => $status, 'per_page' => $perPage],
        ]);
    }

    public function show(Topup $topup)
    {
        $topup->load(['user:id,name,email']);
        return view('admin.topups.show', compact('topup'));
    }

    public function approve(Request $request, Topup $topup)
    {
        if (!$topup->isPending()) {
            return back()->with('status', 'Top up sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $topup) {
            // Kreditkan saldo user
            app(WalletService::class)->credit($topup->user_id, (float)$topup->amount, 'topup', [
                'reason'     => 'manual_topup_approved',
                'topup_id'   => $topup->id,
                'reference'  => $topup->reference,
                'approved_by' => $request->user()->id,
            ]);

            // Update status
            $topup->update([
                'status'      => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'note'        => $request->string('note')->toString() ?: $topup->note,
            ]);
        });

        return redirect()->route('admin.topups.show', $topup)->with('status', 'Top up disetujui & saldo ditambahkan.');
    }

    public function reject(Request $request, Topup $topup)
    {
        if (!$topup->isPending()) {
            return back()->with('status', 'Top up sudah diproses sebelumnya.');
        }

        $data = $request->validate(['note' => ['nullable', 'string', 'max:2000']]);

        $topup->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'note'        => $data['note'] ?? $topup->note,
        ]);

        return redirect()->route('admin.topups.show', $topup)->with('status', 'Top up ditolak.');
    }
}
