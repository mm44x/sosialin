<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $rows = Category::with('provider:id,name')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where('name', 'like', "%{$s}%");
            })
            ->when($request->filled('provider'), function ($q) use ($request) {
                $p = $request->input('provider');
                if (is_numeric($p)) {
                    $q->where('provider_id', (int) $p);
                }
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', [
            'rows'      => $rows,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
            'filters'   => [
                'search'   => $request->input('search'),
                'provider' => $request->input('provider'),
            ],
        ]);
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', [
            'category' => $category->load('provider:id,name'),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name'   => $data['name'],
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('status', "Category #{$category->id} diperbarui.");
    }
}
