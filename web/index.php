<?php
// web/index.php
require_once '../bootstrap.php';
//@todo base directory for loading settings etc

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Silex\Application\MonologTrait;
use Silex\Application\TwigTrait;
use Silex\Application;
use app\provider\YamlConfigServiceProvider;
use lib\geolocation\GeoLocation;
use lib\service\NextBus;
use Predis\Silex\PredisServiceProvider;

class UberApplication extends Application
{
    use Application\MonologTrait;
    use Application\TwigTrait;
}

$app = new UberApplication();
$app['debug'] = true;

$app->register(new YamlConfigServiceProvider(__BASE__ . 'config' . DIRECTORY_SEPARATOR . 'settings.yaml'));
//@todo handle config so that objects in yaml correspond to dot notation
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __BASE__ . $app['config']['monolog']['logfile'],
));
$app->register(new Predis\Silex\PredisServiceProvider(), array(
    'predis.parameters' => $app['config']['predis']['parameters'],
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __BASE__ . $app['config']['twig']['path'],
));


//json decoding
$app->before(function(Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// definitions
$app->get('/', function() use($app) {

    return $app['twig']->render('index.twig');
});

$app->get('/prediction/{latitude}/{longitude}', function(UberApplication $app, $latitude, $longitude) use($app) {

    $location = new lib\geolocation\GeoLocation($latitude, $longitude);

    $stops = $location->getNearbyStops($app['predis'], $app['monolog']);

    //@todo other services
    $serviceConfig = $app['config']['services']['NextBus'];
    $service = new NextBus($serviceConfig['url'], $serviceConfig['key']);
    $predictions = $service->getPrediction($stops, $app['monolog']);

    return $predictions->toJSON();
});

$app->get('/predis', function () use ($app) {
    return var_export($app['predis']->info(), true);
});

//@todo handling for different error types, 404, 503, etc
$app->error(function (\Exception $e, $code) use ($app) {
    $app->log($e->getMessage());
    return new Response('We are sorry, but something went terribly wrong.');
});

$app->run();
