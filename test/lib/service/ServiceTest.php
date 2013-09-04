<?php
use lib\service\Service;
use lib\struct\collection\Stop as StopCollection;
use lib\struct\collection\Prediction as PredictionCollection;
use lib\struct\Stop;

/**
 * Test the lib\service\Service class
 * @author Joshua Ross <joshualross@gmail.com>
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * A data ... ahem... Data Provider
     * @return array
     */
    public function dataDataProvider()
    {
        return array(
        	array(
        	    '<predictions stopTitle="Civic Center Station Outbound" routeCode="1" routeTitle="N - Judah">' .
        	    '<prediction seconds="218" minutes="3" epochTime="1229637162309" isDeparture="false" />' .
        	    ' <prediction seconds="976" minutes="16" epochTime="122963716923" isDeparture="false" />' .
        	    '</predictions>',
        	    false
            ),
            array(
                '<body><Error shouldRetry="true">Agency server blah blah blah</Error></body>',
                true,
            ),
        );
    }

    /**
     * Test
     * @test
     * @dataProvider dataDataProvider
     */
    public function isApiError($data, $expected)
    {
        $service = new ServiceMock();
        $this->assertEquals($expected, $service->isApiError($data));
    }
}

/**
 * Service Mock - create a basic, testable, sublcass
 * @author Joshua Ross <joshualross@gmail.com>
 */
class ServiceMock extends Service
{
    public function __construct()
    {
        return parent::__construct('foo', 'bar');
    }
    protected function init()
    {
    }

    public function isApiError($data)
    {
        return parent::isApiError($data);
    }

    public function appendKey($url)
    {
        return $url;
    }

    public function getPrediction(StopCollection $stops)
    {
    }

    public function getCachedPredictions(StopCollection $stops)
    {
        return parent::getCachedPredictions($stops);
    }

    public function setCachedPredictions(PredictionCollection $predictions)
    {
        return parent::setCachedPredictions($predictions);
    }
}
