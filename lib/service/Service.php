<?php
namespace lib\service;

use lib\struct\collection\Stop as StopCollection;

/**
 * Abstract base class for external services
 * @author Joshua Ross <joshualross@gmail.com>
 */
abstract class Service
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
     * @return type
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
     * Given a collection of stops, get the predictions
     * @param lib\struct\collection\Stop $stops
     * @return
     */
    public abstract function getPrediction(StopCollection $stops);

    //get routes?
    //get route prediction for stop


    /**
     * Given an url, return the url with the key
     * @param string $url
     */
    public abstract function appendKey($url);

    //make api call
    /**
     * Perform API call returning the value
     * @param string $url
     * @return type
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


}