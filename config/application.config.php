<?php

$modules = [
    'Zend\Router',
    'GeebyDeeby',
    'GeebyDeebyLocal',
];
if (PHP_SAPI == 'cli') {
    $modules[] = 'Zend\Mvc\Console';
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
