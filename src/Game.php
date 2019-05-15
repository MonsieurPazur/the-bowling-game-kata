<?php

/**
 * Basic class for Bowling Game functionality.
 * Takes care of rolling bolls and keeps track of score.
 */

namespace App;

use DomainException;
use InvalidArgumentException;

/**
 * Class Game
 * @package App
 */
class Game
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
     * @var int total score from all rolls and bonuses
     */
    private $score;

    /**
     * @var int value (pins knocked down) of previous roll
     */
    private $previous;

    private $rollCount = 0;

    public function __construct()
    {
        $this->score = 0;
        $this->previous = 0;
    }

    /**
     * Method for rolling ball and knocking down pins.
     *
     * @param int $pins number of knocked down pins
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function roll(int $pins): void
    {
        $this->validateRoll($pins);
        $this->score += $pins;
        $this->rollCount++;

        if ($this->rollCount % 2 === 0) {
            $this->previous = 0;
        } else {
            $this->previous = $pins;
        }
    }

    /**
     * Gets current score from all rolls.
     *
     * @return int total game score
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * Checks whether correct number of pins were knocked down.
     *
     * @param int $pins number of knocked down pins
     *
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    private function validateRoll(int $pins): void
    {
        if ($pins < self::MIN_PINS) {
            throw new InvalidArgumentException();
        }
        if ($pins > self::MAX_PINS) {
            throw new InvalidArgumentException();
        }
        if ($pins + $this->previous > self::MAX_PINS) {
            throw new DomainException();
        }
    }
}
