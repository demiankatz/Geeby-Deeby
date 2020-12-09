<?php
return [
    'geeby-deeby' => [
        'siteTitle' => 'Demian\'s Gamebook Web Page',
        'siteEmail' => 'demiankatz@gmail.com',
        'siteOwner' => 'Demian Katz',
    ],
    'controllers' => [
        'aliases' => [
            'GeebyDeeby\Controller\About' => 'GeebyDeebyLocal\Controller\AboutController',
        ],
    ],
    'router' => [
        'routes' => [
            'thanks' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/Thanks',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'thanks',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_exceptions' => false,
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
