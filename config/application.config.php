<?php
return [
    'modules' => [
        'Zend\Router',
        'GeebyDeeby',
        'GeebyDeebyLocal',
    ],
    'module_listener_options' => [
        'config_glob_paths'    => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            './module',
            './vendor',
        ],
    ],
];
