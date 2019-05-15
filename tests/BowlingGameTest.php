<?php

/**
 * Basic test suite for Bowling Game functionalities.
 */

namespace Test;

use App\Game;
use DomainException;
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
     * @param array $input number of pins to knock down in each roll
     * @param int $expected score after a roll
     */
    public function testRolls(array $input, int $expected)
    {
        foreach ($input as $pins) {
            $this->game->roll($pins);
        }
        $this->assertEquals($expected, $this->game->score());
    }

    /**
     * Tests rolling with invalid/incorrect data.
     *
     * @dataProvider invalidRollProvider
     *
     * @param array $input number of pins to knock down in each roll
     * @param string $expected exception class
     */
    public function testInvalidRolls(array $input, string $expected)
    {
        $this->expectException($expected);
        foreach ($input as $pins) {
            $this->game->roll($pins);
        }
    }

    /**
     * Data for rolls that should be correct.
     *
     * @return Generator
     */
    public function rollProvider()
    {
        yield 'single zero' => [[0], 0];
        yield 'single one' => [[1], 1];
        yield 'high rolls across frames' => [[1, 7, 8], 16];
        yield 'strike in first frame' => [[10, 3, 4], 24];
        yield 'spare in first frame' => [[9, 1, 4], 18];
        yield 'spare with ten pins' => [[0, 10, 4, 5], 23];
        yield 'three strikes in a row' => [[10, 10, 10], 60];
        yield 'spare, then strike' => [[5, 5, 10, 6, 3], 48];
        yield 'strike, then all gutter' => [
            [10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            10
        ];
        yield 'strike in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 5, 4],
            19
        ];
        yield 'spare in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 3, 8],
            18
        ];
        yield 'spare, then strike in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 5, 5, 10],
            20
        ];
        yield 'two strikes in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 10, 4],
            24
        ];
        yield 'three strikes in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 10, 10],
            30
        ];
        yield 'perfect game' => [
            [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10],
            300
        ];
    }

    /**
     * Data for invalid/incorrect rolls.
     *
     * @return Generator
     */
    public function invalidRollProvider()
    {
        yield 'negative' => [[-1], InvalidArgumentException::class];
        yield 'over ten' => [[11], InvalidArgumentException::class];
        yield 'over ten in frame' => [[7, 7], DomainException::class];
        yield 'too many rolls' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            DomainException::class
        ];
        yield 'strike, then too many rolls' => [
            [10, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            DomainException::class
        ];
        yield 'four strikes in last frame' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 10, 10, 10],
            DomainException::class
        ];
        yield 'strike in last frame, then too many rolls' => [
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0],
            DomainException::class
        ];
    }
}
