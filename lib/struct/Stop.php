<?php
namespace lib\struct;

/**
 * A Stop Struct
 * @author Joshua Ross <joshualross@gmail.com>
 */
class Stop extends Struct
{
    /**
     * Id
     * @var mixed
     */
    public $id = null;

    /**
     * Stop tag
     * @var mixed
     */
    public $tag = null;

    /**
     * Route
     * @var mixed
     */
    public $route = null;

    /**
     * Direction
     * @var mixed
     */
    public $direction = null;

    /**
     * Service
     * @var mixed
     */
    public $service = null;

    /**
     * Latitude
     * @var float
     */
    public $latitude = null;

    /**
     * Longitude
     * @var float
     */
    public $longitude = null;

    /**
     * Return the redis value for this stop
     * @param string $service
     * @return string
     */
    public function asDelimitedData()
    {
        //service:route:direction:stop
        return implode(':', array($this->service, $this->route, $this->direction, $this->tag, $this->id));
    }

    /**
     * Initialize from delimited data
     * @param string $data
     * @return lib\struct\Stop
     */
    public function initFromDelimitedData($data)
    {
        list($this->service, $this->route, $this->direction, $this->tag, $this->id) = explode(':', $data);
        return $this;
    }

    /**
     * Set the coordinates of this stop
     * @param float $latitude
     * @param float $longitude
     * @return lib\struct\Strop
     */
    public function setCoordinates($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        return $this;
    }
}