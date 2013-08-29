<?php
// web/index.php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

// definitions
$app->get('/', function() use($app) {
   return 'hello';
});

$app->run();
