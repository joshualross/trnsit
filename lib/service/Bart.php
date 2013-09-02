<?php
namespace lib\service;

/**
 * Bart service
 * @author Joshua Ross <joshualross@gmail.com>
 */
class Bart extends Service
{
    /**
     * @param string $url
     * @return string
     */
    public function appendKey($url)
    {
        return "{$url}?key={$this->key}";
    }
}