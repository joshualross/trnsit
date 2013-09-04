<?php
namespace lib\service;

use lib\struct\Stop;
use lib\struct\collection\Stop as StopCollection;
use lib\struct\Prediction;
use lib\struct\collection\Prediction as PredictionCollection;

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
        $monolog = func_get_arg(1);
        $collection = new PredictionCollection();

        //build an array of get parameters
        $parameters = array();
        foreach ($stops as $stop)
        {
            //check if this is in redis cache already
//             $cached = $this->getCachedPredictions();


            $serviceName = $this->serviceNames[$stop->service];
            if (!isset($parameters[$serviceName]))
                $parameters[$serviceName] = array();

            $parameters[$serviceName][] = "stops={$stop->route}|{$stop->tag}";
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

            foreach ($iterator->predictions as $directions)
            {
                $attributes = array(
                    'service' => $service,
                    'route' => (string)$directions->attributes()->routeTag,
                    'serviceTitle' => (string)$directions->attributes()->agencyTitle,
                    'routeTitle' => (string)$directions->attributes()->routeTitle,
                    'stopTitle' => (string)$directions->attributes()->stopTitle,
                );

                foreach ($directions->direction as $direction)
                {
                    $directionTitle = (string)$direction->attributes()->title;
                    $completed = array();
                    foreach ($direction->prediction as $prediction)
                    {
//@todo threshold on how much time until next bus?
                        $dirTag = (string)$prediction->attributes()->dirTag;
                        if (isset($completed[$dirTag]))
                            continue;

                        $struct = new Prediction($attributes + array(
                        	'directionTitle' => $directionTitle,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                            'seconds' => (string)$prediction->attributes()->seconds,
                            'minutes' => (string)$prediction->attributes()->minutes,
                            'dirTag' => $dirTag,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                        ));

                        //parse intelligent direction
                        $struct->direction = Prediction::DIRECTION_INBOUND;
                        if (false === strpos($struct->dirTag, Prediction::DIRECTION_INBOUND))
                            $struct->direction = Prediction::DIRECTION_OUTBOUND;

                        $collection[] = $struct->markSuccess();
                        $completed[$dirTag] = true;
                    }

                }


            }

            $collection->markSuccess();
        }


        return $collection;
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