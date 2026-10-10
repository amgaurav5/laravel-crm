<?php

namespace Webkul\ViaSocket\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class CatalogService
{
    /**
     * An app's name and icon.
     */
    public function find(string $serviceId): ?array
    {
        if (! $catalog = $this->catalog($serviceId)) {
            return null;
        }

        return [
            'name' => $catalog['service']['name'],
            'icon' => $catalog['service']['icon_url'] ?? null,
        ];
    }

    /**
     * The actions an app offers.
     */
    public function actions(string $serviceId): array
    {
        return collect($this->catalog($serviceId)['actions'] ?? [])
            ->map(fn (array $action) => [
                'id' => $action['action_version_id'],
                'name' => $action['name'],
                'description' => $action['description'] ?? null,
            ])
            ->values()
            ->all();
    }

    /**
     * The input fields of one action, or null when the app has no such action.
     */
    public function schema(string $serviceId, string $actionVersionId): ?array
    {
        foreach ($this->catalog($serviceId)['actions'] ?? [] as $action) {
            if ($action['action_version_id'] === $actionVersionId) {
                return $action['input_schema'] ?? ['steps' => ['root' => []], 'blocks' => []];
            }
        }

        return null;
    }

    /**
     * Read one app from the catalog and cache it for a day.
     */
    protected function catalog(string $serviceId): ?array
    {
        $key = 'viasocket.catalog.v2.'.$serviceId;

        if ($catalog = Cache::get($key)) {
            return $catalog;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->post(config('viasocket.catalog_url'), ['service_id' => $serviceId]);
        } catch (Throwable) {
            return null;
        }

        if (! $response->successful() || ! $response->json('service.name')) {
            return null;
        }

        $catalog = $response->json();

        Cache::put($key, $catalog, now()->addDay());

        return $catalog;
    }
}
