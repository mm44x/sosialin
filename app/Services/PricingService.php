<?php

namespace App\Services;

use App\Models\Service;

class PricingService
{
    public function breakdown(Service $service, int $qty): array
    {
        $baseRateUSD = (float) $service->rate; // per 1000
        $provMarkup  = (float) ($service->provider->markup_percent ?? 0);
        $svcMarkup   = $service->markup_percent_override !== null
            ? (float) $service->markup_percent_override
            : null;

        $usedMarkup = $svcMarkup ?? $provMarkup; // override jika ada

        $rateUSDwithMarkup = $baseRateUSD * (1 + ($usedMarkup / 100));

        $mult = (float) env('BILLING_MULTIPLIER', 1);
        $min  = (float) env('BILLING_MIN', 0.01);

        $ratePerThousandLocal = $rateUSDwithMarkup * $mult;
        $costRaw = $ratePerThousandLocal * ($qty / 1000);
        $cost = round($costRaw, 2);
        if ($cost < $min) $cost = $min;

        return [
            'baseRateUSD'          => $baseRateUSD,
            'providerMarkup'       => $provMarkup,
            'serviceMarkup'        => $svcMarkup,
            'usedMarkup'           => $usedMarkup,
            'rateUSDwithMarkup'    => $rateUSDwithMarkup,
            'ratePerThousandLocal' => round($ratePerThousandLocal, 2),
            'mult'                 => $mult,
            'min'                  => $min,
            'qty'                  => $qty,
            'costRaw'              => $costRaw,
            'cost'                 => $cost,
        ];
    }
}
