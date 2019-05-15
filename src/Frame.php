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

    /**
     * Frame constructor.
     * @param $last
     */
    public function __construct($last)
    {
        $this->last = $last;
    }
}
