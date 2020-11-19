<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Team;
use \myrisk\Cup\TeamMember;
use \myrisk\Cup\TeamMemberPosition;

use \webspell_ng\User;

final class TeamTest extends TestCase
{

    public function testIfCupInstanceCanBeCreated(): void
    {

        $now = new \DateTime();

        $position = new TeamMemberPosition();
        $position->setPositionId(666);
        $position->setPosition("Admin");
        $position->setSort(10);

        $team_member = new TeamMember();
        $team_member->setMemberId(1234);
        $team_member->setUser(new User());
        $team_member->setPosition($position);
        $team_member->setJoinDate($now);
        $team_member->setLeftDate($now);
        $team_member->setKickId(666);
        $team_member->setIsActive(true);

        $team = new Team();
        $team->setTeamId(1337);
        $team->setName("Test Cup Team Name");
        $team->setTag("Test Team Tag");
        $team->setCreationDate($now);
        $team->setCountry("de");
        $team->setHomepage("https://gaming.myrisk-ev.de");
        $team->setLogotype("logotype");
        $team->setIsDeleted(true);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals(1337, $team->getTeamId(), "Cup team ID is set.");
        $this->assertEquals("Test Cup Team Name", $team->getName(), "Cup team name is set.");
        $this->assertEquals("Test Team Tag", $team->getTag(), "Cup team tag is set.");
        $this->assertEquals($now, $team->getCreationDate(), "Cup team creation date is set.");
        $this->assertEquals("de", $team->getCountry(), "Cup team country is set.");
        $this->assertEquals("https://gaming.myrisk-ev.de", $team->getHomepage(), "Cup team homepage is set.");
        $this->assertEquals("logotype", $team->getLogotype(), "Cup team logogtype is set.");
        $this->assertTrue($team->isDeleted(), "Cup team is deleted.");

        $this->assertNull($team->getTeamAdmin(), "Cup team admin is not set yet.");

        $team->addMember($team_member);

        $team_admin = $team->getTeamAdmin();
        $this->assertNotNull($team_admin, "Cup team admin is set.");
        $this->assertEquals(1234, $team_admin->getMemberId(), "Member id is set.");
        $this->assertInstanceOf(User::class, $team_admin->getUser());
        $this->assertEquals($now, $team_admin->getJoinDate(), "Member join date is set.");
        $this->assertEquals($now, $team_admin->getLeftDate(), "Member left date is set.");
        $this->assertEquals(666, $team_admin->getKickId(), "Member kick id is set.");
        $this->assertTrue($team_admin->isActive(), "Member is active.");

        $team_admin_position = $team_admin->getPosition();
        $this->assertInstanceOf(TeamMemberPosition::class, $team_admin_position);
        $this->assertEquals(666, $team_admin_position->getPositionId(), "Member position id is set.");
        $this->assertEquals("Admin", $team_admin_position->getPosition(), "Member position name is set.");
        $this->assertEquals(10, $team_admin_position->getSort(), "Member position sort is set.");

    }

}
