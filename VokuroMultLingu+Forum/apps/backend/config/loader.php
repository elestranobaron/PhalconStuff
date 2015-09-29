<?php
$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(array(
    'Test\Backend\Models' => $config->application->modelsDir,
    'Test\Backend\Controllers' => $config->application->controllersDir,
    'Test\Backend\Forms' => $config->application->formsDir,
    'Test\Backend' => $config->application->libraryDir
));

//$loader->register();

// Use composer autoloader to load vendor classes
require_once __DIR__ . '/../../../vendor/autoload.php';
