<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\CupAward;
use myrisk\Cup\CupAwardCategory;
use myrisk\Cup\Handler\CupAwardCategoryHandler;
use myrisk\Cup\Handler\CupAwardHandler;

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

    public static function setUpBeforeClass(): void
    {

        $category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $sort = rand(1, 99999);
        $required_ranking = rand(1, 64);

        $new_category = new CupAwardCategory();
        $new_category->setName($category_name);
        $new_category->setIcon($category_icon);
        $new_category->setActiveColumn("cups_placements");
        $new_category->setSort($sort);
        $new_category->setRequiredCupRanking($required_ranking);

        self::$category = CupAwardCategoryHandler::saveCategory($new_category);

        self::$user = UserHandler::getUserByUserId(1);

    }

    public function testIfCupAwardCanBeSavedAndUpdated(): void
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