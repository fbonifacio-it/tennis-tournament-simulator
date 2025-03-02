<?php

namespace App\Controller;

use App\Domain\Tournament;
use App\Domain\Player;
use App\Repository\TournamentRepository;
use App\Repository\PlayerRepository;
use Exception;

class TournamentController
{
    private TournamentRepository $tournamentRepository;
    private PlayerRepository $playerRepository;

    public function __construct(PlayerRepository $PlayerRepository=null, TournamentRepository $TournamentRepository=null)
    {
        $this->tournamentRepository = $TournamentRepository ?? new TournamentRepository();
        $this->playerRepository = $PlayerRepository ?? new PlayerRepository();
    }


    /**
    *
    * simulate tournament and return the result
    *
    */
    public function createTournament(array $playersData=[])
    {
        if (count($playersData) < 2) {
            return ['error' => 'At least two players are required'];
        }

        $players = [];

        try {
            foreach ($playersData as $data) {
                $player = new Player(
                    $data['name'] ?? null,
                    $data['skillLevel'] ?? 0,
                    $data['gender'] ?? null,
                    $data['strength'] ?? 0,
                    $data['speed'] ?? 0,
                    $data['reactionTime'] ?? 0
                );

                $id = $this->playerRepository->save($player);

                $player->setId($id);
                
                $players[] = $player;
            }

            $tournament = new Tournament($players, $this->playerRepository, $this->tournamentRepository);

            $winner = $tournament->play();

            return ['tournamentId' => $tournament->getId(), 'winner' => $winner->__toArray()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
    *
    * return an array of a tournament by id
    *
    */
    public function getTournament($id) {
        return $this->tournamentRepository->getTournamentById($id);
    }


    /**
    *
    * return all tournaments data
    *
    */
    public function getTournamentHistory() {
        return $this->tournamentRepository->getTournamentHistory();
    }



    /**
    *
    * search tournament by filters
    *
    */
    public function search(array $filters=[]) {

        // Validate filters
        if (!$filters || !is_array($filters)) {
            return ["error" => "Invalid JSON body"];
        }

        $date = $filters['date'] ?? null;
        $gender = $filters['gender'] ?? null;
        $winnerId = $filters['winnerId'] ?? null;

        // Validate date format
        if ($date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return ["error" => "Invalid date format. Use YYYY-MM-DD."];
        }

        // Validate gender
        if ($gender && !in_array($gender, ["M", "F"])) {
            return ["error" => "Gender must be 'M' or 'F'."];
        }

        return $this->tournamentRepository->getFilteredTournaments($date, $gender, $winnerId);
    }
}
