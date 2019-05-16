<?php

/**
 * Bonus rolls behave similar to regular, but exist only to provide bonus points
 * to strikes and spares in last frame. They don't have points of their own.
 */

namespace App;

/**
 * Class BonusRoll
 * @package App
 */
class BonusRoll extends Roll
{
    /**
     * Roll constructor.
     *
     * @param int $pins pins knocked down by this roll
     */
    public function __construct(int $pins)
    {
        parent::__construct($pins);
        $this->points = 0;
    }
}
