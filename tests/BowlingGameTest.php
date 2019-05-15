<?php

/**
 * Basic test suite for Bowling Game functionalities.
 */

namespace Test;

use App\Game;
use PHPUnit\Framework\TestCase;

/**
 * Class BowlingGameTest
 * @package Test
 */
class BowlingGameTest extends TestCase
{
    /**
     * Tests rolling.
     */
    public function testRoll()
    {
        $game = new Game();
        $game->roll();
        $this->assertTrue(true);
    }
}
