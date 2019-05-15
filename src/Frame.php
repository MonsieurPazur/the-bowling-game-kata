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
    private $last;

    private $rolls = 0;

    private $maxRolls = 2;

    /**
     * Frame constructor.
     * @param $last
     */
    public function __construct($last)
    {
        $this->last = $last;
        if ($this->last) {
            $this->maxRolls = 3;
        }
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

    public function addRoll(): void
    {
        $this->rolls++;
    }

    /**
     * @return bool
     */
    public function canRoll(): bool
    {
        return $this->rolls <= $this->maxRolls;
    }
}
