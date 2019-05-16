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
     * @var bool true if this roll is a bonus one (from spare or strike in the last frame)
     */
    private $bonus;

    private $pins;

    /**
     * Roll constructor.
     *
     * @param int $pins pins knocked down by this roll
     * @param bool $bonus true if this roll is a bonus one
     */
    public function __construct(int $pins, bool $bonus)
    {
        $this->pins = $pins;
        $this->bonus = $bonus;
        if ($bonus) {
            $this->points = 0;
        } else {
            $this->points = $pins;
        }
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

    public function getPins(): int
    {
        return $this->pins;
    }
}
