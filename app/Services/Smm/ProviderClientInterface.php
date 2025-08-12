<?php

namespace App\Services\Smm;

interface ProviderClientInterface
{
    public function services(array $params = []): array;
    public function addOrder(array $params): array;
    public function orderStatus(array $params): array;
    public function balance(): array;
}
