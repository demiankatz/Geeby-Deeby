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
                'file' => 'GeebyDeeby\Db\Table\File',
                'filetype' => 'GeebyDeeby\Db\Table\FileType',
                'language' => 'GeebyDeeby\Db\Table\Language',
                'link' => 'GeebyDeeby\Db\Table\Link',
                'linktype' => 'GeebyDeeby\Db\Table\LinkType',
                'materialtype' => 'GeebyDeeby\Db\Table\MaterialType',
                'note' => 'GeebyDeeby\Db\Table\Note',
                'person' => 'GeebyDeeby\Db\Table\Person',
                'platform' => 'GeebyDeeby\Db\Table\Platform',
                'publisher' => 'GeebyDeeby\Db\Table\Publisher',
                'role' => 'GeebyDeeby\Db\Table\Role',
                'series' => 'GeebyDeeby\Db\Table\Series',
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
                    'file' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/File[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditFile',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'file_type' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/FileType[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditFile',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'file_type_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/FileTypeList',
                            'defaults' => array(
                                'controller'    => 'EditFile',
                                'action'        => 'typelist',
                            ),
                        ),
                    ),
                    'language' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Language[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditLanguage',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'link' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Link[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditLink',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'link_type' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/LinkType[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditLink',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'link_type_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/LinkTypeList',
                            'defaults' => array(
                                'controller'    => 'EditLink',
                                'action'        => 'typelist',
                            ),
                        ),
                    ),
                    'material' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/MaterialType[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditMaterialType',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'material_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/MaterialTypeList',
                            'defaults' => array(
                                'controller'    => 'EditMaterialType',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'note' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Note[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditNote',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'platform' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Platform[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPlatform',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'publisher' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Publisher[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPublisher',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
                    'series' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Series[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditSeries',
                                'action'        => 'index',
                                'id'            => 'NEW',
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
            'GeebyDeeby\Controller\EditFile' => 'GeebyDeeby\Controller\EditFileController',
            'GeebyDeeby\Controller\EditMaterialType' => 'GeebyDeeby\Controller\EditMaterialTypeController',
            'GeebyDeeby\Controller\EditNote' => 'GeebyDeeby\Controller\EditNoteController',
            'GeebyDeeby\Controller\EditLanguage' => 'GeebyDeeby\Controller\EditLanguageController',
            'GeebyDeeby\Controller\EditLink' => 'GeebyDeeby\Controller\EditLinkController',
            'GeebyDeeby\Controller\EditPerson' => 'GeebyDeeby\Controller\EditPersonController',
            'GeebyDeeby\Controller\EditPlatform' => 'GeebyDeeby\Controller\EditPlatformController',
            'GeebyDeeby\Controller\EditPublisher' => 'GeebyDeeby\Controller\EditPublisherController',
            'GeebyDeeby\Controller\EditSeries' => 'GeebyDeeby\Controller\EditSeriesController',
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
