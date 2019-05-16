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
     * @var int rolls available (to be made) within frame
     */
    protected $availableRolls;

    /**
     * @var int pins that can be knocked down in this frame
     */
    protected $availablePins;

    /**
     * @var Roll[] collection of rolls made within this frame
     */
    private $rolls;

    /**
     * Frame constructor.
     */
    public function __construct()
    {
        $this->availableRolls = self::MAX_ROLLS;
        $this->availablePins = Roll::MAX_PINS;

        $this->rolls = [];
    }

    /**
     * Adds new Roll to this frame.
     *
     * @param int $pins amount of pins knocked down in this roll
     */
    public function addRoll($pins): void
    {
        $roll = $this->createRoll($pins);
        if (!$this->canRoll($roll)) {
            throw new DomainException();
        }
        $this->rolls[] = $roll;

        $this->handleAvailableRolls();
        $this->handleAvailablePins();
    }

    /**
     * Checks whether this frame had a strike.
     *
     * @return bool true if this frame had a strike
     */
    public function isStrike(): bool
    {
        // Only one roll in frame (so first) and maximum amount of pins knocked down
        return 1 === count($this->rolls) && Roll::MAX_PINS === $this->rolls[0]->getPins();
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
        return Roll::MAX_PINS === $this->getPins();
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
     * Creates roll with given pins knocked down.
     *
     * @param int $pins amount of pins knocked down in this roll
     *
     * @return Roll roll that was created
     */
    protected function createRoll(int $pins): Roll
    {
        return new Roll($pins);
    }

    /**
     * Handle adding or substracting available rolls based on strikes and spares.
     */
    protected function handleAvailableRolls(): void
    {
        if ($this->isStrike()) {
            $this->availableRolls--;
        }
    }

    /**
     * Keeps track of pins left.
     */
    protected function handleAvailablePins(): void
    {
        $this->availablePins -= $this->getCurrentRoll()->getPins();
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
     * Checks whether we can make specific roll within this frame.
     *
     * @param Roll $roll a roll we want to make
     *
     * @return bool true if we can roll within this frame
     */
    private function canRoll(Roll $roll): bool
    {
        // We can't knock down more pins that there are on the track.
        $validPins = $this->availablePins - $roll->getPins() >= 0;

        // Then we check if we can roll more within this frame
        $validRolls = count($this->rolls) + 1 <= $this->availableRolls;
        return $validPins && $validRolls;
    }
}
