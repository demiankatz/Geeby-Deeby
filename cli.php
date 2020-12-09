<?php
// Set up autoloading
require __DIR__ . '/vendor/autoload.php';

// Set up Laminas
$app = Laminas\Mvc\Application::init(require 'config/application.config.php');

// Run command line tools
return $app->getServiceManager()->get(GeebyDeebyConsole\ConsoleRunner::class)->run();