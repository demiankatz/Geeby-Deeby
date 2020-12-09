<?php
return [
    'router' => [
        'routes' => [
            'legacy' => [
                'type'    => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/:filename',
                    'constraints' => [
                        'filename' => '.*(.cgi|.html?|.php)',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeebyLegacy\Controller',
                        'controller'    => 'Legacy',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'aliases' => [
            'GeebyDeebyLegacy\Controller\Legacy' => 'GeebyDeebyLegacy\Controller\LegacyController',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'geeby-deeby-legacy/legacy/notfound' => __DIR__ . '/../view/geeby-deeby-legacy/legacy/notfound.phtml',
        ],
    ],
];
