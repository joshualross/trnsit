<?php
namespace lib\service;

/**
 * 511.org service
 * @author Joshua Ross <joshualross@gmail.com>
 */
class FiveOneOne extends Service
{
    /**
     * @param string $url
     * @return string
     */
    public function appendKey($url)
    {
        return "{$url}?token={$this->key}";
    }

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
        throw new \Exception('Not implemented');
    }
}