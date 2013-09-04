<?php
namespace lib\service\i;

use lib\struct\collection\Stop as StopCollection;
use Predis\Client as PredisClient;

/**
 * Interface for services
 * @author Joshua Ross <joshualross@gmail.com>
 */
interface Service
{
    /**
     * Given a collection of stops, get the predictions
     * @param lib\struct\collection\Stop $stops
     * //@todo handle the predis client better uggggh
     * @return lib\struct\collection\Prediction
     */
    public function getPrediction(StopCollection $stops, PredisClient $predis);

}