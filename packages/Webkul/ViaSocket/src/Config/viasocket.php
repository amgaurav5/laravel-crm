<?php

return [
    /*
     * Credentials of the viaSocket embed. The module stays disabled until all three are set.
     */
    'org_id' => env('VIASOCKET_ORG_ID'),

    'project_id' => env('VIASOCKET_PROJECT_ID'),

    'embed_secret' => env('VIASOCKET_EMBED_SECRET'),

    'embed_script' => 'https://embed.viasocket.com/prod-embedcomponent.js',

    'embed_origin' => 'https://embedfrontend.viasocket.com',
];
