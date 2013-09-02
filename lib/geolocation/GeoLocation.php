<?php
namespace lib\geolocation;
use Predis\Client as PredisClient;
use lib\struct\collection\Stop as StopCollection;
use lib\struct\Stop as StopStruct;

/**
 * A rather crude geolocation library
 * @author Joshua Ross <joshualross@gmail.com>
 */
class GeoLocation
{
    //roughly 1/4 mile in sf bay area
    const MAX_DISTANCE = 0.002;

    /**
     * Latitude for this location
     * @var float
     */
    protected $latitude = null;

    /**
     * Longitude for this location
     * @var float
     */
    protected $longitude = null;

    /**
     * Constructor
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


    /**
     * getNearbyStops
     *
     * I'm such a rookie here.  Basically we calculate a roughly
     * half mile square around the users location by adding-to
     * and subtracting from each piece of the coordinate.  Because
     * in this small of a rectangle, the world is more or less flat
     * I didn't worry too much about the curviture of the earth and
     * the convergence of latitude.
     *
     * Obviously a square is sub-optimal, but I felt it was
     * the more performant for this process.
     *
     *
     * This algorithm will retry with larger and larger squares until we
     * have a reasonable amount of stops or the maximum tries has finished
     *
     *
     * @param Predis\Client $predis
     * @return lib\struct\collection\Collection
     */
    public function getNearbyStops(PredisClient $predis)
    {
//@todo support expanding the search radius
        $predis->select(0);
        $predis->ping();
        //@todo lots of room for optimization
        $intersectResult = array();
        $count = 0;
        $distance = 0;
        while ($count < 3 && count($intersectResult) < 2)
        {
            $count++;
            $distance += self::MAX_DISTANCE; //expand the distance searched by max distance
            $result = $predis->pipeline(function($pipe) use($distance) {
                list($low, $high) = $this->getRange($this->latitude, $distance);
                $pipe->zrangebyscore('LAT', $low, $high);
                list($low, $high) = $this->getRange($this->longitude, $distance);
                $pipe->zrangebyscore('LON', $low, $high);
            });

            $latitudeData = array_shift($result);
            $longitudeData = array_shift($result);
//@todo add the lon+lat to the struct
//@todo one stop per route
            if (!empty($latitudeData) && !empty($longitudeData))
                $intersectResult = array_intersect($latitudeData, $longitudeData);

        }
        //it would be nice to store the lat+lon but we get it again in the prediction

        //create a collection of stops
        $collection = new StopCollection();
        foreach ($intersectResult as $key => $stop)
        {
            $struct = new StopStruct();
            $collection[] = $struct->initFromDelimitedData($stop)->markSuccess();
        }

        return $collection->markSuccess();
    }


    /**
     * Return a min and max longitude or latitude given a point to center around
     *
     * This is very generic, convergence of longitude would need to be calculated
     * to get an accurate distance
     *
     * @param float $value
     * @param float $distance  maximum distance from current longitude/latitiude
     * @return array
     */
    protected function getRange($value, $distance=self::MAX_DISTANCE)
    {
        return array($value - $distance, $value + $distance);
    }
}