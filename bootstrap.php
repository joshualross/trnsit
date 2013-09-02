<?php
/*
 * This file does some basic setup used for all the php processes
 */
//@todo how to handle base
$base = __DIR__ . DIRECTORY_SEPARATOR;
set_include_path($base . PATH_SEPARATOR . get_include_path());


ini_set('error_reporting', E_ALL);
// ini_set('error_log', 'error.log');
ini_set('display_errors', 'stderr');

//simple app autoloader
spl_autoload_register(function($class) {
    $filename = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    @include(DIRECTORY_SEPARATOR . $filename);
});
require_once $base . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
