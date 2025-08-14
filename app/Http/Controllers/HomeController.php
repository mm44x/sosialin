<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home');
    }

    /**
     * Halaman daftar layanan publik dengan:
     * - Search: ?q= atau ?search=
     * - Filter kategori: ?category= atau ?category_id=
     * - Sort: ?sort=price_asc|price_desc|name|name_asc|name_desc|rate_asc|rate_desc
     * Catatan: Sorting harga memakai rate USD + markup (SQL), tampilan harga memakai PricingService (lokal).
     */
    public function services(Request $request)
    {
        // Normalisasi input agar kompatibel dgn view lama/baru
        $keyword  = trim((string) ($request->input('q', $request->input('search', ''))));
        $catIdVal = $request->input('category', $request->input('category_id', ''));
        $catId    = is_numeric($catIdVal) ? (int) $catIdVal : 0;
        $sort     = (string) $request->input('sort', 'name'); // default: name (A–Z)
        $perPage  = (int) $request->integer('per_page', 20);
        $perPage  = max(5, min(50, $perPage));

        // Query dasar: hanya layanan & provider aktif, dan hanya yang public_active
        $base = Service::query()
            ->with(['category:id,name'])
            ->leftJoin('providers', 'providers.id', '=', 'services.provider_id')
            ->where('services.active', true)
            ->where('services.public_active', true)
            ->where(function ($q) {
                // provider harus aktif; jika provider_id null (harusnya tidak), tetap aman ditahan
                $q->whereNotNull('services.provider_id')
                    ->where('providers.active', true);
            });

        // Pencarian: utamakan public_name, fallback name & public_description
        if ($keyword !== '') {
            $base->where(function ($w) use ($keyword) {
                $w->where('services.public_name', 'like', "%{$keyword}%")
                    ->orWhere('services.name', 'like', "%{$keyword}%")
                    ->orWhere('services.public_description', 'like', "%{$keyword}%");
            });
        }

        // Filter kategori (opsional)
        if ($catId > 0) {
            $base->where('services.category_id', $catId);
        }

        // Field bantu untuk sort harga: rate USD + markup (tanpa multiplier lokal)
        $base->select('services.*')->addSelect(DB::raw(
            '(services.rate * (1 + COALESCE(services.markup_percent_override, providers.markup_percent, 0)/100)) as rate_usd_with_markup'
        ));

        // Sorting
        switch ($sort) {
            case 'price_asc':
            case 'rate_asc':
                $base->orderBy('rate_usd_with_markup', 'asc');
                break;
            case 'price_desc':
            case 'rate_desc':
                $base->orderBy('rate_usd_with_markup', 'desc');
                break;
            case 'name_desc':
                $base->orderByRaw('COALESCE(services.public_name, services.name) desc');
                break;
            case 'name':
            case 'name_asc':
            default:
                $base->orderByRaw('COALESCE(services.public_name, services.name) asc');
                break;
        }

        $rows = $base->paginate($perPage)->withQueryString();

        // Hitung harga tampil (lokal) via PricingService — aman untuk publik
        $ps = app(\App\Services\PricingService::class);
        foreach ($rows as $svc) {
            $bd = $ps->breakdown($svc, max((int) $svc->min, 100)); // qty kecil utk konsistensi per 1000
            // properti non-persisten untuk view
            $svc->display_rate_per_thousand = $bd['ratePerThousandLocal'];
        }

        // Dropdown kategori
        $categories = Category::orderBy('name')->get(['id', 'name']);

        // Untuk view versi baru (selected) & lama (filters)
        $selected = [
            'q'        => $keyword,
            'category' => $catId,
            'sort'     => $sort,
            'per_page' => $perPage,
        ];

        return view('public.services', [
            'rows'       => $rows,
            'categories' => $categories,
            'selected'   => $selected,
            // Kompat untuk view lama jika masih dipakai sebagian
            'filters'    => [
                'search'      => $keyword,
                'category_id' => $catId,
                'sort'        => $sort,
                'per_page'    => $perPage,
            ],
        ]);
    }
}
