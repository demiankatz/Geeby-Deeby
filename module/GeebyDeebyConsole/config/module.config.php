<?php
namespace GeebyDeebyConsole\Module\Configuration;

$config = [
    'service_manager' => [
        'factories' => [
            'GeebyDeebyConsole\Command\PluginManager' => 'GeebyDeeby\ServiceManager\AbstractPluginManagerFactory',
            'GeebyDeebyConsole\ConsoleRunner' => 'GeebyDeebyConsole\ConsoleRunnerFactory',
        ],
    ],
    'geeby-deeby' => [
        'plugin_managers' => [
            'command' => [ /* see GeebyDeebyConsole\Command\PluginManager for defaults */ ],
        ],
    ],
];

return $config;
