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
        // Should we group series entries by material type (true), or display them as one continuous list (false)?
        'groupSeriesByMaterialType' => true,
        'file_groups' => array(
            // Fill this array with 'Group Name' => array(id1, id2, id3, ...)
            // if you wish to create custom file groupings on the "List Files"
            // page.  Leave it empty to group by standard File Type values.
        ),
        'isbn_links' => array(
            'Shopping' => array(
                'description' => 'These shopping links are included for your convenience. '
                    . 'This site is not affiliated with any of these retailers, and their '
                    . 'inclusion here is not intended as an endorsement.',
                'links' => array(
                    'Amazon.ca' => 'http://www.amazon.ca/exec/obidos/ASIN/%isbn10%',
                    'Amazon.com' => 'http://www.amazon.com/exec/obidos/ASIN/%isbn10%',
                    'Amazon.com.au' => 'http://www.amazon.com.au/exec/obidos/ASIN/%isbn10%',
                    'Amazon.com.br' => 'http://www.amazon.com.br/exec/obidos/ASIN/%isbn10%',
                    'Amazon.co.jp' => 'http://www.amazon.co.jp/exec/obidos/ASIN/%isbn10%',
                    'Amazon.com.mx' => 'http://www.amazon.com.mx/exec/obidos/ASIN/%isbn10%',
                    'Amazon.co.uk' => 'http://www.amazon.co.uk/exec/obidos/ASIN/%isbn10%',
                    'Amazon.de' => 'http://www.amazon.de/exec/obidos/ASIN/%isbn10%',
                    'Amazon.es' => 'http://www.amazon.es/exec/obidos/ASIN/%isbn10%',
                    'Amazon.fr' => 'http://www.amazon.fr/exec/obidos/ASIN/%isbn10%',
                    'Amazon.it' => 'http://www.amazon.it/exec/obidos/ASIN/%isbn10%',
                    'Barnes & Noble' => 'http://search.barnesandnoble.com/booksearch/isbninquiry.asp?ISBN=%isbn10%',
                    'BookFinder.com' => 'http://www.bookfinder.com/search/?author=&title=&submit=Begin+Search&new_used=*&binding=*&isbn=%isbn10%&keywords=&minprice=&maxprice=&currency=USD&mode=advanced&st=sr&ac=qr',
                ),
            ),
        ),
        'link_groups' => array(
            // Fill this array with 'Group Name' => array(
            //     'desc' => string value (HTML description to show on page)
            //     'title' => string value (page title override)
            //     'typeMatch' => string value (prefix to use for filtering by
            //                                  link type)
            //     'typeTrim' => int value (number of characters to strip from
            //                              left side of link type name)
            // ), ...
            // if you wish to create custom link groupings. These custom groups
            // may be accessed by adding the group name as a subdirectory of the
            // main /Links URL. (e.g. http://mysite.org/Links/MyGroupName).
        ),
    ),
    'controller_plugins' => array(
        'aliases' => array(
            'followup' => 'GeebyDeeby\Controller\Plugin\Followup',
        ),
        'factories' => array(
            'GeebyDeeby\Controller\Plugin\Followup' => 'Zend\ServiceManager\Factory\InvokableFactory',
        )
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'GeebyDeeby\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'category' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Category[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Category',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'categories' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Categories[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Category',
                        'action'        => 'list',
                    ),
                ),
            ),
            'city' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/City[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'City',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'cities' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Cities[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'City',
                        'action'        => 'list',
                    ),
                ),
            ),
            'country' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Country[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Country',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'countries' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Countries[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Country',
                        'action'        => 'list',
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
                    'citation' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Citation[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditCitation',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'citation_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/CitationList',
                            'defaults' => array(
                                'controller'    => 'EditCitation',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'city' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/City[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditCity',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'city_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/CityList',
                            'defaults' => array(
                                'controller'    => 'EditCity',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'cleanup' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/Cleanup',
                            'defaults' => array(
                                'controller'    => 'Cleanup',
                                'action'        => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'hierarchies' => array(
                                'type'    => 'Literal',
                                'options' => array(
                                    'route'    => '/Hierarchies',
                                    'defaults' => array(
                                        'controller'    => 'Cleanup',
                                        'action'        => 'hierarchies',
                                    ),
                                ),
                            ),
                            'imagedupes' => array(
                                'type'    => 'Literal',
                                'options' => array(
                                    'route'    => '/ImageDupes',
                                    'defaults' => array(
                                        'controller'    => 'Cleanup',
                                        'action'        => 'imagedupes',
                                    ),
                                ),
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
                    'edition' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Edition[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditEdition',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'editionattribute' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/EditionsAttribute[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditEditionAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'editionattribute_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/EditionsAttributeList',
                            'defaults' => array(
                                'controller'    => 'EditEditionAttribute',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'file' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/File[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditFile',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
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
                    'fulltextsource' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/FullTextSource[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditFullTextSource',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'fulltextsource_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/FullTextSourceList',
                            'defaults' => array(
                                'controller'    => 'EditFullTextSource',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'item' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Item[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditItem',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'item_creator' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Item_Creator[/:id][/:action][/:extra]',
                            'defaults' => array(
                                'controller'    => 'EditItemCreator',
                                'action'        => 'citation',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'item_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/ItemList',
                            'defaults' => array(
                                'controller'    => 'EditItem',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'item_relationship_linker' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Item/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditItem',
                                'action'        => 'Relationship',
                            ),
                        ),
                    ),
                    'itemrelationship' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/ItemsRelationship[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditItemRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'itemrelationship_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/ItemsRelationshipList',
                            'defaults' => array(
                                'controller'    => 'EditItemRelationship',
                                'action'        => 'list',
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
                            'route'    => '/Link[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditLink',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
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
                    'migrate' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/Migrate',
                            'defaults' => array(
                                'controller'    => 'Migrate',
                                'action'        => 'index',
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
                            'route'    => '/Person[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'person_authority' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/PersonAuthority[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'authority',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'person_authority_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PersonAuthorityList',
                            'defaults' => array(
                                'controller'    => 'EditPerson',
                                'action'        => 'authoritylist',
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
                    'predicate' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Predicate[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditPredicate',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'predicate_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/PredicateList',
                            'defaults' => array(
                                'controller'    => 'EditPredicate',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'publisher' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Publisher[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditPublisher',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null
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
                            'route'    => '/Series[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditSeries',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
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
                    'series_relationship_linker' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Series/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditSeries',
                                'action'        => 'Relationship',
                            ),
                        ),
                    ),
                    'seriesattribute' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/SeriesAttribute[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditSeriesAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'seriesattribute_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/SeriesAttributeList',
                            'defaults' => array(
                                'controller'    => 'EditSeriesAttribute',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'seriesrelationship' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/SeriesRelationship[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditSeriesRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'seriesrelationship_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/SeriesRelationshipList',
                            'defaults' => array(
                                'controller'    => 'EditSeriesRelationship',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'tag' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Tag[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditTag',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'tag_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/TagList',
                            'defaults' => array(
                                'controller'    => 'EditTag',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'tag_relationship_linker' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/Tag/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditTag',
                                'action'        => 'Relationship',
                            ),
                        ),
                    ),
                    'tag_type' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/TagType[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditTag',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'tag_type_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/TagTypeList',
                            'defaults' => array(
                                'controller'    => 'EditTag',
                                'action'        => 'typelist',
                            ),
                        ),
                    ),
                    'tagattribute' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/TagsAttribute[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditTagAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'tagattribute_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/TagsAttributeList',
                            'defaults' => array(
                                'controller'    => 'EditTagAttribute',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'tagrelationship' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/TagsRelationship[/:id]',
                            'defaults' => array(
                                'controller'    => 'EditTagRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ),
                        ),
                    ),
                    'tagrelationship_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/TagsRelationshipList',
                            'defaults' => array(
                                'controller'    => 'EditTagRelationship',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/User[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditUser',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'user_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/UserList',
                            'defaults' => array(
                                'controller'    => 'EditUser',
                                'action'        => 'list',
                            ),
                        ),
                    ),
                    'usergroup' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/UserGroup[/:id][/:action][/[:extra]]',
                            'defaults' => array(
                                'controller'    => 'EditUser',
                                'action'        => 'usergroup',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ),
                        ),
                    ),
                    'usergroup_list' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route'    => '/UserGroupList',
                            'defaults' => array(
                                'controller'    => 'EditUser',
                                'action'        => 'usergrouplist',
                            ),
                        ),
                    ),
                ),
            ),
            'edition' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Edition[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Edition',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'faqs' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/FAQs[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'FAQs',
                        'action'        => 'index',
                    ),
                ),
            ),
            'files' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Files[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'File',
                        'action'        => 'list',
                    ),
                ),
            ),
            'item' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Item[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Item',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'items' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Items[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Item',
                        'action'        => 'list',
                    ),
                ),
            ),
            'language' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Language[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Language',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'languages' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Languages[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Language',
                        'action'        => 'list',
                    ),
                ),
            ),
            'links' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Links[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Link',
                        'action'        => 'list',
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
            'material' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Material[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'MaterialType',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'materials' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Materials[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'MaterialType',
                        'action'        => 'list',
                    ),
                ),
            ),
            'people' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/People[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Person',
                        'action'        => 'list',
                    ),
                ),
            ),
            'person' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Person[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Person',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'platform' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Platform[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Platform',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'platforms' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Platforms[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Platform',
                        'action'        => 'list',
                    ),
                ),
            ),
            'publisher' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Publisher[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Publisher',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'publishers' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Publishers[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Publisher',
                        'action'        => 'list',
                    ),
                ),
            ),
            'reviews' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Reviews[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Reviews',
                        'action'        => 'list',
                    ),
                ),
            ),
            'search' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/Search',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Search',
                        'action'        => 'index',
                    ),
                ),
            ),
            'series' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Series[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Series',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'signup' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Signup[/:action]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Signup',
                        'action'        => 'index',
                    ),
                ),
            ),
            'suggest' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Suggest[/:table]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Suggest',
                        'action'        => 'index',
                    ),
                ),
            ),
            'tag' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Tag[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Tag',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'tags' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Tags[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Tag',
                        'action'        => 'list',
                    ),
                ),
            ),
            'user' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/User[/:id][/:action][/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'User',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ),
                ),
            ),
            'users' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/Users[/[:extra]]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'User',
                        'action'        => 'list',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'GeebyDeeby\Articles' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\Db\Row\PluginManager' => function ($sm) {
                return new \GeebyDeeby\Db\Row\PluginManager($sm);
            },
            'GeebyDeeby\Db\Table\PluginManager' => function ($sm) {
                return new \GeebyDeeby\Db\Table\PluginManager($sm);
            },
            'Zend\Authentication\AuthenticationService' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                $config = $sm->get('Config');
                return new \Zend\Db\Adapter\Adapter(
                    array(
                        'driver' => 'mysqli',
                        'charset' => 'utf8',
                        'hostname' => $config['geeby-deeby']['dbHost'],
                        'username' => $config['geeby-deeby']['dbUser'],
                        'password' => $config['geeby-deeby']['dbPass'],
                        'database' => $config['geeby-deeby']['dbName'],
                        'options' => array('buffer_results' => true)
                    )
                );
            },
        ),
        'aliases' => array(
            'GeebyDeeby\Authentication' => 'Zend\Authentication\AuthenticationService',
        ),
    ),
    'controllers' => array(
        'abstract_factories' => array(
            'GeebyDeeby\Controller\AbstractFactory',
        ),
        'aliases' => array(
            'GeebyDeeby\Controller\Approve' => 'GeebyDeeby\Controller\ApproveController',
            'GeebyDeeby\Controller\Category' => 'GeebyDeeby\Controller\CategoryController',
            'GeebyDeeby\Controller\City' => 'GeebyDeeby\Controller\CityController',
            'GeebyDeeby\Controller\Cleanup' => 'GeebyDeeby\Controller\CleanupController',
            'GeebyDeeby\Controller\Country' => 'GeebyDeeby\Controller\CountryController',
            'GeebyDeeby\Controller\Edit' => 'GeebyDeeby\Controller\EditController',
            'GeebyDeeby\Controller\EditCategory' => 'GeebyDeeby\Controller\EditCategoryController',
            'GeebyDeeby\Controller\EditCitation' => 'GeebyDeeby\Controller\EditCitationController',
            'GeebyDeeby\Controller\EditCity' => 'GeebyDeeby\Controller\EditCityController',
            'GeebyDeeby\Controller\EditCountry' => 'GeebyDeeby\Controller\EditCountryController',
            'GeebyDeeby\Controller\EditEdition' => 'GeebyDeeby\Controller\EditEditionController',
            'GeebyDeeby\Controller\EditEditionAttribute' => 'GeebyDeeby\Controller\EditEditionAttributeController',
            'GeebyDeeby\Controller\EditFile' => 'GeebyDeeby\Controller\EditFileController',
            'GeebyDeeby\Controller\EditFullTextSource' => 'GeebyDeeby\Controller\EditFullTextSourceController',
            'GeebyDeeby\Controller\Edition' => 'GeebyDeeby\Controller\EditionController',
            'GeebyDeeby\Controller\EditItem' => 'GeebyDeeby\Controller\EditItemController',
            'GeebyDeeby\Controller\EditItemCreator' => 'GeebyDeeby\Controller\EditItemCreatorController',
            'GeebyDeeby\Controller\EditItemRelationship' => 'GeebyDeeby\Controller\EditItemRelationshipController',
            'GeebyDeeby\Controller\EditMaterialType' => 'GeebyDeeby\Controller\EditMaterialTypeController',
            'GeebyDeeby\Controller\EditNote' => 'GeebyDeeby\Controller\EditNoteController',
            'GeebyDeeby\Controller\EditLanguage' => 'GeebyDeeby\Controller\EditLanguageController',
            'GeebyDeeby\Controller\EditLink' => 'GeebyDeeby\Controller\EditLinkController',
            'GeebyDeeby\Controller\EditPerson' => 'GeebyDeeby\Controller\EditPersonController',
            'GeebyDeeby\Controller\EditPredicate' => 'GeebyDeeby\Controller\EditPredicateController',
            'GeebyDeeby\Controller\EditPlatform' => 'GeebyDeeby\Controller\EditPlatformController',
            'GeebyDeeby\Controller\EditPublisher' => 'GeebyDeeby\Controller\EditPublisherController',
            'GeebyDeeby\Controller\EditSeries' => 'GeebyDeeby\Controller\EditSeriesController',
            'GeebyDeeby\Controller\EditSeriesAttribute' => 'GeebyDeeby\Controller\EditSeriesAttributeController',
            'GeebyDeeby\Controller\EditSeriesRelationship' => 'GeebyDeeby\Controller\EditSeriesRelationshipController',
            'GeebyDeeby\Controller\EditTag' => 'GeebyDeeby\Controller\EditTagController',
            'GeebyDeeby\Controller\EditTagAttribute' => 'GeebyDeeby\Controller\EditTagAttributeController',
            'GeebyDeeby\Controller\EditTagRelationship' => 'GeebyDeeby\Controller\EditTagRelationshipController',
            'GeebyDeeby\Controller\EditUser' => 'GeebyDeeby\Controller\EditUserController',
            'GeebyDeeby\Controller\FAQs' => 'GeebyDeeby\Controller\FAQsController',
            'GeebyDeeby\Controller\File' => 'GeebyDeeby\Controller\FileController',
            'GeebyDeeby\Controller\Index' => 'GeebyDeeby\Controller\IndexController',
            'GeebyDeeby\Controller\Item' => 'GeebyDeeby\Controller\ItemController',
            'GeebyDeeby\Controller\Language' => 'GeebyDeeby\Controller\LanguageController',
            'GeebyDeeby\Controller\Link' => 'GeebyDeeby\Controller\LinkController',
            'GeebyDeeby\Controller\MaterialType' => 'GeebyDeeby\Controller\MaterialTypeController',
            'GeebyDeeby\Controller\Migrate' => 'GeebyDeeby\Controller\MigrateController',
            'GeebyDeeby\Controller\Person' => 'GeebyDeeby\Controller\PersonController',
            'GeebyDeeby\Controller\Platform' => 'GeebyDeeby\Controller\PlatformController',
            'GeebyDeeby\Controller\Publisher' => 'GeebyDeeby\Controller\PublisherController',
            'GeebyDeeby\Controller\Reviews' => 'GeebyDeeby\Controller\ReviewsController',
            'GeebyDeeby\Controller\Search' => 'GeebyDeeby\Controller\SearchController',
            'GeebyDeeby\Controller\Series' => 'GeebyDeeby\Controller\SeriesController',
            'GeebyDeeby\Controller\Signup' => 'GeebyDeeby\Controller\SignupController',
            'GeebyDeeby\Controller\Suggest' => 'GeebyDeeby\Controller\SuggestController',
            'GeebyDeeby\Controller\Tag' => 'GeebyDeeby\Controller\TagController',
            'GeebyDeeby\Controller\User' => 'GeebyDeeby\Controller\UserController',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'analyzecredits' => function ($sm) {
                $tables = $sm->get('GeebyDeeby\Db\Table\PluginManager');
                return new \GeebyDeeby\View\Helper\AnalyzeCredits(
                    $tables->get('pseudonyms'), $tables->get('itemscreatorscitations')
                );
            },
            'auth' => function ($sm) {
                return new \GeebyDeeby\View\Helper\Auth(
                    $sm->get('GeebyDeeby\Authentication')
                );
            },
            'config' => function ($sm) {
                $cfg = $sm->get('config');
                return new \GeebyDeeby\View\Helper\Config($cfg['geeby-deeby']);
            },
            'fixtitle' => function ($sm) {
                return new \GeebyDeeby\View\Helper\FixTitle(
                    $sm->get('GeebyDeeby\Articles')
                );
            },
            'GeebyDeeby\View\Helper\ScriptManager' => function ($sm) {
                $base = $sm->get('ViewHelperManager')->get('basePath')->__invoke();
                return new \GeebyDeeby\View\Helper\ScriptManager(
                    $base, $sm->get('ViewHelperManager')->get('headScript')
                );
            },
            'showedition' => function ($sm) {
                $controller = $sm->get('ControllerLoader')
                    ->get('GeebyDeeby\Controller\Edition');
                return new \GeebyDeeby\View\Helper\ShowEdition($controller);
            },
            'GeebyDeeby\View\Helper\DescriptionSource' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FirstLetter' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FirstLetterMenu' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FormatReleaseDate' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FormatItemNumber' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\GroupEditions' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\ShowPerson' =>
                'Zend\ServiceManager\Factory\InvokableFactory',
        ),
        'aliases' => array(
            'descriptionsource' => 'GeebyDeeby\View\Helper\DescriptionSource',
            'descriptionSource' => 'GeebyDeeby\View\Helper\DescriptionSource',
            'firstletter' => 'GeebyDeeby\View\Helper\FirstLetter',
            'firstLetter' => 'GeebyDeeby\View\Helper\FirstLetter',
            'firstlettermenu' => 'GeebyDeeby\View\Helper\FirstLetterMenu',
            'firstLetterMenu' => 'GeebyDeeby\View\Helper\FirstLetterMenu',
            'formatreleasedate' => 'GeebyDeeby\View\Helper\FormatReleaseDate',
            'formatReleaseDate' => 'GeebyDeeby\View\Helper\FormatReleaseDate',
            'formatitemnumber' => 'GeebyDeeby\View\Helper\FormatItemNumber',
            'formatItemNumber' => 'GeebyDeeby\View\Helper\FormatItemNumber',
            'groupeditions' => 'GeebyDeeby\View\Helper\GroupEditions',
            'groupEditions' => 'GeebyDeeby\View\Helper\GroupEditions',
            'scriptmanager' => 'GeebyDeeby\View\Helper\ScriptManager',
            'scriptManager' => 'GeebyDeeby\View\Helper\ScriptManager',
            'showperson' => 'GeebyDeeby\View\Helper\ShowPerson',
            'showPerson' => 'GeebyDeeby\View\Helper\ShowPerson',
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
