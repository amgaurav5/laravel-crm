<?php

return [
    [
        'key' => 'integrations',
        'name' => 'viasocket::app.menu.title',
        'route' => [
            'admin.integrations.index',
            'admin.integrations.token',
            'admin.integrations.apps',
            'admin.integrations.app',
            'admin.integrations.schema',
            'admin.integrations.options',
            'admin.integrations.connections.index',
            'admin.integrations.connections.store',
            'admin.integrations.connections.destroy',
            'admin.integrations.flows.index',
            'admin.integrations.flows.store',
            'admin.integrations.flows.update',
            'admin.integrations.flows.status',
            'admin.integrations.flows.history',
            'admin.integrations.flows.destroy',
        ],
        'sort' => 7.5,
    ],
];
