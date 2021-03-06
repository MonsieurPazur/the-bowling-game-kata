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
     * @var int number of frames that the game consists of
     */
    public const FRAMES = 10;

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
        $this->updateFrame();
        $this->getCurrentFrame()->addRoll($pins);

        // This must be run before checking for new strikes or spares.
        $this->updateBonusPoints($pins);
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
     * Initializes fixed amount of frames that will be played during game.
     */
    private function initFrames(): void
    {
        for ($i = 0; $i < self::FRAMES - 1; $i++) {
            $this->frames[] = new Frame();
        }

        // Last frame is different.
        $this->frames[] = new LastFrame();

        // Mark first frame, as the current one.
        $this->currentFrameIndex = 0;
    }

    /**
     * Keeps track of past rolls and updates their score if there happened to be a strike or spare.
     * In case of strike, we need to keep track of two rolls for bonus points.
     *
     * @param int $pins number of points from knocked down pins to add to previous rolls
     */
    private function updateBonusPoints(int $pins): void
    {
        if (null !== $this->oneRollBonus) {
            $this->oneRollBonus->addPoints($pins);
            $this->oneRollBonus = null;
        }

        if (null !== $this->twoRollsBonus) {
            $this->twoRollsBonus->addPoints($pins);

            // $twoRollsBonus becomes $oneRollBonus, since we need to apply bonus points once more (on the next roll).
            $this->oneRollBonus = $this->twoRollsBonus;
            $this->twoRollsBonus = null;
        }

        // Next we look for strikes and spares, so we can store references to those.
        if ($this->getCurrentFrame()->isStrike()) {
            $this->twoRollsBonus = $this->getCurrentRoll();
        } elseif ($this->getCurrentFrame()->isSpare()) {
            $this->oneRollBonus = $this->getCurrentRoll();
        }
    }

    /**
     * Checks for next frame.
     */
    private function updateFrame(): void
    {
        if ($this->getCurrentFrame()->isDone()) {
            if ($this->getCurrentFrame() instanceof LastFrame) {
                throw new DomainException("Can't go pass last frame.");
            }
            $this->currentFrameIndex++;
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
     * Gets frame, that's currently being played.
     *
     * @return Frame currently played frame
     */
    private function getCurrentFrame(): Frame
    {
        return $this->frames[$this->currentFrameIndex];
    }
}
