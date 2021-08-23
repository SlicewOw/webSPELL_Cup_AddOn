<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\CupPenalty;
use myrisk\Cup\CupPenaltyCategory;
use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\Handler\CupPenaltyCategoryHandler;
use myrisk\Cup\Handler\CupPenaltyHandler;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamMemberPositionHandler;

use webspell_ng\User;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

final class CupPenaltyHandlerTest extends TestCase
{

    /**
     * @var CupPenaltyCategory $category
     */
    private static $category;

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

        $new_category = new CupPenaltyCategory();
        $new_category->setNameInGerman("Test Kategorie " . StringFormatterUtils::getRandomString(10));
        $new_category->setNameInEnglish("Test Category " . StringFormatterUtils::getRandomString(10));
        $new_category->setPenaltyPoints(random_int(1, 1000));
        $new_category->setIsLifetimeBan(false);

        self::$category = CupPenaltyCategoryHandler::saveCategory($new_category);

        self::$user = UserHandler::getUserByUserId(1);

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser(self::$user);
        $team_member->setPosition($position);
        $team_member->setJoinDate(new \DateTime("1 day ago"));
        $team_member->setIsActive(true);

        $new_team = new Team();
        $new_team->setName("Test Team " . StringFormatterUtils::getRandomString(10));
        $new_team->setTag(StringFormatterUtils::getRandomString(10));
        $new_team->setCreationDate(new \DateTime("5 hours ago"));
        $new_team->setHomepage("https://gaming.myrisk-ev.de");
        $new_team->setLogotype("logotype");
        $new_team->setIsDeleted(false);
        $new_team->addMember($team_member);
        $new_team->setCountry(
            CountryHandler::getCountryByCountryShortcut("de")
        );

        self::$team = TeamHandler::saveTeam($new_team);

    }

    public function testIfPenaltyCanBeSavedAndUpdated_user(): void
    {

        $comment = StringFormatterUtils::getRandomString(10);

        $new_penalty = new CupPenalty();
        $new_penalty->setPenaltyCategory(self::$category);
        $new_penalty->setAdmin(self::$user);
        $new_penalty->setUser(self::$user);
        $new_penalty->setComment($comment);

        $saved_penalty = CupPenaltyHandler::savePenalty($new_penalty);

        $this->assertInstanceOf(CupPenalty::class, $saved_penalty, "Instance is as expected.");
        $this->assertGreaterThan(0, $saved_penalty->getPenaltyId(), "Penalty ID is set.");
        $this->assertEquals(self::$user->getUserId(), $saved_penalty->getAdmin()->getUserId(), "Admin is set.");
        $this->assertNull($saved_penalty->getTeam(), "Team is NOT set.");
        $this->assertNotNull($saved_penalty->getUser(), "User is set.");
        $this->assertEquals(self::$user->getUserId(), $saved_penalty->getUser()->getUserId(), "User is set.");
        $this->assertEquals($comment, $saved_penalty->getComment(), "Comment is set.");
        $this->assertFalse($saved_penalty->isDeleted(), "Penalty is NOT deleted.");
        $this->assertTrue($saved_penalty->isActive(), "Penalty is active!");

        $changed_penalty = $saved_penalty;
        $changed_penalty->setIsDeleted(true);

        $updated_penalty = CupPenaltyHandler::savePenalty($changed_penalty);

        $this->assertEquals($saved_penalty->getPenaltyId(), $updated_penalty->getPenaltyId(), "Penalty ID is set.");
        $this->assertEquals(self::$user->getUserId(), $updated_penalty->getAdmin()->getUserId(), "Admin is set.");
        $this->assertNull($updated_penalty->getTeam(), "Team is NOT set.");
        $this->assertNotNull($updated_penalty->getUser(), "User is set.");
        $this->assertEquals(self::$user->getUserId(), $updated_penalty->getUser()->getUserId(), "User is set.");
        $this->assertEquals($comment, $updated_penalty->getComment(), "Comment is set.");
        $this->assertTrue($updated_penalty->isDeleted(), "Penalty is deleted.");
        $this->assertFalse($saved_penalty->isActive(), "Penalty is NOT active!");

        $penalties_of_user = CupPenaltyHandler::getPenaltiesOfUser(self::$user);

        $this->assertNotEmpty($penalties_of_user);

    }

    public function testIfPenaltyCanBeSavedAndUpdated_team(): void
    {

        $comment = StringFormatterUtils::getRandomString(10);

        $new_penalty = new CupPenalty();
        $new_penalty->setPenaltyCategory(self::$category);
        $new_penalty->setAdmin(self::$user);
        $new_penalty->setTeam(self::$team);
        $new_penalty->setComment($comment);

        $saved_penalty = CupPenaltyHandler::savePenalty($new_penalty);

        $this->assertInstanceOf(CupPenalty::class, $saved_penalty, "Instance is as expected.");
        $this->assertGreaterThan(0, $saved_penalty->getPenaltyId(), "Penalty ID is set.");
        $this->assertEquals(self::$user->getUserId(), $saved_penalty->getAdmin()->getUserId(), "Admin is set.");
        $this->assertNotNull($saved_penalty->getTeam(), "Team is set.");
        $this->assertEquals(self::$team->getTeamId(), $saved_penalty->getTeam()->getTeamId(), "Team is set.");
        $this->assertNull($saved_penalty->getUser(), "User is NOT set.");
        $this->assertEquals($comment, $saved_penalty->getComment(), "Comment is set.");
        $this->assertFalse($saved_penalty->isDeleted(), "Penalty is NOT deleted.");
        $this->assertTrue($saved_penalty->isActive(), "Penalty is active!");

        $changed_penalty = $saved_penalty;
        $changed_penalty->setIsDeleted(true);

        $updated_penalty = CupPenaltyHandler::savePenalty($changed_penalty);

        $this->assertEquals($saved_penalty->getPenaltyId(), $updated_penalty->getPenaltyId(), "Penalty ID is set.");
        $this->assertEquals(self::$user->getUserId(), $updated_penalty->getAdmin()->getUserId(), "Admin is set.");
        $this->assertNotNull($updated_penalty->getTeam(), "Team is set.");
        $this->assertEquals(self::$team->getTeamId(), $updated_penalty->getTeam()->getTeamId(), "Team is set.");
        $this->assertNull($updated_penalty->getUser(), "User is NOT set.");
        $this->assertEquals($comment, $updated_penalty->getComment(), "Comment is set.");
        $this->assertTrue($updated_penalty->isDeleted(), "Penalty is deleted.");
        $this->assertFalse($updated_penalty->isActive(), "Penalty is NOT active!");

        $penalties_of_team = CupPenaltyHandler::getPenaltiesOfTeam(self::$team);

        $this->assertNotEmpty($penalties_of_team);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPenaltyIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupPenaltyHandler::getPenaltyByPenaltyId(0);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPenaltyReceiverIsInvalid_bothEmpty(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupPenaltyHandler::savePenalty(
            new CupPenalty()
        );

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPenaltyReceiverIsInvalid_bothSet(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $penalty = new CupPenalty();
        $penalty->setUser(self::$user);
        $penalty->setTeam(self::$team);

        CupPenaltyHandler::savePenalty($penalty);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfPenaltyDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        CupPenaltyHandler::getPenaltyByPenaltyId(99999999);

    }

}
