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
     * @var int number of rolls per frame
     */
    const ROLLS_PER_FRAME = 2;

    /**
     * @var int maximum number of rolls within a game
     */
    const MAX_ROLLS = 20;

    /**
     * @var int total score from all rolls and bonuses
     */
    private $score;

    /**
     * @var int value (pins knocked down) of previous roll
     */
    private $previousRoll;

    /**
     * @var array keeps track of scores in specific rolls (including bonus points)
     */
    private $rolls;

    /**
     * @var int|null index of a roll to which we apply first bonus points from strike
     */
    private $strikeFirstBonus;

    /**
     * @var int|null index of a roll to which we apply second bonus points from strike
     */
    private $strikeSecondBonus;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->score = 0;
        $this->previousRoll = 0;

        $this->rolls = [];

        $this->strikeFirstBonus = null;
        $this->strikeSecondBonus = null;
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

        $this->rolls[] = $pins;

        $this->updateStrikes($pins);
        $this->updatePrevious($pins);
    }

    /**
     * Gets current score from all rolls.
     *
     * @return int total game score
     */
    public function score(): int
    {
        $score = 0;
        foreach ($this->rolls as $points) {
            $score += $points;
        }
        return $score;
    }

    /**
     * Checks whether correct number of pins were knocked down.
     * Also checks if we can roll further.
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
        if ($pins + $this->previousRoll > self::MAX_PINS) {
            throw new DomainException();
        }
        if (self::MAX_ROLLS === $this->getRollCount()) {
            throw new DomainException();
        }
    }

    /**
     * Keeps track of past rolls and updates their score if there happened to be a strike.
     * We need to keep track of two rolls for bonus points.
     *
     * @param int $pins number of points from knocked down pins to add to previous rolls
     */
    private function updateStrikes(int $pins): void
    {
        if (!is_null($this->strikeSecondBonus)) {
            $this->rolls[$this->strikeSecondBonus] += $pins;
            $this->strikeSecondBonus = null;
        }
        if (!is_null($this->strikeFirstBonus)) {
            $this->rolls[$this->strikeFirstBonus] += $pins;
            $this->strikeSecondBonus= $this->strikeFirstBonus;
            $this->strikeFirstBonus = null;
        }
    }

    /**
     * Updates previous roll's knocked down pins.
     *
     * @param int $pins pins knocked down in current roll to be updated
     */
    private function updatePrevious(int $pins): void
    {
        // If we reach frame end, we reset previous roll.
        if (0 === $this->getRollCount() % self::ROLLS_PER_FRAME || 10 === $pins) {
            $this->previousRoll = 0;
        } else {
            $this->previousRoll = $pins;
        }

        // If there was strike, we store this roll's index.
        if (self::MAX_PINS === $pins) {
            $this->strikeFirstBonus = $this->getLastRollIndex();
        }
    }

    /**
     * Helper method for getting number of rolls made so far.
     *
     * @return int rolls made so far
     */
    private function getRollCount(): int
    {
        return count($this->rolls);
    }

    /**
     * Helper method for getting index of the last made roll.
     *
     * @return int index of last made roll
     */
    private function getLastRollIndex(): int
    {
        return array_keys($this->rolls)[count($this->rolls) - 1];
    }
}
