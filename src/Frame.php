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
     * @var int how many rolls can be made within last frame
     */
    const MAX_ROLLS_LAST = 3;

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
     * @var int how many rolls can be made within this frame
     */
    private $maxRolls;

    /**
     * Frame constructor.
     *
     * @param bool $last tells if this frame is the last one in the game
     */
    public function __construct(bool $last)
    {
        $this->last = $last;
        $this->maxRolls = $last ? self::MAX_ROLLS_LAST : self::MAX_ROLLS;

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
        if (!$this->canRoll()) {
            throw new DomainException();
        }
        $this->rolls[] = $roll;
    }

    /**
     * Checks whether we can roll within this frame.
     *
     * @return bool true if we can roll within this frame
     */
    public function canRoll(): bool
    {
        return count($this->rolls) + 1 <= $this->maxRolls;
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
     * @return bool
     */
    public function isSpare(): bool
    {
        if ($this->isStrike()) {
            return false;
        }
        $pins = 0;
        foreach ($this->rolls as $roll) {
            $pins += $roll->getPins();
        }
        return self::MAX_PINS === $pins;
    }
}
