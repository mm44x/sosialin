<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $rows = Provider::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where('name', 'like', "%{$s}%");
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.providers.index', [
            'rows'   => $rows,
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function edit(Provider $provider)
    {
        return view('admin.providers.edit', [
            'provider' => $provider,
        ]);
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['nullable', 'string', 'max:50'],
            'base_url'       => ['required', 'url', 'max:255'],
            'markup_percent' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'active'         => ['nullable', 'boolean'],
            // api_key opsional: jika kosong, tidak diubah
            'api_key'        => ['nullable', 'string', 'max:255'],
        ]);

        $update = [
            'name'           => $data['name'],
            'type'           => $data['type'] ?? $provider->type,
            'base_url'       => $data['base_url'],
            'markup_percent' => $data['markup_percent'] ?? 0,
            'active'         => $request->boolean('active'),
        ];

        if ($request->filled('api_key')) {
            $update['api_key'] = $data['api_key']; // ganti hanya jika diisi
        }

        $provider->update($update);

        return redirect()
            ->route('admin.providers.index')
            ->with('status', "Provider #{$provider->id} diperbarui.");
    }

    public function revealKey(Request $request, \App\Models\Provider $provider)
    {
        // Middleware 'admin' sudah melindungi, tapi kita double-check role bila perlu:
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        // Kembalikan API key apa adanya (kita belum mengenkripsi kolom ini).
        // Jika nanti Anda mengenkripsi, decrypt di sini sebelum return.
        $key = (string) ($provider->api_key ?? '');

        // Log audit ringan (opsional)
        Log::info('ADMIN_REVEAL_API_KEY', [
            'admin_id'    => $request->user()->id,
            'provider_id' => $provider->id,
            'ip'          => $request->ip(),
        ]);

        return response()->json(['api_key' => $key]);
    }
}
