<?php

/**
 * This class is the elementary thing that happens during game.
 * Keeps track of points.
 */

namespace App;

/**
 * Class Roll
 * @package App
 */
class Roll
{
    /**
     * @var int amount of points (from knocked down pins and bonus points)
     */
    private $points;

    /**
     * Roll constructor.
     *
     * @param int $pins pins knocked down by this roll
     */
    public function __construct(int $pins)
    {
        $this->points = $pins;
    }

    /**
     * Adds bonus points from strikes and spares
     *
     * @param int $points amount of bonus points
     */
    public function addPoints(int $points): void
    {
        $this->points += $points;
    }

    /**
     * Gets current amount of points from this roll
     *
     * @return int amount of points
     */
    public function getPoints(): int
    {
        return $this->points;
    }
}
