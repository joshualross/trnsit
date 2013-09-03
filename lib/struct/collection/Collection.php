<?php
namespace lib\struct\collection;

use lib\struct\Struct;
use \IteratorAggregate;
use \Countable;
use \ArrayAccess;
use \ArrayIterator;

/**
 * Base Collection object, collection of structs
 * @author Joshua Ross <joshualross@gmail.com>
 */
class Collection extends Struct implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Holds structs
     * @var array
     */
    public $_rows = array();

    /**
     * @param mixed $offset
     * @return lib\struct\Struct
     */
    public function offsetExists($offset)
    {
        if (isset($this->_rows[$offset]))
            return true;

        return false;
    }

    /**
     * @param offset
     * @return lib\struct\Struct
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset))
            return $this->_rows[$offset];

        return null;
    }

    /**
     * @param mixed $offset
     * @param lib\struct\Struct $value
     * @return lib\struct\collection\Collection
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset)
            $this->_rows[] = $value;
        else
            $this->_rows[$offset] = $value;
        return $this;
    }

    /**
     * @param offset
     * @return lib\struct\collection\Collection
     */
    public function offsetUnset ($offset)
    {
    	unset($this->_rows[$offset]);
        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_rows);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->_rows);
    }

    /**
     * Return the keys of the collection
     * @return array
     */
    public function keys()
    {
        return array_keys($this->_rows);
    }

    /**
     * Return the struct as json object
     *
     * @return string
     */
    public function toJSON()
    {
        return json_encode($this->_rows);
    }

    /**
     * Sort the collection by keys
     * @return lib\struct\collection\Collection
     */
    public function ksort()
    {
        ksort($this->_rows);
        return $this;
    }
}