<?php
use lib\service\Service;
use lib\struct\collection\Stop as StopCollection;
use lib\struct\Stop;

/**
 * Test the lib\service\Service class
 * @author Joshua Ross <joshualross@gmail.com>
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test
     * @test
     * @return type
     */
    public function setAndGetCachedPredictions()
    {
        $predis = new Predis\Client(array(
            'scheme' => 'tcp',
            'host' => 'proxy3.openredis.com',
            'port' => '13034',
            'password' => '7633cf76fa6391aed1b6fa6861cfa3f14affda5306f336e615877a7e3609f33a',
        ));

        $collection = new PredictionCollection();
        $collection[] = new Prediction(array(
            'stopId' => 12345,
            'route' => 24,
            'direction' => '24__OB1',
        ));

        $mock = new ServiceMock('foo.bar', '');
        $mock->setCachedPredictions($collection);
    }
}

/**
 * Service Mock
 * @author Joshua Ross <joshualross@gmail.com>
 */
class ServiceMock extends Service
{
    public function getCachedPredictions(StopCollection $stops)
    {
        return parent::getCachedPredictions($stops);
    }

    public function setCachedPredictions(PredictionCollection $predictions)
    {
        return parent::setCachedPredictions($predictions);
    }
}
