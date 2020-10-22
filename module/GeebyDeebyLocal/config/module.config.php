<?php
return [
    'geeby-deeby' => [
        'siteTitle' => 'The Edward T. LeBlanc Memorial Dime Novel Bibliography',
        'siteEmail' => 'demian.katz@villanova.edu',
        'siteOwner' => 'Demian Katz',
        'groupSeriesByMaterialType' => false,
        'activity_log_dir' => '/opt/gbdb/data/logs/',
        'plugin_managers' => [
            'command' => [
                'aliases' => [
                    'harvest/collection' => 'GeebyDeebyLocal\Command\Harvest\CollectionCommand',
                    'harvest/existing' => 'GeebyDeebyLocal\Command\Harvest\ExistingCommand',
                    'harvest/series' => 'GeebyDeebyLocal\Command\Harvest\SeriesCommand',
                    'harvest/tiffs' => 'GeebyDeebyLocal\Command\Harvest\TIFFsCommand',
                    'ingest/directory' => 'GeebyDeebyLocal\Command\Ingest\DirectoryCommand',
                    'ingest/iiif' => 'GeebyDeebyLocal\Command\Ingest\IIIFCommand',
                    'ingest/spreadsheet' => 'GeebyDeebyLocal\Command\Ingest\SpreadsheetCommand',
                    'make/issues' => 'GeebyDeebyLocal\Command\Make\IssuesCommand',
                ],
                'factories' => [
                    'GeebyDeebyLocal\Command\Harvest\CollectionCommand' => 'GeebyDeebyLocal\Command\Harvest\SeriesCommandFactory',
                    'GeebyDeebyLocal\Command\Harvest\ExistingCommand' => 'GeebyDeebyLocal\Command\Harvest\ExistingCommandFactory',
                    'GeebyDeebyLocal\Command\Harvest\SeriesCommand' => 'GeebyDeebyLocal\Command\Harvest\SeriesCommandFactory',
                    'GeebyDeebyLocal\Command\Harvest\TIFFsCommand' => 'GeebyDeebyLocal\Command\Harvest\TIFFsCommandFactory',
                    'GeebyDeebyLocal\Command\Ingest\DirectoryCommand' => 'GeebyDeebyLocal\Command\Ingest\DirectoryCommandFactory',
                    'GeebyDeebyLocal\Command\Ingest\IIIFCommand' => 'GeebyDeebyLocal\Command\Ingest\IIIFCommandFactory',
                    'GeebyDeebyLocal\Command\Ingest\SpreadsheetCommand' => 'GeebyDeebyLocal\Command\Ingest\SpreadsheetCommandFactory',
                    'GeebyDeebyLocal\Command\Make\IssuesCommand' => 'GeebyDeebyLocal\Command\Make\IssuesCommandFactory',
                ]
            ],
        ],
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
            ]
        ]
    ],
    'controller_plugins' => [
        'aliases' => [
            'podcast' => 'GeebyDeebyLocal\Controller\Plugin\Podcast',
        ],
        'factories' => [
            'GeebyDeebyLocal\Controller\Plugin\Podcast' => 'Laminas\ServiceManager\Factory\InvokableFactory',
        ]
    ],
    'router' => [
        'routes' => [
            'about' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/About',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'index',
                    ],
                ],
            ],
            'about-credits' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/About/Credits',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'credits',
                    ],
                ],
            ],
            'about-progress' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/About/Progress',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'progress',
                    ],
                ],
            ],
            'ontology' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/ontology[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Ontology',
                        'action'     => 'index',
                    ],
                ],
            ],
            'podcast' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'index',
                    ],
                ],
            ],
            'podcast-about' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/About[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'about',
                    ],
                ],
            ],
            'podcast-mitties' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/Mittie[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'mittie',
                    ],
                ],
            ],
            'podcast-professor' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/Professor[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'professor',
                    ],
                ],
            ],
            'podcast-rss' => [
                'type' => 'Laminas\Router\Http\Segment',
                'options' => [
                    'route'    => '/Podcast/RSS[/]',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'rss',
                    ],
                ],
            ],
            'podcast-rss-lowercase-for-apple' => [
                'type' => 'Laminas\Router\Http\Segment',
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
    'service_manager' => [
        'factories' => [
            'GeebyDeebyLocal\Ingest\DatabaseIngester' => 'GeebyDeebyLocal\Ingest\DatabaseIngesterFactory',
            'GeebyDeebyLocal\Ingest\FedoraHarvester' => 'GeebyDeebyLocal\Ingest\FedoraHarvesterFactory',
            'GeebyDeebyLocal\Ingest\SolrHarvester' => 'GeebyDeebyLocal\Ingest\SolrHarvesterFactory',
        ],
    ],
    'view_manager' => [
        'display_exceptions' => false,
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
