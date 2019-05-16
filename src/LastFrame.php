<?php

namespace App;

/**
 * Class LastFrame
 * @package App
 */
class LastFrame extends Frame
{

    public function __construct()
    {
        parent::__construct();
        $this->last = true;
    }
}
