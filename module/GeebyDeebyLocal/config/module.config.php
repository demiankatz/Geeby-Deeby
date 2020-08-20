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
        'siteTitle' => 'Demian\'s Gamebook Web Page',
        'siteEmail' => 'demiankatz@gmail.com',
        'siteOwner' => 'Demian Katz',
    ],
    'controllers' => [
        'invokables' => [
            'GeebyDeeby\Controller\About' => 'GeebyDeebyLocal\Controller\AboutController',
        ],
    ],
    'router' => [
        'routes' => [
            'thanks' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
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
