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
     * LastFrame constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->last = true;
    }
}
