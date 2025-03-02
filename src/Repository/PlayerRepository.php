<?php

namespace App\Repository;

use App\Domain\Player;
use App\Infrastructure\Database;
use PDO;

class PlayerRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
    *
    * insert player data into the database using a Player Object
    *
    */
    public function save(Player $player): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO players (name, gender, skill_level, strength, speed, reaction_time)
            VALUES (:name, :gender, :skill, :strength, :speed, :reaction)
            ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
        ");

        $stmt->execute([
            'name' => $player->getName(),
            'gender' => $player->getGender(),
            'skill' => $player->getSkillLevel(),
            'strength' => $player->getStrength(),
            'speed' => $player->getSpeed(),
            'reaction' => $player->getReactionTime()
        ]);

        $id=$this->db->lastInsertId();
        $player->setId($id);

        return $id;
    }

}
