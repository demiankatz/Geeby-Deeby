<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'geeby-deeby' => [
        'siteTitle' => 'The Edward T. LeBlanc Memorial Dime Novel Bibliography',
        'siteEmail' => 'demian.katz@villanova.edu',
        'siteOwner' => 'Demian Katz',
        'groupSeriesByMaterialType' => false,
    ],
    'controllers' => [
        'aliases' => [
            'GeebyDeeby\Controller\About' => 'GeebyDeebyLocal\Controller\AboutController',
            'GeebyDeeby\Controller\Edition' => 'GeebyDeebyLocal\Controller\EditionController',
            'GeebyDeeby\Controller\Ingest' => 'GeebyDeebyLocal\Controller\IngestController',
            'GeebyDeeby\Controller\Item' => 'GeebyDeebyLocal\Controller\ItemController',
            'GeebyDeeby\Controller\Index' => 'GeebyDeebyLocal\Controller\IndexController',
            'GeebyDeeby\Controller\Ontology' => 'GeebyDeebyLocal\Controller\OntologyController',
            'GeebyDeeby\Controller\Person' => 'GeebyDeebyLocal\Controller\PersonController',
            'GeebyDeeby\Controller\Podcast' => 'GeebyDeebyLocal\Controller\PodcastController',
            'GeebyDeeby\Controller\Publisher' => 'GeebyDeebyLocal\Controller\PublisherController',
            'GeebyDeeby\Controller\Series' => 'GeebyDeebyLocal\Controller\SeriesController',
        ],
    ],
    'console' => [
        'router' => [
            'routes' => [
                'check-links' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'check links <series> <provider>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'checklinks',
                        ]
                    ]
                ],
                'harvest-collection' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'harvest collection <collection> <series> <dir>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvestcollection',
                        ]
                    ]
                ],
                'harvest-existing' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'harvest existing <dir>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvestexisting',
                        ]
                    ]
                ],
                'harvest-series' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'harvest series <series> <dir>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvestseries',
                        ]
                    ]
                ],
                'harvest-tiffs' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'harvest tiffs <pid> <dir>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvesttiffs',
                        ]
                    ]
                ],
                'ingest-directory' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'ingest directory <dir>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'directory',
                        ]
                    ]
                ],
                'ingest-spreadsheet' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'ingest spreadsheet <file>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'spreadsheet',
                        ]
                    ]
                ],
                'ingest-existing' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'ingest [existing]',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'existing',
                        ]
                    ]
                ],
                'ingest-images' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'load iiif',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'loadiiif',
                        ]
                    ]
                ],
                'ingest-makeissues' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'makeissues <series> <prefix>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'makeissues',
                        ]
                    ]
                ],
                'ingest-series' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'ingest series <series>',
                        'defaults' => [
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'series',
                        ]
                    ]
                ],
            ]
        ]
    ],
    'controller_plugins' => [
        'invokables' => [
            'podcast' => 'GeebyDeebyLocal\Controller\Plugin\Podcast',
        ]
    ],
    'router' => [
        'routes' => [
            'about' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/About',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'index',
                    ],
                ],
            ],
            'about-credits' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/About/Credits',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'credits',
                    ],
                ],
            ],
            'about-progress' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/About/Progress',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'progress',
                    ],
                ],
            ],
            'ontology' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/ontology[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Ontology',
                        'action'     => 'index',
                    ],
                ],
            ],
            'podcast' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'index',
                    ],
                ],
            ],
            'podcast-about' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/About[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'about',
                    ],
                ],
            ],
            'podcast-mitties' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/Mittie[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'mittie',
                    ],
                ],
            ],
            'podcast-professor' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/Professor[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'professor',
                    ],
                ],
            ],
            'podcast-rss' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/RSS[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'rss',
                    ],
                ],
            ],
            'podcast-rss-lowercase-for-apple' => [
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => [
                    'route'    => '/podcast/rss[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'rss',
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
