<?php

use Webkul\ViaSocket\Support\IntegrationRegistry;

$items = [];

foreach (IntegrationRegistry::categories() as $index => $category) {
    $items[] = [
        'key' => 'settings.'.$category['key'],
        'name' => $category['name'],
        'info' => $category['info'],
        'route' => $category['apps'][0]['route'],
        'sort' => 10 + $index,
        'icon-class' => 'icon-settings',
    ];

    foreach ($category['apps'] as $position => $app) {
        $items[] = [
            'key' => 'settings.'.$category['key'].'.'.$app['slug'],
            'name' => $app['label'],
            'info' => 'Connect '.$app['label'],
            'route' => $app['route'],
            'sort' => $position + 1,
            'icon-class' => 'icon-settings-webhooks',
        ];
    }
}

return $items;
