<?php

namespace App\Services;

use App\Models\Service;

class PricingService
{
    /**
     * Hitung komponen harga untuk 1 layanan.
     *
     * Rumus ringkas:
     *   baseRateUSD (per 1000, dari provider)
     * × (1 + usedMarkup/100)
     * × FX_USD_IDR
     * × BILLING_MULTIPLIER
     * = ratePerThousandLocal
     *
     * cost = max(BILLING_MIN, ratePerThousandLocal × qty/1000), dibulatkan 2 desimal.
     *
     * Catatan:
     * - usedMarkup = markup_percent_override (jika ada) else provider.markup_percent.
     * - Jangan tampilkan provider/markup ke publik; controller/view publik cukup pakai
     *   'ratePerThousandLocal' dan 'cost'.
     *
     * @return array{
     *   baseRateUSD: float,
     *   providerMarkup: float,
     *   serviceMarkup: float|null,
     *   usedMarkup: float,
     *   rateUSDwithMarkup: float,
     *   ratePerThousandLocal: float,
     *   fx: float,
     *   mult: float,
     *   min: float,
     *   qty: int,
     *   costRaw: float,
     *   cost: float
     * }
     */
    public function breakdown(Service $service, int $qty): array
    {
        // 1) Rate dasar dari provider (USD per 1000)
        $baseRateUSD = (float) $service->rate;

        // 2) Tentukan markup yang dipakai
        $provMarkup = (float) ($service->provider->markup_percent ?? 0.0);
        $svcMarkup  = $service->markup_percent_override !== null
            ? (float) $service->markup_percent_override
            : null;
        $usedMarkup = $svcMarkup ?? $provMarkup;

        // 3) Terapkan markup (masih dalam USD)
        $rateUSDwithMarkup = $baseRateUSD * (1 + ($usedMarkup / 100));

        // 4) Faktor konversi & multiplier lokal
        //    - FX_USD_IDR: konversi USD→IDR (default 1 agar tidak “mengejutkan” bila belum di-set)
        //    - BILLING_MULTIPLIER: faktor bisnis tambahan (fee, pembulatan, dll)
        $fx   = (float) env('FX_USD_IDR', 1);
        $mult = (float) env('BILLING_MULTIPLIER', 1);
        $min  = (float) env('BILLING_MIN', 0.01);

        $localFactor = $fx * $mult;

        // 5) Harga lokal per 1000 dan total biaya untuk qty
        $ratePerThousandLocal = $rateUSDwithMarkup * $localFactor;

        $qty = max(0, (int) $qty); // jaga-jaga
        $costRaw = $ratePerThousandLocal * ($qty / 1000);
        $cost    = round($costRaw, 2);
        if ($cost < $min) {
            $cost = $min;
        }

        return [
            'baseRateUSD'          => round($baseRateUSD, 6),
            'providerMarkup'       => round($provMarkup, 4),
            'serviceMarkup'        => $svcMarkup !== null ? round($svcMarkup, 4) : null,
            'usedMarkup'           => round($usedMarkup, 4),
            'rateUSDwithMarkup'    => round($rateUSDwithMarkup, 6),
            'ratePerThousandLocal' => round($ratePerThousandLocal, 2),
            'fx'                   => $fx,
            'mult'                 => $mult,
            'min'                  => $min,
            'qty'                  => $qty,
            'costRaw'              => $costRaw,
            'cost'                 => $cost,
        ];
    }
}
