<?php

namespace App\Domain;

class Game
{
    private Player $player1;
    private Player $player2;

    public function __construct(Player $player1, Player $player2)
    {
        if ($player1->getGender() !== $player2->getGender()) {
            throw new \InvalidArgumentException("Players must be of the same gender.");
        }

        $this->player1 = $player1;
        $this->player2 = $player2;
    }



    /**
    *
    * compute winner of a game 1vs1
    *
    */
    public function getWinner(): Player
    {
        $score1 = $this->calculateScore($this->player1);
        $score2 = $this->calculateScore($this->player2);

        if($score1 == $score2) return $this->getWinner(); 

        return ($score1 >= $score2) ? $this->player1 : $this->player2;
    }

    /**
    *
    * compute score by gender and other attributes
    *
    */
    private function calculateScore(Player $player): float
    {
        $luck = rand(0, 100); // Random luck factor
        $baseSkill = $player->getSkillLevel();

        if ($player->getGender() === "M") {
            return $baseSkill + $player->getStrength() + $player->getSpeed() + $luck;
        } else {
            return $baseSkill + $player->getReactionTime() + $luck;
        }
    }
}
