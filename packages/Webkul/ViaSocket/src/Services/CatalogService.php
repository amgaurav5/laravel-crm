<?php

namespace Webkul\ViaSocket\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class CatalogService
{
    /**
     * Read an app's name and icon from the viaSocket catalog, cached for a day.
     */
    public function find(string $serviceId): ?array
    {
        $key = 'viasocket.catalog.'.$serviceId;

        if ($app = Cache::get($key)) {
            return $app;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post(config('viasocket.catalog_url'), ['service_id' => $serviceId]);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful() || ! $response->json('service.name')) {
            return null;
        }

        $app = [
            'name' => $response->json('service.name'),
            'icon' => $response->json('service.icon_url'),
        ];

        Cache::put($key, $app, now()->addDay());

        return $app;
    }
}
