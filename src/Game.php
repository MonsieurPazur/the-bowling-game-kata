<?php

/**
 * Basic class for Bowling Game functionality.
 * Takes care of rolling bolls and keeps track of score.
 */

namespace App;

/**
 * Class Game
 * @package App
 */
class Game
{
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
}
