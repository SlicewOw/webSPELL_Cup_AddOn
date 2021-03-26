<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamMemberPositionHandler;
use myrisk\Cup\Utils\TeamUtils;
use webspell_ng\UserSession;

final class TeamUtilsTest extends TestCase
{

    /**
     * @var User $user_admin
     */
    private static $user_admin;

    /**
     * @var User $user_player
     */
    private static $user_player;

    /**
     * @var Team $team
     */
    private static $team;

    public static function setUpBeforeClass(): void
    {

        UserSession::setUserSession(1);

        self::$user_admin = UserHandler::getUserByUserId(1);
        self::$user_player = UserHandler::getUserByUserId(2);

        $now = new \DateTime(rand(1990, 2036) . "-01-05 12:34:56");

        $admin_position = TeamMemberPositionHandler::getAdminPosition();
        $player_position = TeamMemberPositionHandler::getPlayerPosition();

        $team_member_01 = new TeamMember();
        $team_member_01->setUser(self::$user_admin);
        $team_member_01->setPosition($admin_position);
        $team_member_01->setJoinDate($now);
        $team_member_01->setIsActive(true);

        $team_member_02 = new TeamMember();
        $team_member_02->setUser(self::$user_player);
        $team_member_02->setPosition($player_position);
        $team_member_02->setJoinDate($now);
        $team_member_02->setIsActive(true);

        $new_team = new Team();
        $new_team->setName("Test Cup Team " . StringFormatterUtils::getRandomString(10));
        $new_team->setTag(StringFormatterUtils::getRandomString(10));
        $new_team->setCreationDate($now);
        $new_team->setHomepage("https://cup.myrisk-ev.de");
        $new_team->setLogotype("logotype.jpg");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member_01);
        $new_team->addMember($team_member_02);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("at")
        );

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfUserIsMemberOfAnyTeam(): void
    {
        $this->assertTrue(TeamUtils::isUserAnyTeamAdmin(), "User is part of a team.");
    }

    public function testIfUserIsPartOfTheTeamAsAdmin(): void
    {

        UserSession::setUserSession(self::$user_admin->getUserId());

        $this->assertTrue(TeamUtils::isUserAnyTeamMember(), "User is a team member.");
        $this->assertTrue(TeamUtils::isUserAnyTeamAdmin(), "User is a team admin.");
        $this->assertTrue(TeamUtils::isUserTeamMember(self::$team), "User is part of the team!");
        $this->assertTrue(TeamUtils::isUserTeamAdmin(self::$team), "User is the team admin!");

    }

    public function testIfUserIsPartOfTheTeamAsPlayer(): void
    {

        UserSession::setUserSession(self::$user_player->getUserId());

        $this->assertTrue(TeamUtils::isUserAnyTeamMember(), "User is a team member.");
        $this->assertFalse(TeamUtils::isUserAnyTeamAdmin(), "User is not a team admin.");
        $this->assertTrue(TeamUtils::isUserTeamMember(self::$team), "User is part of the team!");
        $this->assertFalse(TeamUtils::isUserTeamAdmin(self::$team), "User is not the team admin!");

    }

    public function testIfOtherUserIsNotPartOfTheTeam(): void
    {

        UserSession::setUserSession(3);

        $this->assertFalse(TeamUtils::isUserAnyTeamMember(), "User is not a team member.");
        $this->assertFalse(TeamUtils::isUserAnyTeamAdmin(), "User is not a team admin.");
        $this->assertFalse(TeamUtils::isUserTeamMember(self::$team), "User is not part of the team!");
        $this->assertFalse(TeamUtils::isUserTeamAdmin(self::$team), "User is not the team admin!");

    }

    public function testIfFalseIsReturnedIfUserIsNotLoggedIn(): void
    {

        UserSession::clearUserSession();

        $this->assertFalse(TeamUtils::isUserAnyTeamAdmin(), "User is not a team admin.");
        $this->assertFalse(TeamUtils::isUserTeamMember(self::$team), "Login is required (01)!");
        $this->assertFalse(TeamUtils::isUserTeamAdmin(self::$team), "Login is required (02)!");

    }

    public static function tearDownAfterClass(): void
    {
        UserSession::clearUserSession();
    }

}
