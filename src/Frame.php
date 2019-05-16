<?php

/**
 * This class aggregates rolls and handles strikes and spares.
 */

namespace App;

use DomainException;

/**
 * Class Frame
 * @package App
 */
class Frame
{
    /**
     * @var int how many rolls can be made within regular frame
     */
    const MAX_ROLLS = 2;

    /**
     * @var int maximum number of pins that may be knocked down in a roll
     */
    const MAX_PINS = 10;

    /**
     * @var bool true if this frame is the last one in the game
     */
    protected $last;

    /**
     * @var Roll[] collection of rolls made within this frame
     */
    private $rolls;

    /**
     * @var int rolls available (to be made) within frame
     */
    private $availableRolls;

    /**
     * @var int pins that can be knocked down in this frame
     */
    private $availablePins;

    /**
     * Frame constructor.
     */
    public function __construct()
    {
        $this->last = false;
        $this->availableRolls = self::MAX_ROLLS;
        $this->availablePins = self::MAX_PINS;

        $this->rolls = [];
    }

    /**
     * Checks whether this is the last frame in the game.
     *
     * @return bool true if this frame is the last in the game
     */
    public function isLast(): bool
    {
        return $this->last;
    }

    /**
     * Adds new Roll to this frame.
     *
     * @param int $pins amount of pins knocked down in this roll
     */
    public function addRoll($pins): void
    {
        if ($this->isBonus()) {
            $roll = new Roll($pins, true);
        } else {
            $roll = new Roll($pins, false);
        }
        if (!$this->canRoll($roll)) {
            throw new DomainException();
        }
        $this->rolls[] = $roll;

        $this->handleAvailableRolls();
        $this->handleAvailablePins();
    }

    /**
     * Checks whether we can make specific roll within this frame.
     *
     * @param Roll $roll a roll we want to make
     *
     * @return bool true if we can roll within this frame
     */
    public function canRoll(Roll $roll): bool
    {
        // We can't knock down more pins that there are on the track.
        $validPins = $this->availablePins - $roll->getPins() >= 0;

        // Then we check if we can roll more within this frame
        $validRolls = count($this->rolls) + 1 <= $this->availableRolls;
        return $validPins && $validRolls;
    }

    /**
     * Checks whether there was only one roll in this frame.
     *
     * @return bool true if there was only one roll
     */
    public function isFirstRoll(): bool
    {
        return 1 === count($this->rolls);
    }

    /**
     * Checks whether this frame had a strike.
     *
     * @return bool true if this frame had a strike
     */
    public function isStrike(): bool
    {
        return $this->isFirstRoll() && self::MAX_PINS == $this->rolls[0]->getPins();
    }

    /**
     * Checks whether this frame had a spare.
     *
     * @return bool true if this frame had a spare
     */
    public function isSpare(): bool
    {
        if ($this->isStrike()) {
            return false;
        }
        $pins = $this->getPins();
        return self::MAX_PINS === $pins;
    }

    /**
     * Check whether this frame is done, means we don't roll anymore within it
     *
     * @return bool true if this frame is done
     */
    public function isDone(): bool
    {
        return count($this->rolls) === $this->availableRolls;
    }

    /**
     * Adds bonus rolls to the last frame.
     *
     * @param int $bonusRolls amount of bonus rolls in this frame
     */
    public function addBonusRolls(int $bonusRolls): void
    {
        if (!$this->isLast()) {
            throw new DomainException();
        }
        $this->availableRolls += $bonusRolls;
    }

    /**
     * Gets roll that was just played.
     *
     * @return Roll currenty played roll
     */
    public function getCurrentRoll(): Roll
    {
        return $this->rolls[count($this->rolls) - 1];
    }

    /**
     * Get combine number of points from this frame's rolls.
     *
     * @return int amount of points from all rolls within this frame
     */
    public function getPoints(): int
    {
        $points = 0;
        foreach ($this->rolls as $roll) {
            $points += $roll->getPoints();
        }
        return $points;
    }

    /**
     * Checks if this frame rolls will be counted as bonus points.
     *
     * @return bool true if rolls are bonus
     */
    public function isBonus(): bool
    {
        return $this->availableRolls > self::MAX_ROLLS;
    }

    /**
     * Gets total amount of pins knocked down in all rolls in this frame.
     *
     * @return int total amount of pins knocked down in this frame
     */
    private function getPins(): int
    {
        $pins = 0;
        foreach ($this->rolls as $roll) {
            $pins += $roll->getPins();
        }
        return $pins;
    }

    /**
     * Handle adding or substracting available rolls based on strikes and spares.
     */
    private function handleAvailableRolls(): void
    {
        if ($this->isLast() && !$this->isBonus() && ($this->isStrike() || $this->isSpare())) {
            $this->availableRolls++;
        } else {
            if ($this->isStrike()) {
                $this->availableRolls--;
            }
        }
    }

    /**
     * Keeps track of pins left.
     */
    private function handleAvailablePins(): void
    {
        $this->availablePins -= $this->getCurrentRoll()->getPins();

        // We need to reset pins during last frame, since there is a possibility to knock down
        // all of them and continue within the same frame.
        if ($this->availablePins === 0 && $this->isLast()) {
            $this->availablePins = self::MAX_PINS;
        }
    }
}
