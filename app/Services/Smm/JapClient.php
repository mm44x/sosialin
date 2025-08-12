<?php

namespace App\Services\Smm;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;

class JapClient implements ProviderClientInterface
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey,
        protected ?int $providerId = null,
    ) {}

    protected function call(string $action, array $params = []): array
    {
        $payload = array_merge(['key' => $this->apiKey, 'action' => $action], $params);
        $start = microtime(true);

        try {
            $resp = Http::asForm()
                ->timeout(15)
                ->retry(2, 500)         // 2x retry, jeda 500ms
                ->post($this->baseUrl, $payload);

            $ms = (int) ((microtime(true) - $start) * 1000);

            // Siapkan body & info ukuran untuk menghindari max_allowed_packet
            $rawBody = $resp->body();
            $rawLen  = strlen($rawBody);
            $maxStore = 800000; // ~800 KB, aman untuk default MariaDB
            $storedBody = $rawBody;

            if ($rawLen > $maxStore) {
                $storedBody = substr($rawBody, 0, $maxStore) . "\n/* [TRUNCATED] original_bytes={$rawLen} */";
            }

            ApiLog::create([
                'provider_id' => $this->providerId,
                'endpoint'    => $action,
                'request'     => $payload,
                'status_code' => $resp->status(),
                'duration_ms' => $ms,
                'response'    => $storedBody,
            ]);

            $resp->throw();

            $json = $resp->json();
            if (!is_array($json)) {
                throw new \RuntimeException('Invalid JSON response from provider');
            }
            return $json;
        } catch (\Throwable $e) {
            // Log error tambahan jika perlu (di atas sudah tercatat)
            throw $e;
        }
    }

    public function services(array $params = []): array
    {
        // beberapa panel pakai 'services', sebagian 'get_services'
        return $this->call('services', $params);
    }

    public function addOrder(array $params): array
    {
        return $this->call('add', $params);
    }

    public function orderStatus(array $params): array
    {
        return $this->call('status', $params);
    }

    public function balance(): array
    {
        return $this->call('balance');
    }
}
