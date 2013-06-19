<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'adfabreward_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/AdfabReward/Entity'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'AdfabReward\Entity'  => 'adfabreward_entity'
                )
            )
        )
    ),

	'data-fixture' => array(
		'location' => __DIR__ . '/../src/AdfabReward/DataFixtures/ORM',
	),

    'view_manager' => array(
        'template_path_stack' => array(
            'adfabreward' => __DIR__ . '/../view',
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'adfabrewardadmin' => 'AdfabReward\Controller\AdminController',
            'adfabreward'      => 'AdfabReward\Controller\IndexController',
        ),
    ),

    'core_layout' => array(
        'AdfabReward' => array(
            'default_layout' => 'layout/2columns-right',
            'children_views' => array(
                'col_right'  => 'application/common/column_right.phtml',
            ),
            'controllers' => array(
                'adfabreward'   => array(
                    'default_layout' => 'layout/2columns-right',
                    'children_views' => array(
                        'col_right'  => 'application/common/column_right.phtml',
                    ),
                    'actions' => array(
                        'default_layout' => 'layout/homepage-2columns-right',
                        'children_views' => array(
                            'col_right'  => 'application/common/column_right.phtml',
                        ),
                    ),
                ),
            ),
        ),
    ),

    'router' => array(
        'routes' => array(
            'reward' => array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/reward',
                    'defaults' => array(
                        'controller' => 'adfabreward',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' =>array(
                    'leaderboard' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/leaderboard/:period[/:filter][/:p]',
                            'constraints' => array(
                                'filter' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'adfabreward',
                                'action'     => 'leaderboard'
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin' => array(
                'child_routes' => array(
                    'adfabrewardadmin' => array(
                        'type' => 'Literal',
                        'priority' => 1000,
                        'options' => array(
                            'route' => '/reward',
                            'defaults' => array(
                                'controller' => 'adfabrewardadmin',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' =>array(
                            'list' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/list[/:p]',
                                    'defaults' => array(
                                        'controller' => 'adfabrewardadmin',
                                        'action'     => 'list',
                                    ),
                                ),
                            ),
                            'create' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/create',
                                    'defaults' => array(
                                        'controller' => 'adfabrewardadmin',
                                        'action'     => 'create'
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/edit/:actionId',
                                    'defaults' => array(
                                        'controller' => 'adfabrewardadmin',
                                        'action'     => 'edit',
                                        'userId'     => 0
                                    ),
                                ),
                            ),
                            'remove' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/remove/:actionId',
                                    'defaults' => array(
                                        'controller' => 'adfabrewardadmin',
                                        'action'     => 'remove',
                                        'userId'     => 0
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'translator' => array(
            'locale' => 'fr_FR',
            'translation_file_patterns' => array(
                    array(
                            'type'         => 'phpArray',
                            'base_dir'     => __DIR__ . '/../language',
                            'pattern'      => '%s.php',
                            'text_domain'  => 'adfabreward'
                    ),
            ),
    ),

    'navigation' => array(
        'default' => array(
            'reward' => array(
                'label' => 'Les récompenses',
                'route' => 'reward',
            ),
            'leaderboard' => array(
                'label' => 'Le classement',
                'route' => 'reward/leaderboard',
                'action'     => 'leaderboard'
            ),
        ),
        /*'admin' => array(
            'adfabrewardadmin' => array(
                'label' => 'Actions',
                'route' => 'zfcadmin/adfabrewardadmin/list',
                'resource' => 'reward',
                'privilege' => 'list',
                'pages' => array(
                    'create' => array(
                        'label' => 'New Action',
                        'route' => 'zfcadmin/adfabcmsadmin/pages/list',
                        'resource' => 'reward',
                        'privilege' => 'list',
                    ),
                ),
            ),
        ),*/
    )
);
