<?php

use Webkul\ViaSocket\Support\IntegrationRegistry;

$shared = [
    'admin.settings.integrations.token',
    'admin.settings.integrations.connections.store',
    'admin.settings.integrations.connections.destroy',
    'admin.settings.integrations.api.actions',
    'admin.settings.integrations.api.schema',
    'admin.settings.integrations.api.options',
    'admin.settings.integrations.api.run',
];

$items = [];

foreach (IntegrationRegistry::categories() as $index => $category) {
    $items[] = [
        'key' => 'settings.'.$category['key'],
        'name' => $category['name'],
        'route' => array_column($category['apps'], 'route'),
        'sort' => 10 + $index,
    ];

    foreach ($category['apps'] as $position => $app) {
        $items[] = [
            'key' => 'settings.'.$category['key'].'.'.$app['slug'],
            'name' => $app['label'],
            'route' => array_merge([$app['route']], $shared),
            'sort' => $position + 1,
        ];
    }
}

return $items;
