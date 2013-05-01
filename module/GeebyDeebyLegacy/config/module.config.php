<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'legacy' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/:filename',
                    'constraints' => array(
                        'filename' => '.*(.cgi|.htm|.php)',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeebyLegacy\Controller',
                        'controller'    => 'Legacy',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'GeebyDeebyLegacy\Controller\Legacy' => 'GeebyDeebyLegacy\Controller\LegacyController',
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'geeby-deeby-legacy/legacy/notfound' => __DIR__ . '/../view/geeby-deeby-legacy/legacy/notfound.phtml',
        ),
    ),
);
