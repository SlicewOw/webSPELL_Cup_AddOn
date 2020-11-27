<?php declare(strict_types=1);

use myrisk\Cup\Enum\TeamEnums;
use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Team;
use \myrisk\Cup\TeamMember;
use \myrisk\Cup\Handler\TeamHandler;
use \myrisk\Cup\Handler\TeamMemberHandler;
use \myrisk\Cup\Handler\TeamMemberPositionHandler;

use \webspell_ng\User;
use \webspell_ng\Handler\CountryHandler;
use \webspell_ng\Handler\UserHandler;
use \webspell_ng\Utils\StringFormatterUtils;

final class TeamMemberHandlerTest extends TestCase
{

    /**
     * @var User $user
     */
    private static $user;

    /**
     * @var Team $team
     */
    private static $team;

    /**
     * @var \DateTime $creation_date
     */
    private static $creation_date;

    public static function setUpBeforeClass(): void
    {

        self::$user = UserHandler::getUserByUserId(1);
        self::$creation_date = new \DateTime(rand(1990, 2036) . "-01-05 12:34:56");

        $team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $team_tag = StringFormatterUtils::getRandomString(10);

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser(self::$user);
        $team_member->setPosition($position);
        $team_member->setJoinDate(self::$creation_date);
        $team_member->setIsActive(true);

        $new_team = new Team();
        $new_team->setName($team_name);
        $new_team->setTag($team_tag);
        $new_team->setCreationDate(self::$creation_date);
        $new_team->setHomepage("https://slicewuff.de");
        $new_team->setLogotype("logotype");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfMemberCanBeUpdated(): void
    {

        $position = TeamMemberPositionHandler::getCoachPosition();

        $team_admin = TeamMemberHandler::getMemberByUserIdAndTeam(self::$user->getUserId(), self::$team);
        $team_admin->setPosition($position);

        TeamMemberHandler::updateTeamMember(self::$team, array($team_admin));

        $updated_team = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        $this->assertNull($updated_team->getTeamAdmin(), "No team admin anymore.");
        $this->assertEquals(1, count($updated_team->getMembers()), "No member anymore.");

        $team_coach = $updated_team->getMembers()[0];
        $this->assertGreaterThan(0, $team_coach->getPosition()->getPositionId(), "Position ID is set.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_COACH, $team_coach->getPosition()->getPosition(), "Coach is set.");

    }

    public function testIfMemberCanBeKicked(): void
    {

        $saved_team_member = TeamMemberHandler::getMemberByUserIdAndTeam(self::$user->getUserId(), self::$team);

        $this->assertEquals(self::$user->getUserId(), $saved_team_member->getUser()->getUserId(), "User ID is set.");
        $this->assertEquals(self::$creation_date, $saved_team_member->getJoinDate(), "Join date is set.");

        TeamMemberHandler::kickMember(self::$team, self::$team->getTeamAdmin());

        $team_without_admin = TeamHandler::getTeamByTeamId(self::$team->getTeamId());

        $this->assertNull($team_without_admin->getTeamAdmin(), "No team admin anymore.");
        $this->assertEquals(0, count($team_without_admin->getMembers()), "No member anymore.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfTeamMemberIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamMemberHandler::getMemberByUserIdAndTeam(-1, new Team());

    }

    public function testIfUnexpectedValueExceptionIsThrownIfTeamMemberIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        TeamMemberHandler::getMemberByUserIdAndTeam(999999999, self::$team);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfUserIdOfMemberIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $team_member = new TeamMember();
        $team_member->setUser(new User());

        $team = new Team();
        $team->addMember($team_member);

        TeamMemberHandler::saveTeamMembers($team);

    }

}
