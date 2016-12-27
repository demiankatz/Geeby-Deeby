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
        'siteTitle' => 'Demian\'s Gamebook Web Page',
        'siteEmail' => 'demiankatz@gmail.com',
        'siteOwner' => 'Demian Katz',
    ),
    'controllers' => array(
        'invokables' => array(
            'GeebyDeeby\Controller\About' => 'GeebyDeebyLocal\Controller\AboutController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'thanks' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/Thanks',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\About',
                        'action'     => 'thanks',
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
