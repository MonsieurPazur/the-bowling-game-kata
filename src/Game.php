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
     * @var int maximum number of rolls within a game
     */
    const MAX_ROLLS = 20;

    /**
     * @var int number of additional rolls to be provided in case of strike in last frame
     */
    const STRIKE_BONUS_ROLLS = 2;

    /**
     * @var int number of additional rolls to be provided in case of spare in last frame
     */
    const SPARE_BONUS_ROLLS = 1;

    /**
     * @var int number of frames that the game consists of
     */
    const FRAMES = 10;

    /**
     * @var int number of additional rolls provided in case of strike or spare in the last frame
     */
    private $bonusRolls;

    /**
     * @var int index of the current frame
     */
    private $currentFrameIndex;

    /**
     * @var Frame[] collection of Frame objects
     */
    private $frames;

    /**
     * @var Roll|null strike that we apply bonus points from two rolls
     */
    private $twoRollsBonus;

    /**
     * @var Roll|null strike or spare that we apply bonus points from one roll
     */
    private $oneRollBonus;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->bonusRolls = 0;

        $this->twoRollsBonus = null;
        $this->oneRollBonus = null;

        $this->initFrames();
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
        if ($this->isBonusRoll()) {
            $this->bonusRoll($pins);
        } else {
            $this->regularRoll($pins);
        }
    }

    /**
     * Gets current score from all frames.
     *
     * @return int total game score
     */
    public function score(): int
    {
        $score = 0;
        foreach ($this->frames as $frame) {
            $score += $frame->getPoints();
        }
        return $score;
    }

    /**
     * Checks if this roll is supplied by strike or spare in the last frame.
     *
     * @return bool true if this is bonus roll
     */
    private function isBonusRoll(): bool
    {
        return 0 !== $this->bonusRolls;
    }

    /**
     * Bonus rolls are supplied by strike or spare in the last frame, and work only
     * as bonus points providers for strike or spare.
     *
     * @param int $pins number of knocked down pins
     */
    private function bonusRoll(int $pins): void
    {
        $this->makeRoll($pins, true);
        $this->bonusRolls--;
        $this->updateBonusPoints($pins);
    }

    /**
     * Regular roll, not supplied by strike or spare in the last frame.
     *
     * @param int $pins number of knocked down pins
     */
    private function regularRoll(int $pins): void
    {
        $this->updateFrame();
        $this->makeRoll($pins, false);

        // This must be run before checking for new strikes or spares.
        $this->updateBonusPoints($pins);

        if ($this->getCurrentFrame()->isStrike()) {
            $this->strike();
        } elseif ($this->isSpare()) {
            $this->spare();
        }
    }

    /**
     * Keeps track of past rolls and updates their score if there happened to be a strike or spare.
     * In case of strike, we need to keep track of two rolls for bonus points.
     *
     * @param int $pins number of points from knocked down pins to add to previous rolls
     */
    private function updateBonusPoints(int $pins): void
    {
        if (!is_null($this->oneRollBonus)) {
            $this->oneRollBonus->addPoints($pins);
            $this->oneRollBonus = null;
        }
        if (!is_null($this->twoRollsBonus)) {
            $this->twoRollsBonus->addPoints($pins);

            // Two roll bonus becomes one roll, since we need to apply bonus points once more.
            $this->oneRollBonus= $this->twoRollsBonus;
            $this->twoRollsBonus = null;
        }
    }

    /**
     * Checks for next frame
     */
    private function updateFrame(): void
    {
        if ($this->getCurrentFrame()->isDone()) {
            $this->nextFrame();
        }
    }

    /**
     * Helper method for getting index of the last made roll.
     *
     * @return Roll currently made roll
     */
    private function getCurrentRoll(): Roll
    {
        return $this->getCurrentFrame()->getCurrentRoll();
    }

    /**
     * Stores last made roll as a strike (for first out of two bonus point rolls).
     */
    private function strike(): void
    {
        $this->twoRollsBonus = $this->getCurrentRoll();
        if ($this->isLastFrame() && !$this->isBonusRoll()) {
            $this->getCurrentFrame()->addBonusRolls(self::STRIKE_BONUS_ROLLS);
            $this->bonusRolls = self::STRIKE_BONUS_ROLLS;
        }
    }

    /**
     * Checks whether this roll was a spare.
     *
     * @return bool true if this roll was a spare
     */
    private function isSpare(): bool
    {
        return $this->getCurrentFrame()->isSpare();
    }

    /**
     * Stores last made roll as a spare.
     */
    private function spare(): void
    {
        $this->oneRollBonus = $this->getCurrentRoll();
        if ($this->isLastFrame() && !$this->isBonusRoll()) {
            $this->getCurrentFrame()->addBonusRolls(self::SPARE_BONUS_ROLLS);
            $this->bonusRolls = self::SPARE_BONUS_ROLLS;
        }
    }

    /**
     * Checks whether current frame is the last in the game.
     *
     * @return bool true if this is the last frame of the game
     */
    private function isLastFrame(): bool
    {
        return $this->getCurrentFrame()->isLast();
    }

    /**
     * Initializes fixed amount of frames that will be played during game.
     */
    private function initFrames(): void
    {
        for ($i = 0; $i < self::FRAMES - 1; $i++) {
            $this->frames[] = new Frame(false);
        }

        // Last frame is different
        $this->frames[] = new Frame(true);
        $this->currentFrameIndex = 0;
    }

    /**
     * Gets frame, that's currently being played.
     *
     * @return Frame currently played frame
     */
    private function getCurrentFrame(): Frame
    {
        return $this->frames[$this->currentFrameIndex];
    }

    /**
     * Increments current frame reference.
     */
    private function nextFrame(): void
    {
        if ($this->getCurrentFrame()->isLast()) {
            throw new DomainException();
        }
        $this->currentFrameIndex++;
    }

    /**
     * Helper method for creating roll and adding it to current frame
     *
     * @param int $pins amount of pins knocked down
     * @param bool $bonus true if this is bonus roll
     */
    private function makeRoll(int $pins, bool $bonus): void
    {
        $roll = new Roll($pins, $bonus);
        $this->getCurrentFrame()->addRoll($roll);
    }
}
