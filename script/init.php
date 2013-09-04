<?php
require_once '../bootstrap.php';

use Predis\Client;
use Symfony\Component\Yaml\Yaml;
use lib\service\NextBus;
use lib\service\Bart;
use lib\service\FiveOneOne;

$config = Yaml::parse(__BASE__ . 'config' . DIRECTORY_SEPARATOR . 'settings.yaml');
foreach (array('NextBus'/*, 'Bart'*/) as $service)
{
    $serviceConfig = $config['services'][$service];
    $class = "{$service}Ext";
    $service = new $class($serviceConfig['url'], $serviceConfig['key']);
    $results = $service->doInit();
}

$predis = new Predis\Client(array(
    'scheme' => 'tcp',
    'host' => 'proxy3.openredis.com',
    'port' => '13034',
    'password' => '7633cf76fa6391aed1b6fa6861cfa3f14affda5306f336e615877a7e3609f33a',
));

$predis->select(0);
// $predis->flushdb();
$response = $predis->pipeline(function($pipe) use ($results) {
    $pipe->ping();
    foreach ($results as $result) //should be 2
    {
        foreach ($result as $geoLocationPart => $geoLocationData)
        {
            foreach ($geoLocationData as $location => $key)
            {
                $pipe->zadd($geoLocationPart, $location, $key);
            }
        }
    }
});

print_r($response);


//our class extensions
class NextBusExt extends NextBus
{
    public function doInit()
    {
        return $this->init();
    }
}
class BartExt extends Bart
{
    public function doInit()
    {
        return $this->init();
    }
}
class FiveOneOneExt extends FiveOneOne
{
    public function doInit()
    {
        return $this->init();
    }
}
