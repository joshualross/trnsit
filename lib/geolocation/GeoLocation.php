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
    const MAX_DISTANCE = 0.005;

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
        //are we alive?
        $predis->select(0);
        $predis->ping();

        $count = 0;
        $distance = 0;
        //create a collection of stops
        $collection = new StopCollection();
        while ($count < 3 && $collection->count() < 2)
        {
            $count++;
            $distance += self::MAX_DISTANCE; //expand the distance searched by max distance
            $result = $predis->pipeline(function($pipe) use($distance) {
                list($low, $high) = $this->getRange($this->latitude, $distance);
                $pipe->zrangebyscore('LAT', $low, $high, array('withscores' => true));
                list($low, $high) = $this->getRange($this->longitude, $distance);
                $pipe->zrangebyscore('LON', $low, $high, array('withscores' => true));
            });

            /*
             * Now we have to compute the intersection and store the lat/lon data
             * it might be easier to duplicate the lat/lon data in the member(stop string)
             * but then we are storing additional data and for these small sets I don't
             * think we are gaining a huge advantage
             */
            $latitudeDataSet = array_shift($result);
            $longitudeDataSet = array_shift($result);

             if (empty($latitudeDataSet) || empty($longitudeDataSet))
                 continue;

            foreach ($latitudeDataSet as $data)
            {
                list($stopString, $latitude) = $data;
                foreach ($longitudeDataSet as $data)
                {
                    list($comparator, $longitude) = $data;
                    if ($stopString == $comparator)
                    {
                        $struct = new StopStruct();
                        $struct->initFromDelimitedData($stopString)
                            ->setCoordinates($latitude, $longitude)
                            ->markSuccess();

                        //order by distance!
                        $collection[$this->getHaversineGreatCircleDistance($latitude, $longitude)] = $struct;

                    }
                }
            }
        }

        return $collection->ksort()->markSuccess();
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

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     *
     * shamelessly taken from http://stackoverflow.com/questions/14750275
     */
    function getHaversineGreatCircleDistance($latitude, $longitude, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}