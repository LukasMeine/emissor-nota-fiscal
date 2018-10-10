<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'NotaFiscal\Models'      => $config->application->modelsDir,
    'NotaFiscal\Controllers' => $config->application->controllersDir,
    'NotaFiscal\Forms'       => $config->application->formsDir,
    'NotaFiscal'             => $config->application->libraryDir
]);

$loader->register();

// Use composer autoloader to load vendor classes
require_once BASE_PATH . '/vendor/autoload.php';