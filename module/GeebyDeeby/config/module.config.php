<?php

return [
    'geeby-deeby' => [
        'siteTitle' => 'My Gamebook Web Page',
        'siteEmail' => 'me@emailhost.com',
        'siteOwner' => 'Webmaster',
        // See https://docs.laminas.dev/laminas-mail/transport/intro/ for more
        // documentation on email transport options:
        'emailTransport' => [
            'type' => 'sendmail',
            'options' => [],
        ],
        'dbHost' => 'localhost',
        'dbName' => 'gbdb', // database schema name
        'dbUser' => 'gbdb', // database username
        'dbPass' => 'gbdb', // database password
        // Should we group series entries by material type (true), or display them as one continuous list (false)?
        'groupSeriesByMaterialType' => true,
        // Set to a directory path to create log files of editor activity:
        'activity_log_dir' => false,
        'file_groups' => [
            // Fill this array with 'Group Name' => array(id1, id2, id3, ...)
            // if you wish to create custom file groupings on the "List Files"
            // page.  Leave it empty to group by standard File Type values.
        ],
        'isbn_links' => [
            'Shopping' => [
                'description' => 'These shopping links are included for your convenience. '
                    . 'This site is not affiliated with any of these retailers, and their '
                    . 'inclusion here is not intended as an endorsement.',
                'links' => [
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
                    'WorldCat [find in a library]' => 'https://www.worldcat.org/search?q=bn%3A%isbn10%&qt=advanced',
                ],
            ],
        ],
        'link_groups' => [
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
        ],
        'search_controls' => [
            // You can add edition attribute IDs and labels here to make them
            // available for display in search results:
            'edition_attributes' => [
                // 1234 => 'Show my example attribute',
            ],
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'followup' => 'GeebyDeeby\Controller\Plugin\Followup',
        ],
        'factories' => [
            'GeebyDeeby\Controller\Plugin\Followup' => 'Laminas\ServiceManager\Factory\InvokableFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'GeebyDeeby\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
            ],
            'category' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Category[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Category',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'categories' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Categories[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Category',
                        'action'        => 'list',
                    ],
                ],
            ],
            'city' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/City[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'City',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'cities' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Cities[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'City',
                        'action'        => 'list',
                    ],
                ],
            ],
            'country' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Country[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Country',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'countries' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Countries[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Country',
                        'action'        => 'list',
                    ],
                ],
            ],
            'edit' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/edit',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Edit',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                    'approve' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/Approve',
                            'defaults' => [
                                'controller'    => 'Approve',
                                'action'        => 'index',
                            ],
                        ],
                    ],
                    'category' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Category[/:id]',
                            'defaults' => [
                                'controller'    => 'EditCategory',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'category_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/CategoryList',
                            'defaults' => [
                                'controller'    => 'EditCategory',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'citation' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Citation[/:id]',
                            'defaults' => [
                                'controller'    => 'EditCitation',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'citation_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/CitationList',
                            'defaults' => [
                                'controller'    => 'EditCitation',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'city' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/City[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditCity',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'city_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/CityList',
                            'defaults' => [
                                'controller'    => 'EditCity',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'cleanup' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/Cleanup',
                            'defaults' => [
                                'controller'    => 'Cleanup',
                                'action'        => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'hierarchies' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/Hierarchies',
                                    'defaults' => [
                                        'controller'    => 'Cleanup',
                                        'action'        => 'hierarchies',
                                    ],
                                ],
                            ],
                            'imagedupes' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/ImageDupes',
                                    'defaults' => [
                                        'controller'    => 'Cleanup',
                                        'action'        => 'imagedupes',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'country' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Country[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditCountry',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'country_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/CountryList',
                            'defaults' => [
                                'controller'    => 'EditCountry',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'edition' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Edition[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditEdition',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'editionattribute' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/EditionsAttribute[/:id]',
                            'defaults' => [
                                'controller'    => 'EditEditionAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'editionattribute_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/EditionsAttributeList',
                            'defaults' => [
                                'controller'    => 'EditEditionAttribute',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'editionfulltextattribute' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/EditionFullTextAttribute[/:id]',
                            'defaults' => [
                                'controller'    => 'EditEditionFullTextAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'editionfulltextattribute_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/EditionFullTextAttributeList',
                            'defaults' => [
                                'controller'    => 'EditEditionFullTextAttribute',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'file' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/File[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditFile',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'file_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/FileList',
                            'defaults' => [
                                'controller'    => 'EditFile',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'file_type' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/FileType[/:id]',
                            'defaults' => [
                                'controller'    => 'EditFile',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'file_type_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/FileTypeList',
                            'defaults' => [
                                'controller'    => 'EditFile',
                                'action'        => 'typelist',
                            ],
                        ],
                    ],
                    'fulltextsource' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/FullTextSource[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditFullTextSource',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'fulltextsource_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/FullTextSourceList',
                            'defaults' => [
                                'controller'    => 'EditFullTextSource',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'item' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Item[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditItem',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'item_creator' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Item_Creator[/:id][/:action][/:extra]',
                            'defaults' => [
                                'controller'    => 'EditItemCreator',
                                'action'        => 'citation',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'item_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/ItemList',
                            'defaults' => [
                                'controller'    => 'EditItem',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'item_relationship_linker' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Item/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditItem',
                                'action'        => 'Relationship',
                            ],
                        ],
                    ],
                    'itemattribute' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ItemsAttribute[/:id]',
                            'defaults' => [
                                'controller'    => 'EditItemAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'itemattribute_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/ItemsAttributeList',
                            'defaults' => [
                                'controller'    => 'EditItemAttribute',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'itemrelationship' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/ItemsRelationship[/:id]',
                            'defaults' => [
                                'controller'    => 'EditItemRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'itemrelationship_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/ItemsRelationshipList',
                            'defaults' => [
                                'controller'    => 'EditItemRelationship',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'language' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Language[/:id]',
                            'defaults' => [
                                'controller'    => 'EditLanguage',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'language_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/LanguageList',
                            'defaults' => [
                                'controller'    => 'EditLanguage',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'link' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Link[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditLink',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'link_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/LinkList',
                            'defaults' => [
                                'controller'    => 'EditLink',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'link_type' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/LinkType[/:id]',
                            'defaults' => [
                                'controller'    => 'EditLink',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'link_type_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/LinkTypeList',
                            'defaults' => [
                                'controller'    => 'EditLink',
                                'action'        => 'typelist',
                            ],
                        ],
                    ],
                    'material' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/MaterialType[/:id]',
                            'defaults' => [
                                'controller'    => 'EditMaterialType',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'material_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/MaterialTypeList',
                            'defaults' => [
                                'controller'    => 'EditMaterialType',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'migrate' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/Migrate',
                            'defaults' => [
                                'controller'    => 'Migrate',
                                'action'        => 'index',
                            ],
                        ],
                    ],
                    'note' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Note[/:id]',
                            'defaults' => [
                                'controller'    => 'EditNote',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'note_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/NoteList',
                            'defaults' => [
                                'controller'    => 'EditNote',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'person' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Person[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'person_authority' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/PersonAuthority[/:id]',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'authority',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'person_authority_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PersonAuthorityList',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'authoritylist',
                            ],
                        ],
                    ],
                    'person_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PersonList',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'person_role' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/PersonRole[/:id]',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'role',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'person_role_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PersonRoleList',
                            'defaults' => [
                                'controller'    => 'EditPerson',
                                'action'        => 'rolelist',
                            ],
                        ],
                    ],
                    'platform' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Platform[/:id]',
                            'defaults' => [
                                'controller'    => 'EditPlatform',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'platform_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PlatformList',
                            'defaults' => [
                                'controller'    => 'EditPlatform',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'predicate' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Predicate[/:id]',
                            'defaults' => [
                                'controller'    => 'EditPredicate',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'predicate_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PredicateList',
                            'defaults' => [
                                'controller'    => 'EditPredicate',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'publisher' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Publisher[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditPublisher',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'publisher_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/PublisherList',
                            'defaults' => [
                                'controller'    => 'EditPublisher',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'series' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Series[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditSeries',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'series_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/SeriesList',
                            'defaults' => [
                                'controller'    => 'EditSeries',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'series_relationship_linker' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Series/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditSeries',
                                'action'        => 'Relationship',
                            ],
                        ],
                    ],
                    'seriesattribute' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/SeriesAttribute[/:id]',
                            'defaults' => [
                                'controller'    => 'EditSeriesAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'seriesattribute_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/SeriesAttributeList',
                            'defaults' => [
                                'controller'    => 'EditSeriesAttribute',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'seriesrelationship' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/SeriesRelationship[/:id]',
                            'defaults' => [
                                'controller'    => 'EditSeriesRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'seriesrelationship_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/SeriesRelationshipList',
                            'defaults' => [
                                'controller'    => 'EditSeriesRelationship',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'tag' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Tag[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditTag',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'tag_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/TagList',
                            'defaults' => [
                                'controller'    => 'EditTag',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'tag_relationship_linker' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/Tag/:id/Relationship/:relationship_id[/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditTag',
                                'action'        => 'Relationship',
                            ],
                        ],
                    ],
                    'tag_type' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/TagType[/:id]',
                            'defaults' => [
                                'controller'    => 'EditTag',
                                'action'        => 'type',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'tag_type_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/TagTypeList',
                            'defaults' => [
                                'controller'    => 'EditTag',
                                'action'        => 'typelist',
                            ],
                        ],
                    ],
                    'tagattribute' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/TagsAttribute[/:id]',
                            'defaults' => [
                                'controller'    => 'EditTagAttribute',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'tagattribute_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/TagsAttributeList',
                            'defaults' => [
                                'controller'    => 'EditTagAttribute',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'tagrelationship' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/TagsRelationship[/:id]',
                            'defaults' => [
                                'controller'    => 'EditTagRelationship',
                                'action'        => 'index',
                                'id'            => 'NEW',
                            ],
                        ],
                    ],
                    'tagrelationship_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/TagsRelationshipList',
                            'defaults' => [
                                'controller'    => 'EditTagRelationship',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'user' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/User[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditUser',
                                'action'        => 'index',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'user_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/UserList',
                            'defaults' => [
                                'controller'    => 'EditUser',
                                'action'        => 'list',
                            ],
                        ],
                    ],
                    'usergroup' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/UserGroup[/:id][/:action][/[:extra]]',
                            'defaults' => [
                                'controller'    => 'EditUser',
                                'action'        => 'usergroup',
                                'id'            => 'NEW',
                                'extra'         => null,
                            ],
                        ],
                    ],
                    'usergroup_list' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/UserGroupList',
                            'defaults' => [
                                'controller'    => 'EditUser',
                                'action'        => 'usergrouplist',
                            ],
                        ],
                    ],
                ],
            ],
            'edition' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Edition[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Edition',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'faqs' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/FAQs[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'FAQs',
                        'action'        => 'index',
                    ],
                ],
            ],
            'files' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Files[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'File',
                        'action'        => 'list',
                    ],
                ],
            ],
            'item' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Item[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Item',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'items' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Items[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Item',
                        'action'        => 'list',
                    ],
                ],
            ],
            'language' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Language[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Language',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'languages' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Languages[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Language',
                        'action'        => 'list',
                    ],
                ],
            ],
            'links' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Links[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Link',
                        'action'        => 'list',
                    ],
                ],
            ],
            'login' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Index',
                        'action'        => 'login',
                    ],
                ],
            ],
            'logout' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/logout',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Index',
                        'action'        => 'logout',
                    ],
                ],
            ],
            'material' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Material[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'MaterialType',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'materials' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Materials[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'MaterialType',
                        'action'        => 'list',
                    ],
                ],
            ],
            'people' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/People[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Person',
                        'action'        => 'list',
                    ],
                ],
            ],
            'person' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Person[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Person',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'platform' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Platform[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Platform',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'platforms' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Platforms[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Platform',
                        'action'        => 'list',
                    ],
                ],
            ],
            'publisher' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Publisher[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Publisher',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'publishers' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Publishers[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Publisher',
                        'action'        => 'list',
                    ],
                ],
            ],
            'reviews' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Reviews[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Reviews',
                        'action'        => 'list',
                    ],
                ],
            ],
            'search' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/Search',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Search',
                        'action'        => 'index',
                    ],
                ],
            ],
            'search-creator-ajax' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/Search/CreatorAjax',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Search',
                        'action'        => 'creatorAjax',
                    ],
                ],
            ],
            'search-edition-attribute-ajax' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/Search/EditionAttributeAjax',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Search',
                        'action'        => 'editionAttributeAjax',
                    ],
                ],
            ],
            'series' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Series[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Series',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'signup' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Signup[/:action]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Signup',
                        'action'        => 'index',
                    ],
                ],
            ],
            'suggest' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Suggest[/:table]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Suggest',
                        'action'        => 'index',
                    ],
                ],
            ],
            'tag' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Tag[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Tag',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'tag_by_label' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Tag/label/[:label]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Tag',
                        'action'        => 'label',
                    ],
                ],
            ],
            'tags' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Tags[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'Tag',
                        'action'        => 'list',
                    ],
                ],
            ],
            'user' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/User[/:id][/:action][/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'User',
                        'action'        => 'index',
                        'id'            => null,
                        'extra'         => null,
                    ],
                ],
            ],
            'users' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/Users[/[:extra]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'GeebyDeeby\Controller',
                        'controller'    => 'User',
                        'action'        => 'list',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'GeebyDeeby\Articles' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\Db\Row\PluginManager' =>
                'GeebyDeeby\ServiceManager\AbstractPluginManagerFactory',
            'GeebyDeeby\Db\Table\PluginManager' =>
                'GeebyDeeby\ServiceManager\AbstractPluginManagerFactory',
            'GeebyDeeby\EmailService' => 'GeebyDeeby\EmailServiceFactory',
            'Laminas\Authentication\AuthenticationService' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'Laminas\Db\Adapter\Adapter' => 'GeebyDeeby\Db\AdapterFactory',
        ],
        'aliases' => [
            'GeebyDeeby\Authentication' => 'Laminas\Authentication\AuthenticationService',
        ],
    ],
    'controllers' => [
        'abstract_factories' => [
            'GeebyDeeby\Controller\AbstractFactory',
        ],
        'aliases' => [
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
            'GeebyDeeby\Controller\EditEditionFullTextAttribute' => 'GeebyDeeby\Controller\EditEditionFullTextAttributeController',
            'GeebyDeeby\Controller\EditFile' => 'GeebyDeeby\Controller\EditFileController',
            'GeebyDeeby\Controller\EditFullTextSource' => 'GeebyDeeby\Controller\EditFullTextSourceController',
            'GeebyDeeby\Controller\Edition' => 'GeebyDeeby\Controller\EditionController',
            'GeebyDeeby\Controller\EditItem' => 'GeebyDeeby\Controller\EditItemController',
            'GeebyDeeby\Controller\EditItemAttribute' => 'GeebyDeeby\Controller\EditItemAttributeController',
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
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'GeebyDeeby\View\Helper\AnalyzeCredits' =>
                'GeebyDeeby\View\Helper\AnalyzeCreditsFactory',
            'GeebyDeeby\View\Helper\Auth' => 'GeebyDeeby\View\Helper\AuthFactory',
            'GeebyDeeby\View\Helper\Config' =>
                'GeebyDeeby\View\Helper\ConfigFactory',
            'GeebyDeeby\View\Helper\DescriptionSource' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FirstLetter' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FirstLetterMenu' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FixTitle' =>
                'GeebyDeeby\View\Helper\FixTitleFactory',
            'GeebyDeeby\View\Helper\FormatItemNumber' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\FormatReleaseDate' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\GroupEditions' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\IconButton' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\ScriptManager' =>
                'GeebyDeeby\View\Helper\ScriptManagerFactory',
            'GeebyDeeby\View\Helper\ShowEdition' =>
                'GeebyDeeby\View\Helper\ShowEditionFactory',
            'GeebyDeeby\View\Helper\ShowPerson' =>
                'Laminas\ServiceManager\Factory\InvokableFactory',
            'GeebyDeeby\View\Helper\ToggleLink' =>
                'GeebyDeeby\View\Helper\ToggleLinkFactory',
        ],
        'aliases' => [
            'analyzecredits' => 'GeebyDeeby\View\Helper\AnalyzeCredits',
            'analyzeCredits' => 'GeebyDeeby\View\Helper\AnalyzeCredits',
            'auth' => 'GeebyDeeby\View\Helper\Auth',
            'config' => 'GeebyDeeby\View\Helper\Config',
            'descriptionsource' => 'GeebyDeeby\View\Helper\DescriptionSource',
            'descriptionSource' => 'GeebyDeeby\View\Helper\DescriptionSource',
            'firstletter' => 'GeebyDeeby\View\Helper\FirstLetter',
            'firstLetter' => 'GeebyDeeby\View\Helper\FirstLetter',
            'firstlettermenu' => 'GeebyDeeby\View\Helper\FirstLetterMenu',
            'firstLetterMenu' => 'GeebyDeeby\View\Helper\FirstLetterMenu',
            'fixtitle' => 'GeebyDeeby\View\Helper\FixTitle',
            'fixTitle' => 'GeebyDeeby\View\Helper\FixTitle',
            'formatitemnumber' => 'GeebyDeeby\View\Helper\FormatItemNumber',
            'formatItemNumber' => 'GeebyDeeby\View\Helper\FormatItemNumber',
            'formatreleasedate' => 'GeebyDeeby\View\Helper\FormatReleaseDate',
            'formatReleaseDate' => 'GeebyDeeby\View\Helper\FormatReleaseDate',
            'groupeditions' => 'GeebyDeeby\View\Helper\GroupEditions',
            'groupEditions' => 'GeebyDeeby\View\Helper\GroupEditions',
            'iconButton' => 'GeebyDeeby\View\Helper\IconButton',
            'scriptmanager' => 'GeebyDeeby\View\Helper\ScriptManager',
            'scriptManager' => 'GeebyDeeby\View\Helper\ScriptManager',
            'showedition' => 'GeebyDeeby\View\Helper\ShowEdition',
            'showEdition' => 'GeebyDeeby\View\Helper\ShowEdition',
            'showperson' => 'GeebyDeeby\View\Helper\ShowPerson',
            'showPerson' => 'GeebyDeeby\View\Helper\ShowPerson',
            'togglelink' => 'GeebyDeeby\View\Helper\ToggleLink',
            'toggleLink' => 'GeebyDeeby\View\Helper\ToggleLink',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
