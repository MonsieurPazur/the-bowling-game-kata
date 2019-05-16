<?php

/**
 * This class is the elementary thing that happens during game.
 * Keeps track of points.
 */

namespace App;

use InvalidArgumentException;

/**
 * Class Roll
 * @package App
 */
class Roll
{
    /**
     * @var int minimum number of pins that may be knocked down in a roll
     */
    const MIN_PINS = 0;

    /**
     * @var int maximum number of pins that may be knocked down in a roll
     */
    const MAX_PINS = 10;

    /**
     * @var int amount of points (from knocked down pins and bonus points)
     */
    private $points;

    /**
     * @var bool true if this roll is a bonus one (from spare or strike in the last frame)
     */
    private $bonus;

    /**
     * @var int amount of pins knocked down in this roll
     */
    private $pins;

    /**
     * Roll constructor.
     *
     * @param int $pins pins knocked down by this roll
     * @param bool $bonus true if this roll is a bonus one
     */
    public function __construct(int $pins, bool $bonus)
    {
        $this->validate($pins);

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

    /**
     * Gets amount of pins knocked down in this roll
     *
     * @return int amount of pins knocked down
     */
    public function getPins(): int
    {
        return $this->pins;
    }

    /**
     * Checks if number of pins knocked down is correct.
     *
     * @param int $pins number of pins knocked down
     */
    private function validate(int $pins): void
    {
        if ($pins < self::MIN_PINS) {
            throw new InvalidArgumentException();
        }
        if ($pins > self::MAX_PINS) {
            throw new InvalidArgumentException();
        }
    }
}
