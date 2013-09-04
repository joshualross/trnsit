<?php
namespace lib\struct;

/**
 * A Stop Struct
 * @author Joshua Ross <joshualross@gmail.com>
 */
class Prediction extends Struct
{
    const DIRECTION_INBOUND = 'IB';
    const DIRECTION_OUTBOUND = 'OB';

    /**
     * Id
     * @var mixed
     */
    public $id = null;

    /**
     * Stop Id
     * @var string
     */
    public $stopId = null;

    /**
     * Stop Title
     * @var string
     */
    public $stopTitle = null;

    /**
     * Route
     * @var mixed
     */
    public $route = null;

    /**
     * Route Title
     * @var string
     */
    public $routeTitle = null;

    /**
     * Direction
     * @var mixed
     */
    public $dirTag = null;

    /**
     * Direction
     * @var mixed
     */
    public $direction = null;

    /**
     * Direction Title
     * @var string
     */
    public $directionTitle = null;

    /**
     * Service
     * @var mixed
     */
    public $service = null;

    /**
     * Service Title
     * @var string
     */
    public $serviceTitle = null;

    /**
     * Seconds until next bus/train
     * @var integer
     */
    public $seconds = null;

    /**
     * Minutes until next bus/train
     * @var integer
     */
    public $minutes = null;

    /**
     * Timestamp
     * @var integer
     */
    public $timestamp = null;

    /**
     * Return the redis value for this stop
     * @param string $service
     * @return string
     */
    public function asDelimitedData()
    {
        //service:route:direction:stop
        return implode(':', array($this->service, $this->route, $this->direction, $this->id));
    }

    /**
     * Initialize from delimited data
     * @param string $data
     * @return lib\struct\Stop
     */
    public function initFromDelimitedData($data)
    {
        list($this->service, $this->route, $this->direction, $this->id) = explode(':', $data);
        return $this;
    }
}