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
        $q = Service::with(['provider', 'category'])
            ->when($request->search, fn($qq, $s) => $qq->where('name', 'like', "%$s%"))
            ->when($request->provider, fn($qq, $p) => $qq->where('provider_id', $p))
            ->when($request->category, fn($qq, $c) => $qq->where('category_id', $c))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.index', [
            'rows' => $q,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'service' => $service->load(['provider', 'category']),
            'providers' => \App\Models\Provider::orderBy('name')->get(['id', 'name']),
            'categories' => \App\Models\Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'external_service_id' => ['required', 'string', 'max:50'],
            'category_id' => ['required', 'exists:categories,id'],
            'provider_id' => ['required', 'exists:providers,id'],
            'active'  => ['nullable', 'boolean'],
            'markup_percent_override' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);

        $service->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'external_service_id' => $data['external_service_id'],
            'category_id' => $data['category_id'],
            'provider_id' => $data['provider_id'],
            'active' => (bool)($data['active'] ?? false),
            'markup_percent_override' => $data['markup_percent_override'] ?? null,
        ]);

        return redirect()->route('admin.services.index')
            ->with('status', "Service #{$service->id} diperbarui.");
    }
}
