<?php

namespace Webkul\ViaSocket\Support;

use Illuminate\Support\Str;

class IntegrationRegistry
{
    /**
     * The configured categories with their apps, ready for menu, ACL and route generation.
     */
    public static function categories(): array
    {
        $categories = [];

        foreach (config('viasocket.categories', []) as $name => $services) {
            $apps = [];

            foreach ($services as $serviceId => $label) {
                $slug = Str::slug($label);

                $apps[] = [
                    'service_id' => $serviceId,
                    'label' => $label,
                    'slug' => $slug,
                    'route' => 'admin.settings.integrations.'.$slug,
                ];
            }

            $categories[] = [
                'key' => 'integrations_'.Str::slug($name, '_'),
                'name' => $name,
                'info' => $name.' apps you can connect to Krayin',
                'apps' => $apps,
            ];
        }

        return $categories;
    }

    /**
     * Find one configured app by its service id.
     */
    public static function find(string $serviceId): ?array
    {
        foreach (static::categories() as $category) {
            foreach ($category['apps'] as $app) {
                if ($app['service_id'] === $serviceId) {
                    return $app + ['category' => $category['name']];
                }
            }
        }

        return null;
    }
}
