<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Prunable;

class ApiLog extends Model
{
    use Prunable;

    protected $table = 'api_logs';

    protected $fillable = [
        'provider_id',
        'endpoint',
        'request',
        'status_code',
        'duration_ms',
        'response',
    ];

    protected $casts = [
        'request'     => 'array',   // otomatis JSON encode/decode jika array
        'status_code' => 'integer',
        'duration_ms' => 'integer',
        // 'response' sengaja dibiarkan string (raw) karena bisa non-JSON / sangat besar
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /** Pretty-print untuk request (dipakai di view) */
    public function getRequestPrettyAttribute(): string
    {
        if (is_array($this->request)) {
            return json_encode($this->request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        $decoded = json_decode((string) $this->request, true);
        return $decoded
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : (string) $this->request;
    }

    /** Pretty-print untuk response (dipakai di view) */
    public function getResponsePrettyAttribute(): string
    {
        $decoded = json_decode((string) $this->response, true);
        return $decoded
            ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : (string) $this->response;
    }

    /**
     * Hapus log yang berusia > 30 hari.
     * (Dipanggil oleh `php artisan model:prune` atau scheduler harian)
     */
    public function prunable()
    {
        return static::where('created_at', '<', now()->subDays(30));
    }

    /** Opsional: hook sebelum dihapus */
    public function pruning()
    {
        // Kosongkan bila tidak perlu (mis. hapus file terlampir jika ada).
    }
}
