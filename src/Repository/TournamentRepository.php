<?php

namespace App\Repository;

use App\Domain\Tournament;
use App\Domain\Player;
use App\Infrastructure\Database;
use PDO;

class TournamentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
    *
    * insert tournament and return id
    *
    */
    public function saveTournament(Tournament $tournament, Player $winner): int
    {
        $stmt = $this->db->prepare("INSERT INTO tournaments (gender, winner_id) VALUES (:gender, :winner_id)");
        $stmt->execute([
            'gender' => $tournament->getGender(),
            'winner_id' => $winner->getId()
        ]);

        return $this->db->lastInsertId();
    }

    /**
    *
    * insert game and return id
    *
    */
    public function saveMatch(int $tournamentId, int $round, Player $player1, Player $player2, Player $winner)
    {
        $stmt = $this->db->prepare("
            INSERT INTO games (tournament_id, round_number, player1_id, player2_id, winner_id)
            VALUES (:tournament_id, :round, :player1, :player2, :winner_id)
        ");
        $stmt->execute([
            'tournament_id' => $tournamentId,
            'round' => $round,
            'player1' => $player1->getId(),
            'player2' => $player2->getId(),
            'winner_id' => $winner->getId()
        ]);
        return $this->db->lastInsertId();
    }

    /**
    *
    * fetch all tournament data by id and return and array
    *
    */
    public function getTournamentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tournaments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $tournamentFetch = $stmt->fetch();

        if (!$tournamentFetch) return null;

        $response = [];

        $stmt = $this->db->prepare("SELECT * FROM games WHERE tournament_id = :id");
        $stmt->execute(['id' => $id]);
        $gamesFetch = $stmt->fetchAll();

        $response['tournament'] = $tournamentFetch;

        foreach($gamesFetch as $gameData) {
            $stmt = $this->db->prepare("SELECT * FROM players WHERE id IN (:player1, :player2)");
            $stmt->execute(['player1' => $gameData['player1_id'],'player2' => $gameData['player2_id']]);
            $playersFetch = $stmt->fetchAll();

            foreach($playersFetch as $playerData) {
                if($playerData['id'] == $gameData['winner_id']) {
                  $playerData['winner'] = true;    
                }else {
                  $playerData['winner'] = false;    
                }

                $players[]=$playerData;
            }    

            $gameData['players'] = $players;
            $response['games'][] = $gameData;
        }

        return $response;
    }

    /**
    *
    * fetch all tournaments data and return and array
    *
    */
    public function getTournamentHistory() {
        $stmt = $this->db->prepare("SELECT * FROM tournaments WHERE 1");
        $stmt->execute();
        $tournamentsFetch = $stmt->fetchAll();

        foreach($tournamentsFetch as $tournamentData) {
            $stmt = $this->db->prepare("SELECT * FROM games WHERE tournament_id = :id");
            $stmt->execute(['id' => $tournamentData['id']]);
            $gamesFetch = $stmt->fetchAll();

            foreach($gamesFetch as $gameData) {
                $stmt = $this->db->prepare("SELECT * FROM players WHERE id IN (:player1, :player2)");
                $stmt->execute(['player1' => $gameData['player1_id'],'player2' => $gameData['player2_id']]);
                $playersFetch = $stmt->fetchAll();

                foreach($playersFetch as $playerData) {
                    if($playerData['id'] == $gameData['winner_id']) {
                      $playerData['winner'] = true;    
                    }else {
                      $playerData['winner'] = false;    
                    }

                    $players[]=$playerData;
                }    

                $gameData['players'] = $players;
                $tournamentData['games'][] = $gameData;
            }

            $response['tournaments'][] = $tournamentData;
        }

        return $response;
    }

    /**
    *
    * find tournament by filters and return an array
    *
    */
    public function getFilteredTournaments(?string $date, ?string $gender, ?int $winnerId): array
    {
        $query = "SELECT * FROM tournaments WHERE 1=1";
        $params = [];

        if ($date) {
            $query .= " AND DATE(created_at) = :date";
            $params['date'] = $date;
        }

        if ($gender) {
            $query .= " AND gender = :gender";
            $params['gender'] = $gender;
        }

        if ($winnerId) {
            $query .= " AND winner_id = :winnerId";
            $params['winnerId'] = $winnerId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
