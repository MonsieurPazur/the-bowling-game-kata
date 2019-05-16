<?php

/**
 * This basicaly works like Frame, but with different validation logic.
 */

namespace App;

/**
 * Class LastFrame
 * @package App
 */
class LastFrame extends Frame
{
    /**
     * Creates roll with given pins knocked down. Handles bonus rolls.
     *
     * @param int $pins amount of pins knocked down in this roll
     *
     * @return Roll roll that was created
     */
    protected function createRoll(int $pins): Roll
    {
        if ($this->isBonus()) {
            return new BonusRoll($pins);
        } else {
            return new Roll($pins);
        }
    }

    /**
     * Handle adding or substracting available rolls based on strikes and spares.
     */
    protected function handleAvailableRolls(): void
    {
        if (!$this->isBonus() && ($this->isStrike() || $this->isSpare())) {
            $this->availableRolls++;
        }
    }

    /**
     * Keeps track of pins left.
     */
    protected function handleAvailablePins(): void
    {
        parent::handleAvailablePins();

        // We need to reset pins during last frame, since there is a possibility to knock down
        // all of them and continue within the same frame.
        if ($this->availablePins === 0) {
            $this->availablePins = Roll::MAX_PINS;
        }
    }

    /**
     * Checks if this frame rolls will be counted as bonus points.
     *
     * @return bool true if rolls are bonus
     */
    private function isBonus(): bool
    {
        return $this->availableRolls > self::MAX_ROLLS;
    }
}
