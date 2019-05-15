<?php

/**
 * This class aggregates rolls and handles strikes and spares.
 */

namespace App;

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
     * @var bool true if this frame is the last one in the game
     */
    private $last;

    /**
     * @var int how many rolls have already been made within this frame
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
     * Increments number of rolls made within this frame.
     */
    public function addRoll(): void
    {
        $this->rolls++;
    }

    /**
     * Checks whether we can roll within this frame.
     *
     * @return bool true if we can roll within this frame
     */
    public function canRoll(): bool
    {
        return $this->rolls <= $this->maxRolls;
    }

    /**
     * Checks whether there was only one roll in this frame.
     *
     * @return bool true if there was only one roll
     */
    public function isFirstRoll(): bool
    {
        return 1 === $this->rolls;
    }
}
