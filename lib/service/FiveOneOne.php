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





    //get routes?
    //get route prediction for stop


    //make api call


}