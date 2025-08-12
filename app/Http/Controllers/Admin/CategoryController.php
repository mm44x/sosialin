<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Provider;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $rows = Category::with('provider')->orderBy('name')->paginate(20);
        return view('admin.categories.index', compact('rows'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'provider_id' => $data['provider_id'] ?? null,
            'active' => (bool)($data['active'] ?? false),
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori diperbarui.');
    }
}
