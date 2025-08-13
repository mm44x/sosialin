<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


class ApiLogController extends Controller
{
    public function index(Request $request)
    {
        // Ambil & parse rentang tanggal (aman, default: hari ini)
        $fromStr = $request->input('from');
        $toStr   = $request->input('to');
        try {
            $from = $fromStr ? Carbon::parse($fromStr) : Carbon::today();
        } catch (\Throwable) {
            $from = Carbon::today();
        }
        try {
            $to   = $toStr   ? Carbon::parse($toStr)   : Carbon::today();
        } catch (\Throwable) {
            $to   = Carbon::today();
        }
        if ($to->lessThan($from)) {
            [$from, $to] = [$to, $from];
        }

        $q = ApiLog::with('provider:id,name')
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when(
                $request->filled('provider_id'),
                fn($qq) =>
                $qq->where('provider_id', (int) $request->input('provider_id'))
            )
            ->when(
                $request->filled('endpoint'),
                fn($qq) =>
                $qq->where('endpoint', 'like', '%' . $request->string('endpoint')->toString() . '%')
            )
            ->when(
                $request->filled('status_code'),
                fn($qq) =>
                $qq->where('status_code', (int) $request->input('status_code'))
            )
            ->orderByDesc('id');

        $logs = $q->paginate(20)->withQueryString();

        return view('admin.api_logs.index', [
            'logs'      => $logs,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
            'filters'   => [
                'from'        => $from->toDateString(),
                'to'          => $to->toDateString(),
                'provider_id' => $request->input('provider_id'),
                'endpoint'    => $request->input('endpoint'),
                'status_code' => $request->input('status_code'),
            ],
        ]);
    }

    public function show(Request $request, \App\Models\ApiLog $log)
    {
        $log->load('provider:id,name');

        // Request sudah dicast array di model. Jika bukan array, coba decode.
        $reqData = $log->request;
        if (!is_array($reqData) && is_string($reqData)) {
            $decoded = json_decode($reqData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $reqData = $decoded;
            }
        }

        // Response bisa sangat besar. Siapkan versi parsed & raw + preview.
        $respRaw = (string) ($log->response ?? '');
        $respArr = json_decode($respRaw, true);
        $respIsJson = is_array($respArr);

        $limit = 10000; // 10KB preview
        $respPreview = mb_substr($respRaw, 0, $limit, 'UTF-8');
        $respTruncated = mb_strlen($respRaw, 'UTF-8') > $limit;

        return view('admin.api_logs.show', [
            'log'           => $log,
            'reqData'       => $reqData,
            'respRaw'       => $respRaw,
            'respArr'       => $respArr,
            'respIsJson'    => $respIsJson,
            'respPreview'   => $respPreview,
            'respTruncated' => $respTruncated,
        ]);
    }
}
