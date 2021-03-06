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
    public const MIN_PINS = 0;

    /**
     * @var int maximum number of pins that may be knocked down in a roll
     */
    public const MAX_PINS = 10;

    /**
     * @var int amount of points (from knocked down pins and bonus points)
     */
    protected $points;

    /**
     * @var int amount of pins knocked down in this roll
     */
    private $pins;

    /**
     * Roll constructor.
     *
     * @param int $pins pins knocked down by this roll
     */
    public function __construct(int $pins)
    {
        $this->validate($pins);

        $this->pins = $pins;
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
            throw new InvalidArgumentException("Can't roll less than " . self::MIN_PINS . ' pins');
        }
        if ($pins > self::MAX_PINS) {
            throw new InvalidArgumentException("Can't roll more than " . self::MAX_PINS . ' pins');
        }
    }
}
