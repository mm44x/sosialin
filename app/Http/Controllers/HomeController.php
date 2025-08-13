<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home');
    }

    public function services(\Illuminate\Http\Request $request)
    {
        $q = \App\Models\Service::query()
            ->with(['category:id,name'])
            ->where('active', true)
            ->where('public_active', true)
            ->whereHas('provider', fn($qq) => $qq->where('active', true))
            ->when($request->filled('search'), function ($qq) use ($request) {
                $s = $request->string('search')->toString();
                $qq->where(function ($w) use ($s) {
                    $w->where('name', 'like', "%{$s}%")
                        ->orWhere('public_name', 'like', "%{$s}%");
                });
            })
            ->when(
                $request->filled('category_id'),
                fn($qq) =>
                $qq->where('category_id', (int) $request->input('category_id'))
            );

        // Sorting aman (tanpa expose provider)
        $sort = $request->string('sort', 'name_asc')->toString();
        $q = match ($sort) {
            'name_desc' => $q->orderBy('name', 'desc'),
            'rate_asc'  => $q->orderBy('rate', 'asc'),
            'rate_desc' => $q->orderBy('rate', 'desc'),
            default     => $q->orderBy('name', 'asc'),
        };

        $perPage = (int) $request->integer('per_page', 20);
        $perPage = max(5, min(50, $perPage));

        $rows = $q->paginate($perPage)->withQueryString();

        // Hitung harga jual/1000 untuk item halaman ini
        $pricing = app(\App\Services\PricingService::class);
        $computed = [];
        foreach ($rows as $svc) {
            $bd = $pricing->breakdown($svc, 1000);
            $computed[$svc->id] = [
                'ratePerThousand' => $bd['ratePerThousandLocal'],
            ];
        }

        $categories = \App\Models\Category::where('active', true)->orderBy('name')->get(['id', 'name']);

        return view('public.services', [
            'rows'       => $rows,
            'categories' => $categories,
            'computed'   => $computed,
            'filters'    => $request->only(['search', 'category_id', 'sort', 'per_page']),
        ]);
    }
}
