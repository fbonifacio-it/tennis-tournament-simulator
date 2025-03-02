<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Player;
use App\Domain\Game;

class GameTest extends TestCase
{
    public function testGameDeterminesAWinner()
    {
        $player1 = new Player("John Doe", 80, "M", 70, 60); // Strength and Speed for males
        $player2 = new Player("Mike Tyson", 85, "M", 90, 70); // Another male player

        $game = new Game($player1, $player2);
        $winner = $game->getWinner();

        $this->assertInstanceOf(Player::class, $winner);
        $this->assertTrue($winner === $player1 || $winner === $player2);
    }

    public function testGameCannotBeCreatedWithDifferentGenders()
    {
        $this->expectException(\InvalidArgumentException::class);

        $malePlayer = new Player("John Doe", 80, "M", 70, 60);
        $femalePlayer = new Player("Jane Doe", 85, "F", 50);

        new Game($malePlayer, $femalePlayer); // This should throw an exception
    }
}
