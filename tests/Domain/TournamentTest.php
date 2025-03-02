<?php

use PHPUnit\Framework\TestCase;
use App\Domain\Player;
use App\Domain\Tournament;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;

class TournamentTest extends TestCase
{
    private PlayerRepository $mockPlayerRepository;
    private TournamentRepository $mockTournamentRepository;

    protected function setUp(): void
    {
        // Create a mock of the TournamentRepository
        $this->mockPlayerRepository = $this->createMock(PlayerRepository::class);

  

        $this->mockTournamentRepository = $this->createMock(TournamentRepository::class);
        $this->mockTournamentRepository->method('saveTournament')->willReturn(random_int(0, 100));
        $this->mockTournamentRepository->method('saveMatch')->willReturn(null);



    }


    public function testTournamentRunsSuccessfully()
    {
        $players = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("Beth", 85, "F", null, null, 75),
            new Player("Claire", 78, "F", null, null, 70),
            new Player("Diana", 88, "F", null, null, 85)
        ];


        $tournament = new Tournament($players, $this->mockPlayerRepository, $this->mockTournamentRepository);
        $winner = $tournament->play();

        $this->assertInstanceOf(Player::class, $winner);
        $this->assertContains($winner->getName(), ["Alice", "Beth", "Claire", "Diana"]);
    }

    public function testTournamentFailsWithInvalidPlayerCount()
    {
        $this->expectException(InvalidArgumentException::class);

        $players = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("Beth", 85, "F", null, null, 75),
            new Player("Claire", 78, "F", null, null, 70)
        ];

        new Tournament($players, $this->mockPlayerRepository, $this->mockTournamentRepository); // Should throw an exception
    }

    public function testTournamentFailsWithMixedGenders()
    {
        $this->expectException(InvalidArgumentException::class);

        $players = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("John", 85, "M", 80, 70)
        ];

        new Tournament($players, $this->mockPlayerRepository, $this->mockTournamentRepository); // Should throw an exception
    }

    public function testNoMixedGenderMatches()
    {
        $malePlayers = [
            new Player("John", 85, "M", 70, 80, null),
            new Player("Mike", 80, "M", 75, 78, null)
        ];

        $femalePlayers = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("Beth", 85, "F", null, null, 75)
        ];

        $maleTournament = new Tournament($malePlayers, $this->mockPlayerRepository, $this->mockTournamentRepository);
        $femaleTournament = new Tournament($femalePlayers, $this->mockPlayerRepository, $this->mockTournamentRepository);

        $maleWinner = $maleTournament->play();
        $femaleWinner = $femaleTournament->play();

        $this->assertEquals("M", $maleWinner->getGender());
        $this->assertEquals("F", $femaleWinner->getGender());
    }

    public function testEmptyTournamentThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $tournament = new Tournament([], $this->mockPlayerRepository, $this->mockTournamentRepository);
        $tournament->play();
    }

    public function testWinnerHasValidAttributes()
    {
        $players = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("Beth", 85, "F", null, null, 75)
        ];

        $tournament = new Tournament($players, $this->mockPlayerRepository, $this->mockTournamentRepository);
        $winner = $tournament->play();

        $this->assertGreaterThanOrEqual(0, $winner->getSkillLevel());
        $this->assertLessThanOrEqual(100, $winner->getSkillLevel());
        $this->assertEquals("F", $winner->getGender());
    }

    public function testCreatingFemalePlayerWithoutReactionTimeThrowsError()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Reaction time is missing
        new Player("Beth", 90, "F", null, null, null);
    }

    public function testCreatingMalePlayerWithoutStrengthOrSpeedThrowsError()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Strength is missing
        new Player("Mike", 85, "M", null, 80, null);
    }

    public function testFemalePlayerRequiresReactionTime()
    {
        $player = new Player("Alice", 90, "F", null, null, 80);

        $this->assertEquals("F", $player->getGender());
        $this->assertNotNull($player->getReactionTime());
        $this->assertNull($player->getStrength());
        $this->assertNull($player->getSpeed());
    }

    public function testMalePlayerRequiresStrengthAndSpeed()
    {
        $player = new Player("John", 85, "M", 70, 80, null);

        $this->assertEquals("M", $player->getGender());
        $this->assertNotNull($player->getStrength());
        $this->assertNotNull($player->getSpeed());
        $this->assertNull($player->getReactionTime());
    }

    public function testTournamentHasNoTies()
    {
        // Create players (all Female or all Male)
        $players = [
            new Player("Alice", 90, "F", null, null, 80),
            new Player("Beth", 85, "F", null, null, 75),
            new Player("Claire", 78, "F", null, null, 70),
            new Player("Diana", 88, "F", null, null, 85)
        ];

        // Create tournament
        $tournament = new Tournament($players, $this->mockPlayerRepository, $this->mockTournamentRepository);

        // Play the tournament
        $winner = $tournament->play();

        // Ensure a winner is selected (not null)
        $this->assertInstanceOf(Player::class, $winner);
        $this->assertNotNull($winner);

        // Ensure the winner is from the original list
        $playerIds = array_map(fn($p) => $p->getId(), $players);
        $this->assertContains($winner->getId(), $playerIds);
    }


}
