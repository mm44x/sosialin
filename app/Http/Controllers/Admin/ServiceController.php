<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $rows = Service::with(['provider', 'category'])
            ->when($request->filled('search'), function ($qq) use ($request) {
                $s = $request->string('search')->toString();
                $qq->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%{$s}%")
                        ->orWhere('public_name', 'like', "%{$s}%");
                });
            })
            ->when($request->filled('provider'), fn($qq) => $qq->where('provider_id', (int) $request->input('provider')))
            ->when($request->filled('category'), fn($qq) => $qq->where('category_id', (int) $request->input('category')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.index', [
            'rows'       => $rows,
            'providers'  => Provider::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'service'    => $service->load(['provider', 'category']),
            'providers'  => Provider::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            // internal
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'external_service_id' => ['required', 'string', 'max:100'],
            'category_id'         => ['required', 'exists:categories,id'],
            'provider_id'         => ['required', 'exists:providers,id'],
            'active'              => ['nullable', 'boolean'],

            // pricing override
            'markup_percent_override' => ['nullable', 'numeric', 'min:0', 'max:1000'],

            // publik
            'public_active'      => ['nullable', 'boolean'],
            'public_name'        => ['nullable', 'string', 'max:255'],
            'public_description' => ['nullable', 'string'],
        ]);

        // Normalisasi checkbox
        $data['active']         = $request->boolean('active');
        $data['public_active']  = $request->boolean('public_active');

        $service->update([
            'name'                    => $data['name'],
            'description'             => $data['description'] ?? null,
            'external_service_id'     => $data['external_service_id'],
            'category_id'             => (int) $data['category_id'],
            'provider_id'             => (int) $data['provider_id'],
            'active'                  => (bool) $data['active'],
            'markup_percent_override' => $data['markup_percent_override'] ?? null,
            'public_active'           => (bool) ($data['public_active'] ?? false),
            'public_name'             => $data['public_name'] ?? null,
            'public_description'      => $data['public_description'] ?? null,
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('status', "Service #{$service->id} diperbarui.");
    }
}
