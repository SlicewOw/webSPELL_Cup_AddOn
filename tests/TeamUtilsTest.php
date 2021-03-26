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
     * @var User $user
     */
    private static $user;

    /**
     * @var Team $team
     */
    private static $team;

    public static function setUpBeforeClass(): void
    {

        UserSession::setUserSession(1);

        self::$user = UserHandler::getUserByUserId(1);

        $now = new \DateTime(rand(1990, 2036) . "-01-05 12:34:56");

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser(self::$user);
        $team_member->setPosition($position);
        $team_member->setJoinDate($now);
        $team_member->setIsActive(true);

        $new_team = new Team();
        $new_team->setName("Test Cup Team " . StringFormatterUtils::getRandomString(10));
        $new_team->setTag(StringFormatterUtils::getRandomString(10));
        $new_team->setCreationDate($now);
        $new_team->setHomepage("https://cup.myrisk-ev.de");
        $new_team->setLogotype("logotype.jpg");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("at")
        );

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfUserIsPartOfTheTeam(): void
    {

        $this->assertTrue(TeamUtils::isUserTeamMember(self::$team), "User is part of the team!");
        $this->assertTrue(TeamUtils::isUserTeamAdmin(self::$team), "User is the team admin!");

    }

    public function testIfOtherUserIsNotPartOfTheTeam(): void
    {

        $user = UserHandler::getUserByUserId(2);

        UserSession::setUserSession($user->getUserId());

        $this->assertFalse(TeamUtils::isUserTeamMember(self::$team), "User is not part of the team!");
        $this->assertFalse(TeamUtils::isUserTeamAdmin(self::$team), "User is not the team admin!");

    }

    public function testIfFalseIsReturnedIfUserIsNotLoggedIn(): void
    {

        UserSession::clearUserSession();

        $this->assertFalse(TeamUtils::isUserTeamMember(self::$team), "Login is required (01)!");
        $this->assertFalse(TeamUtils::isUserTeamAdmin(self::$team), "Login is required (02)!");

    }

    public static function tearDownAfterClass(): void
    {
        UserSession::clearUserSession();
    }

}
