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
     * Adds bonus rolls to the last frame.
     *
     * @param int $bonusRolls amount of bonus rolls in this frame
     */
    public function addBonusRolls(int $bonusRolls): void
    {
        $this->availableRolls += $bonusRolls;
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
            $this->availablePins = self::MAX_PINS;
        }
    }
}
