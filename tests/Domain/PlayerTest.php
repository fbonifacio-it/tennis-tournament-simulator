<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Player;

class PlayerTest extends TestCase
{
    public function testValidPlayerCreation()
    {
        $player = new Player("Alice", 90, "F", null, null, 80);

        $this->assertEquals("Alice", $player->getName());
        $this->assertEquals(90, $player->getSkillLevel());
        $this->assertEquals("F", $player->getGender());
        $this->assertEquals(80, $player->getReactionTime());
    }

    public function testInvalidSkillLevelThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Player("Bob", 150, "M", 90, 80, null); // Invalid skill level
    }

    public function testInvalidGenderThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Player("Charlie", 80, "X", 70, 60, null); // Invalid gender
    }

    public function testAttributesRange()
    {
        $player = new Player("Diana", 0, "F", null, null, 100);

        $this->assertGreaterThanOrEqual(0, $player->getSkillLevel());
        $this->assertLessThanOrEqual(100, $player->getSkillLevel());

        $this->assertGreaterThanOrEqual(0, $player->getReactionTime());
        $this->assertLessThanOrEqual(100, $player->getReactionTime());
    }
}
