<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\CupAward;
use myrisk\Cup\CupAwardCategory;
use myrisk\Cup\Enum\CupAwardEnums;
use myrisk\Cup\Team;
use myrisk\Cup\TeamMember;
use myrisk\Cup\Handler\CupAwardCategoryHandler;
use myrisk\Cup\Handler\CupAwardHandler;
use myrisk\Cup\Handler\TeamHandler;
use myrisk\Cup\Handler\TeamMemberPositionHandler;

final class CupAwardHandlerTest extends TestCase
{

    /**
     * @var CupAwardCategory $category
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

        $category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $sort = rand(1, 99999);
        $required_ranking = rand(1, 64);

        $new_category = new CupAwardCategory();
        $new_category->setName($category_name);
        $new_category->setIcon($category_icon);
        $new_category->setActiveColumn(CupAwardEnums::ACTIVE_COLUMN_NAME_CUP_RANKING);
        $new_category->setSort($sort);
        $new_category->setRequiredValue($required_ranking);

        self::$category = CupAwardCategoryHandler::saveCategory($new_category);

        self::$user = UserHandler::getUserByUserId(1);

        $year = rand(1990, 2036);

        $team_name = "Test Cup Team " . StringFormatterUtils::getRandomString(10);
        $team_tag = StringFormatterUtils::getRandomString(10);
        $now = new \DateTime($year . "-01-05 12:34:56");

        $position = TeamMemberPositionHandler::getAdminPosition();

        $team_member = new TeamMember();
        $team_member->setUser(
            UserHandler::getUserByUserId(1)
        );
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

    public function testIfUserCupAwardCanBeSavedAndUpdated(): void
    {

        $old_count_of_awards = count(CupAwardHandler::getCupAwardsOfUser(self::$user));

        $old_date = new \DateTime("1 minute ago");

        $new_award = new CupAward();
        $new_award->setCategory(self::$category);
        $new_award->setUser(self::$user);

        $award = CupAwardHandler::saveAward($new_award);

        $this->assertGreaterThan(0, $award->getAwardId(), "Award ID is set.");
        $this->assertEquals(self::$category, $award->getCategory(), "Category is set.");
        $this->assertGreaterThan($old_date->getTimestamp(), $award->getDate()->getTimestamp(), "Date is set.");

        $changed_date = new \DateTime("5 days ago");

        $award->setDate($changed_date);

        $updated_award = CupAwardHandler::saveAward($award);

        $this->assertEquals($award->getAwardId(), $updated_award->getAwardId(), "Award ID is set.");
        $this->assertEquals(self::$category, $updated_award->getCategory(), "Category is set.");
        $this->assertEquals($changed_date->getTimestamp(), $updated_award->getDate()->getTimestamp(), "Date is set.");

        $new_count_of_awards = count(CupAwardHandler::getCupAwardsOfUser(self::$user));

        $this->assertGreaterThan($old_count_of_awards, $new_count_of_awards, "User has a new cup award.");

    }

    public function testIfTeamCupAwardCanBeSavedAndUpdated(): void
    {

        $old_count_of_awards = count(CupAwardHandler::getCupAwardsOfTeam(self::$team));

        $old_date = new \DateTime("1 minute ago");

        $new_award = new CupAward();
        $new_award->setCategory(self::$category);
        $new_award->setTeam(self::$team);

        $award = CupAwardHandler::saveAward($new_award);

        $this->assertGreaterThan(0, $award->getAwardId(), "Award ID is set.");
        $this->assertEquals(self::$category, $award->getCategory(), "Category is set.");
        $this->assertGreaterThan($old_date->getTimestamp(), $award->getDate()->getTimestamp(), "Date is set.");

        $changed_date = new \DateTime("10 days ago");

        $award->setDate($changed_date);

        $updated_award = CupAwardHandler::saveAward($award);

        $this->assertEquals($award->getAwardId(), $updated_award->getAwardId(), "Award ID is set.");
        $this->assertEquals(self::$category, $updated_award->getCategory(), "Category is set.");
        $this->assertEquals($changed_date->getTimestamp(), $updated_award->getDate()->getTimestamp(), "Date is set.");

        $new_count_of_awards = count(CupAwardHandler::getCupAwardsOfTeam(self::$team));

        $this->assertGreaterThan($old_count_of_awards, $new_count_of_awards, "Team has a new cup award.");

    }

    public function testIfCupAwardsAreReturnedBasedOnAwardCategory(): void
    {

        $new_category = new CupAwardCategory();
        $new_category->setName("Test category " . StringFormatterUtils::getRandomString(10));
        $new_category->setIcon("icon_" . StringFormatterUtils::getRandomString(10) . ".jpg");
        $new_category->setActiveColumn(CupAwardEnums::ACTIVE_COLUMN_NAME_CUP_RANKING);
        $new_category->setSort(rand(1, 99999));
        $new_category->setRequiredValue(rand(1, 64));

        $award_category = CupAwardCategoryHandler::saveCategory($new_category);

        $this->assertEquals(0, count(CupAwardHandler::getCupAwardsOfAwardCategory($award_category)), "New award category does not have a award yet.");

        $new_award = new CupAward();
        $new_award->setCategory($award_category);
        $new_award->setUser(self::$user);

        CupAwardHandler::saveAward($new_award);

        $this->assertEquals(1, count(CupAwardHandler::getCupAwardsOfAwardCategory($award_category)), "Award category does have one award.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfAwardIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupAwardHandler::getAwardByAwardId(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfAwardIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        CupAwardHandler::getAwardByAwardId(999999999);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfAwardReceiverIsNotSet(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $award = new CupAward();
        $award->setCategory(self::$category);

        CupAwardHandler::saveAward($award);

    }

}