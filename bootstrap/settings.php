<?php
return [
    'settings' => [

        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'events_upload' => __DIR__.'/../public/uploads/evenements/',
        'trials_upload' => __DIR__.'/../public/uploads/epreuves/',

        'view' => [
            'template_path' => __DIR__ . '/../src/App/Resources/views',
            'twig' => [
                'cache' => __DIR__ . '/../cache',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        'routes' => [
            'dir' => __DIR__ . '/../src/App/Resources/routes',
            'files' => [
                'app',
                'auth',
                'evenement',
                'user',
                'epreuve'
            ]
        ],

    ],
];