<?php
/*
 * This file does some basic setup used for all the php processes
 */
//@todo how to handle base
define('__BASE__', __DIR__ . DIRECTORY_SEPARATOR);
set_include_path(__BASE__ . PATH_SEPARATOR . get_include_path());


ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

//simple app autoloader
spl_autoload_register(function($class) {
    $filename = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    @include($filename);
});
require_once __BASE__ . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
