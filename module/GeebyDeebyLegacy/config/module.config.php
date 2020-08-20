<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'router' => [
        'routes' => [
            'legacy' => [
                'type'    => 'Zend\Router\Http\Segment',
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
