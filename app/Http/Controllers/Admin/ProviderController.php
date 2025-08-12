<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::orderBy('name')->get();
        return view('admin.providers.index', compact('providers'));
    }

    public function edit(Provider $provider)
    {
        return view('admin.providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate([
            'markup_percent' => ['required', 'numeric', 'min:0', 'max:1000'],
            'active'         => ['nullable', 'boolean'],
            // opsional:
            // 'base_url'    => ['required','url'],
        ]);

        $provider->update([
            'markup_percent' => (float) $data['markup_percent'],
            'active'         => (bool) ($data['active'] ?? false),
            // 'base_url'    => $request->input('base_url'), // jika ingin diedit
        ]);

        return redirect()
            ->route('admin.providers.index')
            ->with('status', "Provider {$provider->name} diperbarui.");
    }
}
