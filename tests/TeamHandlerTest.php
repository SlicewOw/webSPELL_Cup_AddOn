<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Team;
use \myrisk\Cup\TeamMember;
use \myrisk\Cup\TeamMemberPosition;
use \myrisk\Cup\Enum\TeamEnums;
use \myrisk\Cup\Handler\TeamHandler;
use \myrisk\Cup\Handler\TeamMemberPositionHandler;

use \webspell_ng\User;
use \webspell_ng\Handler\CountryHandler;
use \webspell_ng\Handler\UserHandler;
use \webspell_ng\Utils\StringFormatterUtils;


final class TeamHandlerTest extends TestCase
{

    public function testIfTeamCanBeSavedAndUpdated(): void
    {

        $user = UserHandler::getUserByUserId(1);

        $year = rand(1990, 2036);

        $team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $team_tag = StringFormatterUtils::getRandomString(10);
        $now = new \DateTime($year . "-01-05 12:34:56");

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser($user);
        $team_member->setPosition($position);
        $team_member->setJoinDate($now);
        $team_member->setIsActive(true);

        $new_team = new Team();
        $new_team->setName($team_name);
        $new_team->setTag($team_tag);
        $new_team->setCreationDate($now);
        $new_team->setHomepage("https://gaming.myrisk-ev.de");
        $new_team->setLogotype("logotype");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        $team = TeamHandler::saveTeam($new_team);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertGreaterThan(0, $team->getTeamId(), "Cup team ID is set.");
        $this->assertEquals($team_name, $team->getName(), "Cup team name is set.");
        $this->assertEquals($team_tag, $team->getTag(), "Cup team tag is set.");
        $this->assertEquals($now, $team->getCreationDate(), "Cup team creation date is set.");
        $this->assertEquals("Germany", $team->getCountry()->getName(), "Cup team country is set.");
        $this->assertEquals("de", $team->getCountry()->getShortcut(), "Cup team country is set.");
        $this->assertEquals("https://gaming.myrisk-ev.de", $team->getHomepage(), "Cup team homepage is set.");
        $this->assertEquals("logotype", $team->getLogotype(), "Cup team logogtype is set.");
        $this->assertFalse($team->isDeleted(), "Cup team is deleted.");
        $this->assertNotEmpty($team->getPassword(), "Password is set per default.");

        $team_admin = $team->getTeamAdmin();
        $this->assertNotNull($team_admin, "Cup team admin is set.");
        $this->assertGreaterThan(0, $team_admin->getMemberId(), "Member id is set.");
        $this->assertInstanceOf(User::class, $team_admin->getUser());
        $this->assertEquals($now, $team_admin->getJoinDate(), "Member join date is set.");
        $this->assertNull($team_admin->getLeftDate(), "Member left date is set.");
        $this->assertEquals(0, $team_admin->getKickId(), "Member kick id is set.");
        $this->assertTrue($team_admin->isActive(), "Member is active.");

        $team_admin_position = $team_admin->getPosition();
        $this->assertInstanceOf(TeamMemberPosition::class, $team_admin_position);
        $this->assertGreaterThan(0, $team_admin_position->getPositionId(), "Member position id is set.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_ADMIN, $team_admin_position->getPosition(), "Member position name is set.");
        $this->assertEquals(1, $team_admin_position->getSort(), "Member position sort is set.");

        $changed_team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $changed_team_tag = StringFormatterUtils::getRandomString(10);

        $changed_team = TeamHandler::getTeamByTeamId($team->getTeamId());
        $changed_team->setName($changed_team_name);
        $changed_team->setTag($changed_team_tag);

        $updated_team = TeamHandler::saveTeam($changed_team);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertGreaterThan(0, $updated_team->getTeamId(), "Cup team ID is set.");
        $this->assertEquals($changed_team_name, $updated_team->getName(), "Cup team name is set.");
        $this->assertNotEquals($team->getName(), $updated_team->getName(), "Cup team name is updated.");
        $this->assertEquals($changed_team_tag, $updated_team->getTag(), "Cup team tag is set.");
        $this->assertNotEquals($team->getTag(), $updated_team->getTag(), "Cup team tag is updated.");
        $this->assertEquals($now, $updated_team->getCreationDate(), "Cup team creation date is set.");
        $this->assertEquals("Germany", $updated_team->getCountry()->getName(), "Cup team country is set.");
        $this->assertEquals("de", $updated_team->getCountry()->getShortcut(), "Cup team country is set.");
        $this->assertEquals("https://gaming.myrisk-ev.de", $updated_team->getHomepage(), "Cup team homepage is set.");
        $this->assertEquals("logotype", $updated_team->getLogotype(), "Cup team logogtype is set.");
        $this->assertFalse($updated_team->isDeleted(), "Cup team is deleted.");
        $this->assertNotEmpty($updated_team->getPassword(), "Password is set per default.");

        $this->assertGreaterThan(0, count(TeamHandler::getTeamsOfUser($user)), "User is member of team/s.");

    }

    public function testIfTeamsAreReturned(): void
    {

        $cup_teams = TeamHandler::getAllActiveTeams();

        $any_team_is_deleted = false;
        $any_team_is_admin_team = false;

        foreach ($cup_teams as $cup_team) {

            if ($cup_team->isDeleted()) {
                $any_team_is_deleted = true;
            }

            if ($cup_team->isAdminTeam()) {
                $any_team_is_admin_team = true;
            }

        }

        $this->assertFalse($any_team_is_deleted, "No team of the list of active teams is deleted.");
        $this->assertFalse($any_team_is_admin_team, "No team of the list of active teams is an admin team.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfTeamIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamHandler::getTeamByTeamId(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfTeamDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        TeamHandler::getTeamByTeamId(9999999);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfNoTeamAdminIsPresent(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamHandler::saveTeam(new Team());

    }

}
