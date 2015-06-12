<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'controllers' => array(
        'invokables' => array(
            'GeebyDeeby\Controller\About' => 'GeebyDeebyLocal\Controller\AboutController',
            'GeebyDeeby\Controller\Edition' => 'GeebyDeebyLocal\Controller\EditionController',
            'GeebyDeeby\Controller\Ingest' => 'GeebyDeebyLocal\Controller\IngestController',
            'GeebyDeeby\Controller\Item' => 'GeebyDeebyLocal\Controller\ItemController',
            'GeebyDeeby\Controller\Ontology' => 'GeebyDeebyLocal\Controller\OntologyController',
            'GeebyDeeby\Controller\Podcast' => 'GeebyDeebyLocal\Controller\PodcastController',
            'GeebyDeeby\Controller\Series' => 'GeebyDeebyLocal\Controller\SeriesController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'ingest' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'ingest',
                        'defaults' => array(
                            'controller' => 'GeebyDeeby\Controller\Ingest',
                            'action' => 'index',
                        )
                    )
                )
            )
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
