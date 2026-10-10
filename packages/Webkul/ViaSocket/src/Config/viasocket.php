<?php

return [
    /*
     * Credentials of the embed. The module stays disabled until all three are set.
     */
    'org_id' => env('VIASOCKET_ORG_ID'),

    'project_id' => env('VIASOCKET_PROJECT_ID'),

    'embed_secret' => env('VIASOCKET_EMBED_SECRET'),

    'connect_script' => 'https://flow.viasocket.com/connect.js',

    'api_url' => 'https://flow-api.viasocket.com',

    'flow_url' => 'https://flow.sokt.io/func',

    'catalog_url' => 'https://flow.sokt.io/func/scriK4LFg2kc',

    'search_url' => 'https://flow.sokt.io/func/scri12BSufQM',

    'apps_url' => 'https://plug-service.viasocket.com/api/v1/plugins/all',

    'scripts' => [
        'create_flow' => 'scrisgf5H0hN',
        'set_trigger' => 'scrij2DSRMlP',
        'save_steps' => 'scrigkyMNOEC',
    ],
];
