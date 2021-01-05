<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamLog;
use myrisk\Cup\TeamMember;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamLogHandler;
use myrisk\Cup\Handler\TeamMemberPositionHandler;

final class TeamLogHandlerTest extends TestCase
{

    /**
     * @var User $user
     */
    private static $user;

    /**
     * @var Team $team
     */
    private static $team;

    public static function setUpBeforeClass(): void
    {

        self::$user = UserHandler::getUserByUserId(1);

        $year = rand(1990, 2036);

        $team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $team_tag = StringFormatterUtils::getRandomString(10);
        $now = new \DateTime($year . "-01-05 12:34:56");

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser(self::$user);
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

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfTeamLogCanBeSaved(): void
    {

        $parent_id = rand(1, 999999);

        $old_count_of_team_logs = count(TeamLogHandler::getTeamLogsByTeam(self::$team));

        $new_team_log = new TeamLog();
        $new_team_log->setInfo("team_joined");
        $new_team_log->setParentId($parent_id);
        $new_team_log->setKickedByUser(self::$user);

        TeamLogHandler::saveTeamLog(self::$team, $new_team_log);

        $team_logs = TeamLogHandler::getTeamLogsByTeam(self::$team);
        $new_count_of_team_logs = count(TeamLogHandler::getTeamLogsByTeam(self::$team));

        $last_team_log = $team_logs[$new_count_of_team_logs - 1];

        $this->assertGreaterThan($old_count_of_team_logs, $new_count_of_team_logs, "Log is saved.");
        $this->assertEquals($parent_id, $last_team_log->getParentId(), "Parent ID is expected.");
        $this->assertEquals(self::$team->getName(), $last_team_log->getTeamName(), "Team name is set.");
        $this->assertEquals(self::$user->getUserId(), $last_team_log->getKickedByUser()->getUserId(), "Kicked by user is set.");

    }

}
