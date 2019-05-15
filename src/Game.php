<?php

/**
 * Basic class for Bowling Game functionality.
 * Takes care of rolling bolls and keeps track of score.
 */

namespace App;

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
     * @var int total score from all rolls and bonuses
     */
    private $score = 0;

    /**
     * Method for rolling ball and knocking down pins.
     *
     * @param int $pins number of knocked down pins
     */
    public function roll(int $pins): void
    {
        $this->validateRoll($pins);
        $this->score += $pins;
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
     */
    private function validateRoll(int $pins): void
    {
        if ($pins < self::MIN_PINS) {
            throw new InvalidArgumentException();
        }
        if ($pins > 10) {
            throw new InvalidArgumentException();
        }
    }
}
