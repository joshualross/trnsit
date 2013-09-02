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
     * Initialization of the data set
     *
     * @return
     */
    public function init()
    {
        $service = self::SERVICE_MUNI;

        //omfg xml parsing is the worst ever
        //so what are we doing here?  we basically get all the routes for the service,
        //then make separate api calls for each service because we can't be guaranteed
        //to get all routes/data in one call.  We also might need to throttle this

        $results = array();

        //get the routes
        $url = $this->appendKey($this->url . '?command=routeList&a=sf-muni');
        $data = $this->api($url);
        $iterator = new \SimpleXMLIterator($data);
        $routeIds = array();
        foreach ($iterator->route as $route)
        {
            $routeId = (string)$route->attributes()->tag;
            $routeIds[$routeId] = $routeId;
        }

//for testing
// $routeIds = array('N', '21', '6', '71');

        foreach ($routeIds as $routeId)
        {

            $result = array('LON' => array(), 'LAT' => array());
            $count = 0;

            do
            {
                if ($count > 0)
                    sleep(10); //might need to do exponential backoff
                $url = $this->appendKey($this->url . '?command=routeConfig&a=sf-muni&r=' . $routeId);
                $data = $this->api($url);

            } while (++$count < 3 && false !== stripos($data, '<Error'));

            //fail
            if (false !== stripos($data, '<Error'))
            {
//                 print_r($data);
                throw new \Exception('Unable to get route data from service');
            }

            $iterator = new \SimpleXMLIterator($data);
            $route = (string)$iterator->route->attributes()->tag;
            $stops = array();

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
                            'direction' => $direction,
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
            sleep(5);
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
//         $collection =

        $monolog = func_get_arg(1);

$monolog->addRecord(200, __METHOD__);

        //build an array of get parameters
        $parameters = array();
        foreach ($stops as $stop)
        {
            $serviceName = $this->serviceNames[$stop->service];
            if (!isset($parameters[$serviceName]))
                $parameters[$serviceName] = array();

            $parameters[$serviceName][] = "stops={$stop->route}|{$stop->tag}";
        }

        $collection = new PredictionCollection();

        //get the routes
        foreach ($parameters as $service => $stops)
        {
            $url = $this->url . '?command=predictionsForMultiStops&a=' . $service . '&' . implode('&', $stops);
            $url = $this->appendKey($url);

            $data = $this->api($url);


            //if we are loading predictions, we want them to be fast, don't retry
            if (false !== stripos($data, '<Error'))
            {
                return $collection->markError('Service is down');
            }

            //create prediction structs assigning them to the collection
            $iterator = new \SimpleXMLIterator($data);
//             $monolog->addRecord(200, print_r($iterator, true));

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
                    foreach ($direction->prediction as $prediction)
                    {
//@todo threshold on how much time until next bus?

                        $collection[] = new Prediction($attributes + array(
                        	'directionTitle' => $directionTitle,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                            'seconds' => (string)$prediction->attributes()->seconds,
                            'minutes' => (string)$prediction->attributes()->minutes,
                            'direction' => (string)$prediction->attributes()->dirTag,
                            'timestamp' => (string)$prediction->attributes()->epochTime,
                        ));
                    }

                }


            }


            return $collection->markSuccess();
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