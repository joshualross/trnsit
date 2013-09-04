<?php
namespace lib\service;

use lib\struct\Stop;
use lib\struct\collection\Stop as StopCollection;
use lib\struct\Prediction;
use lib\struct\collection\Prediction as PredictionCollection;
use Predis\Client as PredisClient;

/**
 * NextBus service
 * @author Joshua Ross <joshualross@gmail.com>
 */
class NextBus extends Service
{
    /**
     * Service Names
     * @var array
     */
    protected $serviceNames = array(
        self::SERVICE_MUNI => 'sf-muni',
    );

    /**
     * Initialization of the data set - this is only called from cli script, it is intended to populate the database
     * from the nextbus service
     *
     * this method is purposefully protected so that a potentiall caller must first extend this class, then
     * implement a method that will call init.
     *
     * @return array
     */
    protected function init()
    {
        $service = self::SERVICE_MUNI;

        //omfg xml parsing is the worst ever
        //so what are we doing here?  we basically get all the routes for the service,
        //then make separate api calls for each service because we can't be guaranteed
        //to get all routes/data in one call.

        $results = array();

        //get the routes
        $url = $this->appendKey($this->url . '?command=routeList&a=sf-muni');
        $data = $this->api($url);

        //fail
        if ($this->isApiError($data))
            throw new \Exception('Unable to get routes from service');

        $iterator = new \SimpleXMLIterator($data);
        $routeIds = array();
        foreach ($iterator->route as $route)
        {
            $routeId = (string)$route->attributes()->tag;
            $routeIds[$routeId] = $routeId;
        }

        foreach ($routeIds as $routeId)
        {

            $count = 0;
            do
            {
                //nextbus has limits, so retry a few times if we hit an error, just in case we hit a limit
                if ($count > 0)
                    sleep(10); //might need to do exponential backoff
                $url = $this->appendKey($this->url . '?command=routeConfig&a=sf-muni&r=' . $routeId);
                $data = $this->api($url);

            } while (++$count < 3 && $this->isApiError($data));

            //fail
            if ($this->isApiError($data))
                throw new \Exception('Unable to get route data from service');

            $iterator = new \SimpleXMLIterator($data);
            $route = (string)$iterator->route->attributes()->tag;
            $stops = array();
            $result = array('LON' => array(), 'LAT' => array());

            foreach ($iterator->route->direction as $routeDirection)
            {
                $direction = (string)$routeDirection->attributes()->tag;
                $stops[$direction] = array();
                foreach ($routeDirection->stop as $stop)
                {
                    $stopTag = (string)$stop->attributes()->tag;
                    $stops[$direction][$stopTag] = $stopTag;
                }
            }

            //now add these stops to redis using their lon and lat
            foreach ($iterator->route->stop as $routeStop)
            {
                $stopTag = (string)$routeStop->attributes()->tag;

                foreach ($stops as $direction => $directionStops)
                {
                    if (isset($directionStops[$stopTag]))
                    {
                        $struct = new Stop(array(
                            'id' => (string)$routeStop->attributes()->stopId, //get the real stop id
                            'tag' => $stopTag,
                            'service' => $service,
                            'dirTag' => $direction,
                            'route' => $route,
                        ));

                        //insert into result set
                        $lon = (string)$routeStop->attributes()->lon;
                        $lat = (string)$routeStop->attributes()->lat;

                        $value = $struct->asDelimitedData();
                        $result['LON'][$lon] = $value;
                        $result['LAT'][$lat] = $value;
                    }
                }
            }

            $results[] = $result;
        }

        return $results;
    }

    /**
     * Return a collection of predictions for given stops
     * @param lib\struct\collection\Stop
     * @return lib\struct\collection\Prediction
     */
    public function getPrediction(StopCollection $stops)
    {
        $collection = new PredictionCollection();

        //build an array of get parameters
        $parameters = array();
        $stopIds = array();
        foreach ($stops as $stop)
        {
            $serviceName = $this->serviceNames[$stop->service];
            if (!isset($parameters[$serviceName]))
                $parameters[$serviceName] = array();

            $direction = Prediction::DIRECTION_INBOUND;
            if (false === strpos($stop->direction, Prediction::DIRECTION_INBOUND))
                $direction = Prediction::DIRECTION_OUTBOUND;

            //stopid,route,direction
            $parameters[$serviceName][$stop->id . $stop->route . $direction] = "stops={$stop->route}|{$stop->tag}";
            //create a stopTag => $stopId data store, we use this for the predictions
            $stopIds[$stop->tag] = $stop->id;

        }

        //get the routes from each service
        foreach ($parameters as $service => $stops)
        {
            $url = $this->url . '?command=predictionsForMultiStops&a=' . $service . '&' . implode('&', $stops);
            $url = $this->appendKey($url);

            $data = $this->api($url);

            //if we are loading predictions, we want them to be fast, don't retry
            if ($this->isApiError($data))
                return $collection->markError('Service is down');

            //create prediction structs assigning them to the collection
            $iterator = new \SimpleXMLIterator($data);
            $completed = array();
            foreach ($iterator->predictions as $directions)
            {
                $attributes = array(
                    'service' => $service,
                    'route' => (string)$directions->attributes()->routeTag,
                    'serviceTitle' => (string)$directions->attributes()->agencyTitle,
                    'routeTitle' => (string)$directions->attributes()->routeTitle,
                    'stopTitle' => (string)$directions->attributes()->stopTitle,
                );

                $stopTag = (string)$directions->attributes()->stopTag;
                if (isset($stopIds[$stopTag]))
                    $attributes['stopId'] = $stopIds[$stopTag];

                foreach ($directions->direction as $direction)
                {
                    $directionTitle = (string)$direction->attributes()->title;
                    foreach ($direction->prediction as $prediction)
                    {
                        $dirTag = (string)$prediction->attributes()->dirTag;
                        //parse intelligent direction
                        $direction = Prediction::DIRECTION_INBOUND;
                        if (false === strpos($dirTag, Prediction::DIRECTION_INBOUND))
                            $direction = Prediction::DIRECTION_OUTBOUND;

                        $struct = new Prediction($attributes + array(
                        	'directionTitle' => $directionTitle,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                            'seconds' => (string)$prediction->attributes()->seconds,
                            'minutes' => (string)$prediction->attributes()->minutes,
                            'dirTag' => $dirTag,
                            'direction' => $direction,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                        ));

                        //one stop per route direction
                        if (!isset($completed[$attributes['route'] . $direction]))
                            $collection[] = $struct->markSuccess();

                        $completed[$attributes['route'] . $direction] = true;
                    }
                }
            }
        }

        return $collection->markSuccess();
    }

    /**
     * @param string $url
     * @return string
     */
    public function appendKey($url)
    {
        return $url;
    }



}