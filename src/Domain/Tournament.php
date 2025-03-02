<?php

namespace App\Domain;

use InvalidArgumentException;
use App\Repository\TournamentRepository;
use App\Repository\PlayerRepository;

class Tournament
{
    private array $players;
    private string $gender;
    private array $history = [];
    private PlayerRepository $playerRepository;
    private TournamentRepository $tournamentRepository;
    private Player $winner;

    private ?int $id;
    private ?int $winner_id;
    private string $created_at;

    public function __construct(array $players, PlayerRepository $PlayerRepository, TournamentRepository $TournamentRepository)
    {

        if (count($players) < 2 || (count($players) & (count($players) - 1)) !== 0) {
            throw new InvalidArgumentException("Number of players must be a power of 2.");
        }

        $this->gender = $players[0]->getGender();
        foreach ($players as $player) {
            if ($player->getGender() !== $this->gender) {
                throw new InvalidArgumentException("All players must be of the same gender.");
            }
        }

        shuffle($players); // Randomize player order
        $this->players = $players;
        $this->playerRepository = $PlayerRepository ?? new PlayerRepository();
        $this->tournamentRepository = $TournamentRepository ?? new TournamentRepository();

    }

    /**
    *
    * start playing the tournament and the games until one player is left
    *
    */
    public function play(): Player
    {
        $tournamentId = null;
        
        $round = 1;
        $currentPlayers = $this->players;

        while (count($currentPlayers) > 1) {
            // echo "🏆 Round {$round} - Players: " . count($currentPlayers) . PHP_EOL;
            $nextRound = [];

            for ($i = 0; $i < count($currentPlayers); $i += 2) {
                $game = new Game($currentPlayers[$i], $currentPlayers[$i + 1]);
                $winner = $game->getWinner();


                $nextRound[] = $winner;

                if (!$tournamentId) {
                    $tournamentId = $this->tournamentRepository->saveTournament($this, $winner);
                    $this->setId($tournamentId);
                }

                $this->tournamentRepository->saveMatch($tournamentId, $round, $currentPlayers[$i], $currentPlayers[$i + 1], $winner);

            }

            $currentPlayers = $nextRound;
            $round++;
        }

        return $currentPlayers[0];
    }


    public function getGender() {
        return $this->gender;
    }

    public function getId() {
        return $this->id;
    }

    public function getWinnerId() {
        return $this->winner_id;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setGender($gender) {
        $this->gender=$gender;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setWinnerId($id) {
        $this->winner_id=$id;
    }

    public function setCreatedAt($date) {
        $this->created_at =$date;
    }

    public function setWinner(Player $player) {
        $this->winner = $player;
    }

    public function getWinner() {
        return $this->winner;
    }


}
