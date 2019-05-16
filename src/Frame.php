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
    private $last;

    /**
     * @var array collection of rolls made within this frame
     */
    private $rolls;

    /**
     * @var int rolls available (to be made) within frame
     */
    private $availableRolls;

    /**
     * Frame constructor.
     *
     * @param bool $last tells if this frame is the last one in the game
     */
    public function __construct(bool $last)
    {
        $this->last = $last;
        $this->availableRolls = self::MAX_ROLLS;

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
     * @param Roll $roll given roll to be added
     */
    public function addRoll(Roll $roll): void
    {
        if (!$this->canRoll($roll)) {
            throw new DomainException();
        }
        $this->rolls[] = $roll;
        if ($this->isStrike()) {
            $this->availableRolls--;
        }
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
        // First we check if there were too many pins knocked down
        if ($this->isLast()) {
            $tooManyPins = false;
        } else {
            $tooManyPins = $this->getPins() + $roll->getPins() > self::MAX_PINS;
        }

        // Then we check if we can roll more within this frame
        $tooManyRolls = count($this->rolls) + 1 > $this->availableRolls;
        return !$tooManyPins && !$tooManyRolls;
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
     * @return Roll
     */
    public function getCurrentRoll(): Roll
    {
        return $this->rolls[count($this->rolls) - 1];
    }
}
