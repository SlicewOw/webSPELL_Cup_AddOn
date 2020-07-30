<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Team;

final class TeamTest extends TestCase
{

    public function testIfCupInstanceCanBeCreated(): void
    {

        $team = new Team();
        $team->setTeamId(1337);
        $team->setName("Test Cup Team Name");

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals(1337, $team->getTeamId(), "Cup team ID is set.");
        $this->assertEquals("Test Cup Team Name", $team->getName(), "Cup team name is set.");

    }

}
