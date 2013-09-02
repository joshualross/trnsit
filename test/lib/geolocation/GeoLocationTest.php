<?php
use lib\geolocation\GeoLocation;

/**
 * Test the lib\geolocation\GeoLocation
 * @author Joshua Ross <joshualross@gmail.com>
 */
class GeoLocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Return coordinates
     *
     * @return array
     */
    public function latitudeDataProvider()
    {
        return array(
        	array(
        	    '37.7728915',
        	    '-122.436805',
            ),
        );
    }

    /**
     * Test getNearbyStops
     * @test
     * @param float $latitude
     * @param float $longitude
     * @dataProvider latitudeDataProvider
     */
    public function getNearbyStops($latitude, $longitude)
    {

        $predis = new Predis\Client(array(
            'scheme' => 'tcp',
            'host' => 'proxy3.openredis.com',
            'port' => '13034',
            'password' => '7633cf76fa6391aed1b6fa6861cfa3f14affda5306f336e615877a7e3609f33a',
        ));

        $location = new GeoLocation($latitude, $longitude);
        $result = $location->getNearbyStops($predis);
        print_r($result);
    }

}
