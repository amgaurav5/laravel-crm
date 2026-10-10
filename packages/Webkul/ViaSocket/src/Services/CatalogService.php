<?php

namespace Webkul\ViaSocket\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class CatalogService
{
    /**
     * The apps to pick from: the most used ones, or the best matches for a search.
     */
    public function apps(?string $search = null): array
    {
        $search = trim((string) $search);

        $key = 'viasocket.apps.'.($search === '' ? 'top' : md5(mb_strtolower($search)));

        return Cache::remember($key, now()->addHour(), function () use ($search) {
            try {
                $response = $search === ''
                    ? Http::timeout(15)->get(config('viasocket.apps_url'), ['limit' => 60, 'offset' => 0])
                    : Http::timeout(15)->get(config('viasocket.search_url'), ['key' => $search]);
            } catch (Throwable) {
                return [];
            }

            return collect($response->json('data', []))
                ->map(fn (array $app) => [
                    'id' => $app['rowid'] ?? $app['service_id'] ?? null,
                    'name' => $app['name'] ?? '',
                    'icon' => $app['iconurl'] ?? null,
                    'description' => $app['description'] ?? null,
                ])
                ->filter(fn (array $app) => $app['id'] && $app['name'])
                ->values()
                ->all();
        });
    }

    /**
     * One app with its actions and triggers (without their input fields).
     */
    public function app(string $serviceId): ?array
    {
        if (! $catalog = $this->catalog($serviceId)) {
            return null;
        }

        return [
            'id' => $serviceId,
            'name' => $catalog['service']['name'],
            'icon' => $catalog['service']['icon_url'] ?? null,
            'requires_auth' => (bool) ($catalog['service']['requires_auth'] ?? true),
            'actions' => collect($catalog['actions'] ?? [])->map(fn (array $action) => [
                'id' => $action['action_id'],
                'version' => $action['action_version_id'],
                'name' => $action['name'],
                'description' => $action['description'] ?? null,
            ])->values()->all(),
            'triggers' => collect($catalog['triggers'] ?? [])->map(fn (array $trigger) => [
                'id' => $trigger['trigger_id'],
                'version' => $trigger['trigger_version_id'],
                'name' => $trigger['name'],
                'description' => $trigger['description'] ?? null,
                'sample' => $trigger['sample_output'] ?? null,
            ])->values()->all(),
        ];
    }

    /**
     * The input fields of an action or trigger, found by its version id.
     */
    public function schema(string $serviceId, string $kind, string $versionId): ?array
    {
        $versionKey = $kind === 'triggers' ? 'trigger_version_id' : 'action_version_id';

        foreach ($this->catalog($serviceId)[$kind] ?? [] as $item) {
            if (($item[$versionKey] ?? null) === $versionId) {
                return $item['input_schema'] ?? ['steps' => ['root' => []], 'blocks' => []];
            }
        }

        return null;
    }

    /**
     * Whether a version id belongs to any action or trigger of the app.
     */
    public function hasVersion(string $serviceId, string $versionId): bool
    {
        return $this->schema($serviceId, 'actions', $versionId) !== null
            || $this->schema($serviceId, 'triggers', $versionId) !== null;
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
