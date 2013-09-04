<?php
namespace lib\service;

use lib\struct\collection\Stop as StopCollection;
use lib\struct\collection\Prediction as PredictionCollection;
use lib\service\i\Service as ServiceInterface;

/**
 * Abstract base class for external services
 * @author Joshua Ross <joshualross@gmail.com>
 */
abstract class Service implements ServiceInterface
{
    const SERVICE_MUNI = 1;
    const SERVICE_BART = 2;

    /**
     * API key
     * @var mixed
     */
    protected $key = null;

    /**
     * Base URL
     * @var string
     */
    protected $url = null;


    /**
     * Constructor
     * @param $url
     */
    public function __construct($url, $key)
    {
        $this->url = $url;
        $this->key = $key;
    }

    /**
     * Return data necessary for initialization of redis cache
     * @todo fill in return type
     * @return
     */
    protected abstract function init();

    /**
     * Given an url, return the url with the key
     * @param string $url
     */
    public abstract function appendKey($url);

    //make api call
    /**
     * Perform API call returning the value
     * @param string $url
     * @return string
     */
    protected function api($url)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $data = curl_exec($handle);
        curl_close($handle);
//@todo handle error cases
        return $data;
    }

    /**
     * Return a collection of predictions
     * @param lib\struct\collection\Stop $stops
     * @return lib\struct\collection\Collection
     */
    protected function getCachedPredictions(StopCollection $stops)
    {
        //get from cache, unserialize
        //@todo cache layer for predictions
    }

    /**
     * Set a set of predictions in cache
     * scope param
     * @return type
     */
    protected function setCachedPredictions(PredictionCollection $collection)
    {
        //add to cache, serialize
        //@todo cache layer for predictions
        return $this;
    }


    /**
     * Return a cache key for this prediction
     * @param string $service
     * @param string $stopId
     * @param string $route
     * @param string $dirTag
     * @return string
     */
    protected function getCacheKey($service, $stopId, $route, $dirTag)
    {
        //@todo this might not be a good cache key, had issues
        return implode(':', array($service, $stopId, $route, $dirTag));
    }

    /**
     * Return true if the response from the api is an error
     * @param string $data
     * @return boolean
     */
    protected function isApiError($data)
    {
        if (false !== stripos($data, '<Error'))
            return true;
        return false;
    }

}