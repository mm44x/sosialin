<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Provider;
use Illuminate\Http\Request;

class ApiLogController extends Controller
{
    public function index(Request $request)
    {
        $q = ApiLog::query()->with('provider');

        if ($request->filled('provider_id')) {
            $q->where('provider_id', (int) $request->input('provider_id'));
        }
        if ($request->filled('endpoint')) {
            $q->where('endpoint', $request->string('endpoint'));
        }
        if ($request->filled('status_code')) {
            $q->where('status_code', (int) $request->input('status_code'));
        }
        if ($request->filled('date_from')) {
            $q->where('created_at', '>=', $request->date('date_from')->startOfDay());
        }
        if ($request->filled('date_to')) {
            $q->where('created_at', '<=', $request->date('date_to')->endOfDay());
        }

        $logs = $q->orderByDesc('id')->paginate(25)->withQueryString();

        return view('admin.api_logs.index', [
            'logs' => $logs,
            'providers' => Provider::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only(['provider_id', 'endpoint', 'status_code', 'date_from', 'date_to']),
        ]);
    }

    public function show(ApiLog $api_log)
    {
        // Tampilkan detail satu log (request/response JSON pretty)
        return view('admin.api_logs.show', ['log' => $api_log->load('provider')]);
    }
}
