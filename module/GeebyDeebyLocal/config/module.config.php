<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'geeby-deeby' => array(
        'siteTitle' => 'The Edward T. LeBlanc Memorial Dime Novel Bibliography',
        'siteEmail' => 'demian.katz@villanova.edu',
        'siteOwner' => 'Demian Katz',
        'groupSeriesByMaterialType' => false,
    ),
    'controllers' => array(
        'invokables' => array(
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
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'harvest-existing' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'harvest existing <dir>',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvestexisting',
                        )
                    )
                ),
                'harvest-series' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'harvest series <series> <dir>',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'harvestseries',
                        )
                    )
                ),
                'ingest-directory' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'ingest directory <dir>',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'directory',
                        )
                    )
                ),
                'ingest-existing' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'ingest [existing]',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'existing',
                        )
                    )
                ),
                'ingest-images' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'load iiif',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'loadiiif',
                        )
                    )
                ),
                'ingest-makeissues' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'makeissues <series> <prefix>',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'makeissues',
                        )
                    )
                ),
                'ingest-series' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'ingest series <series>',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'series',
                        )
                    )
                ),
            )
        )
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'podcast' => 'GeebyDeebyLocal\Controller\Plugin\Podcast',
        )
    ),
    'router' => array(
        'routes' => array(
            'about' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/About',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'index',
                    ),
                ),
            ),
            'about-credits' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/About/Credits',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'credits',
                    ),
                ),
            ),
            'about-progress' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/About/Progress',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'progress',
                    ),
                ),
            ),
            'ontology' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/ontology[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Ontology',
                        'action'     => 'index',
                    ),
                ),
            ),
            'podcast' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Podcast[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'index',
                    ),
                ),
            ),
            'podcast-about' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Podcast/About[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'about',
                    ),
                ),
            ),
            'podcast-mitties' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Podcast/Mittie[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'mittie',
                    ),
                ),
            ),
            'podcast-professor' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Podcast/Professor[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'professor',
                    ),
                ),
            ),
            'podcast-rss' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Podcast/RSS[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'rss',
                    ),
                ),
            ),
            'podcast-rss-lowercase-for-apple' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/podcast/rss[/]',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Podcast',
                        'action'     => 'rss',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'display_exceptions' => false,
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
