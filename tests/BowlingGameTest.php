<?php

/**
 * Basic test suite for Bowling Game functionalities.
 */

namespace Test;

use App\Game;
use InvalidArgumentException;
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
        $game->roll(0);
        $this->assertEquals(0, $game->score());

        $game->roll(1);
        $this->assertEquals(1, $game->score());
    }

    public function testInvalidRoll()
    {
        $game = new Game();
        $this->expectException(InvalidArgumentException::class);
        $game->roll(-1);
    }
}
