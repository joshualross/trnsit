<?php
namespace lib\struct;

/**
 * Base Struct class
 * @author Joshua Ross <joshualross@gmail.com>
 */
class Struct
{
    /**
     * Success
     * @var boolean
     */
    public $_success = false;

    /**
     * Error
     * @var string
     */
    public $_error = null;


    /**
     * Mark Success
     *
     * @return lib\struct\Struct
     */
    public function markSuccess()
    {
        $this->_success = true;
        return $this;
    }

    /**
     * Constructor
     * @param array $initialValues
     * @return type
     */
    public function __construct(array $initialValues = array())
    {
        foreach ($initialValues as $key => $value)
            $this->{$key} = $value;
    }


    /**
     * Mark Error optionally with message
     * @param string $error
     * @return lib\struct\Struct
     */
    public function markError($error='')
    {
        $this->_success = false;
        $this->_error = $error;
        return $this;
    }

    /**
     * Return the struct as json object
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode(get_object_vars($this));
    }
}