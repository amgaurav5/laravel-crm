<?php

return [
    /*
     * Credentials of the viaSocket embed. The module stays disabled until all three are set.
     */
    'org_id' => env('VIASOCKET_ORG_ID'),

    'project_id' => env('VIASOCKET_PROJECT_ID'),

    'embed_secret' => env('VIASOCKET_EMBED_SECRET'),

    'connect_script' => 'https://flow.viasocket.com/connect.js',

    'api_url' => 'https://flow-api.viasocket.com',

    'run_url' => 'https://flow.sokt.io/func',

    'catalog_url' => 'https://flow.sokt.io/func/scriK4LFg2kc',

    /*
     * The integrations listed on the Settings page, grouped by category: service id => label.
     */
    'categories' => [
        'Email' => [
            'rowo0bqrhj5g' => 'Gmail',
            'rowtuwzc2a57' => 'Microsoft Outlook',
            'row85xjphw1y' => 'MailChimp',
        ],

        'Communication' => [
            'rowbu58rc' => 'Slack',
            'rowxzmscatfe' => 'Microsoft Teams',
            'rowhh6qyg7v9' => 'Zoom',
        ],

        'Calendar & Scheduling' => [
            'rowkhibv5efp' => 'Google Calendar',
            'rowzc1pvmxyn' => 'Calendly',
        ],

        'Spreadsheets & Files' => [
            'rowqm5xi2' => 'Google Sheets',
            'row292jrurzk' => 'Google Drive',
            'rowk7lb9w' => 'Airtable',
        ],

        'CRM & Sales' => [
            'rowqfz6bv' => 'HubSpot',
            'row6dq7roqe6' => 'Salesforce',
            'row40nchn9gf' => 'Zoho CRM',
            'row5lelnpykr' => 'Pipedrive',
        ],

        'Productivity & Projects' => [
            'rowndtpfztyo' => 'Notion',
            'rowm0p081h7n' => 'Asana',
        ],

        'Forms' => [
            'rowshu8t54lr' => 'Google Forms',
        ],

        'Payments & Commerce' => [
            'rowr25lbhrq6' => 'Stripe',
            'rowyvmqz30q7' => 'Shopify',
        ],
    ],
];
