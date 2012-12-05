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
        'siteTitle' => 'My Gamebook Web Page',
        'siteEmail' => 'me@emailhost.com',
        'siteOwner' => 'Webmaster',
        'dbHost' => 'localhost',
        'dbName' => 'gbdb', // database schema name
        'dbUser' => 'gbdb', // database username
        'dbPass' => 'gbdb', // database password
        'db_table_plugin_manager' => array(
            'invokables' => array(
                'category' => 'GeebyDeeby\Db\Table\Category',
                'country' => 'GeebyDeeby\Db\Table\Country',
                'person' => 'GeebyDeeby\Db\Table\Person',
                'role' => 'GeebyDeeby\Db\Table\Role',
                'user' => 'GeebyDeeby\Db\Table\User',
                'usergroup' => 'GeebyDeeby\Db\Table\UserGroup',
            ),
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'followup' => 'GeebyDeeby\Controller\Plugin\Followup',
        )
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'edit' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/edit',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Edit',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'approve' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/Approve',
                            'defaults' => array(
                                'controller'    => 'Approve',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'category' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Category[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditCategory',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'category_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/CategoryList',
                            'defaults' => array(
                                'controller'    => 'EditCategory',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'country' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Country[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditCountry',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'country_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/CountryList',
                            'defaults' => array(
                                'controller'    => 'EditCountry',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'file_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/FileList',
                            'defaults' => array(
                                'controller'    => 'EditFile',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'language_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/LanguageList',
                            'defaults' => array(
                                'controller'    => 'EditLanguage',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'link_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/LinkList',
                            'defaults' => array(
                                'controller'    => 'EditLink',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'material_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/MaterialList',
                            'defaults' => array(
                                'controller'    => 'EditMaterial',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'note_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/NoteList',
                            'defaults' => array(
                                'controller'    => 'EditNote',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'person' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Person[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'person_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PersonList',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'person_role' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/PersonRole[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'role',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'person_role_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PersonRoleList',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'rolelist',
                            ),
                        ),
                    ),
                    'platform_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PlatformList',
                            'defaults' => array(
                                'controller'    => 'EditPlatform',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'publisher_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PublisherList',
                            'defaults' => array(
                                'controller'    => 'EditPublisher',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'series_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/SeriesList',
                            'defaults' => array(
                                'controller'    => 'EditSeries',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                ),
            ),
            'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Index',
                        'action'        => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Index',
                        'action'        => 'logout',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'GeebyDeeby\DbAdapter' => function ($sm) {
                $config = $sm->get('Config');
                return new \Zend\Db\Adapter\Adapter(
                    array(
                        'driver' => 'mysqli',
                        'hostname' => $config['geeby-deeby']['dbHost'],
                        'username' => $config['geeby-deeby']['dbUser'],
                        'password' => $config['geeby-deeby']['dbPass'],
                        'database' => $config['geeby-deeby']['dbName'],
                        'options' => array('buffer_results' => true)
                    )
                );
            },
            'GeebyDeeby\DbTablePluginManager' => function ($sm) {
                $config = $sm->get('Config');
                return new \GeebyDeeby\Db\Table\PluginManager(
                    new Zend\ServiceManager\Config(
                        $config['geeby-deeby']['db_table_plugin_manager']
                    )
                );
            },
        ),
        'invokables' => array(
            'GeebyDeeby\Authentication' => 'Zend\Authentication\AuthenticationService',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'GeebyDeeby\Controller\Edit' => 'GeebyDeeby\Controller\EditController',
            'GeebyDeeby\Controller\EditCategory' => 'GeebyDeeby\Controller\EditCategoryController',
            'GeebyDeeby\Controller\EditCountry' => 'GeebyDeeby\Controller\EditCountryController',
            'GeebyDeeby\Controller\EditPerson' => 'GeebyDeeby\Controller\EditPersonController',
            'GeebyDeeby\Controller\Index' => 'GeebyDeeby\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
