<?php

namespace App;

/**
 * Class Roll
 * @package App
 */
class Roll
{
    private $points = 0;

    /**
     * Roll constructor.
     * @param $pins
     */
    public function __construct($pins)
    {
        $this->points = $pins;
    }

    /**
     * @param $points
     */
    public function addPoints($points)
    {
        $this->points += $points;
    }

    public function getPoints()
    {
        return $this->points;
    }
}
