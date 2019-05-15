<?php

/**
 * Basic test suite for Bowling Game functionalities.
 */

namespace Test;

use App\Game;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class BowlingGameTest
 * @package Test
 */
class BowlingGameTest extends TestCase
{
    /**
     * @var Game
     */
    private $game;

    /**
     * Sets up Game object for tests.
     */
    public function setUp(): void
    {
        $this->game = new Game();
    }

    /**
     * Tests rolling.
     *
     * @dataProvider rollProvider
     *
     * @param int $input number of pins to knock down in a roll
     * @param int $expected score after a roll
     */
    public function testRolls(int $input, int $expected)
    {
        $this->game->roll($input);
        $this->assertEquals($expected, $this->game->score());
    }

    /**
     * Tests rolling with invalid/incorrect data.
     *
     * @dataProvider invalidRollProvider
     *
     * @param int $input number of pins to knock down in a roll
     * @param string $expected exception class
     */
    public function testInvalidRolls(int $input, string $expected)
    {
        $this->expectException($expected);
        $this->game->roll($input);
    }

    /**
     * Data for rolls that should be correct.
     *
     * @return Generator
     */
    public function rollProvider()
    {
        yield 'single zero' => [0, 0];
        yield 'single one' => [1, 1];
    }

    /**
     * Data for invalid/incorrect rolls.
     *
     * @return Generator
     */
    public function invalidRollProvider()
    {
        yield 'negative' => [-1, InvalidArgumentException::class];
    }
}
