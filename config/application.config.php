<?php

$modules = [
    'Laminas\Router',
    'GeebyDeeby',
];
if (PHP_SAPI == 'cli') {
    $modules[] = 'GeebyDeebyConsole';
}
return [
    'modules' => $modules,
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
