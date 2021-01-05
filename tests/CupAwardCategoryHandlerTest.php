<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\CupAwardCategory;
use myrisk\Cup\Handler\CupAwardCategoryHandler;

final class CupAwardCategoryHandlerTest extends TestCase
{

    public function testIfCupAwardCategoryCanBeSavedAndUpdated(): void
    {

        $category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $sort = rand(1, 99999);
        $required_ranking = rand(1, 64);

        $new_category = new CupAwardCategory();
        $new_category->setName($category_name);
        $new_category->setIcon($category_icon);
        $new_category->setActiveColumn("cups_placements");
        $new_category->setInfo("Test information");
        $new_category->setSort($sort);
        $new_category->setRequiredCupRanking($required_ranking);

        $category = CupAwardCategoryHandler::saveCategory($new_category);

        $this->assertGreaterThan(0, $category->getCategoryId(), "Category ID is set.");

        $saved_category = CupAwardCategoryHandler::getCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($category_name, $saved_category->getName(), "Name is set.");
        $this->assertEquals($category_icon, $saved_category->getIcon(), "Icon is set.");
        $this->assertEquals("cups_placements", $saved_category->getActiveColumn(), "Active column is set.");
        $this->assertEquals("Test information", $saved_category->getInfo(), "Info is set.");
        $this->assertEquals($sort, $saved_category->getSort(), "Sort is set.");
        $this->assertEquals($required_ranking, $saved_category->getRequiredCupRanking(), "Ranking is required to get this award.");
        $this->assertNull($saved_category->getRequiredCountOfCups(), "Cups are not required to get this award.");
        $this->assertNull($saved_category->getRequiredCountOfMatches(), "Matches are not required to get this award.");

        $changed_category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $changed_category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $changed_sort = rand(1, 99999);

        $saved_category->setName($changed_category_name);
        $saved_category->setIcon($changed_category_icon);
        $saved_category->setSort($changed_sort);

        CupAwardCategoryHandler::saveCategory($saved_category);

        $updated_category = CupAwardCategoryHandler::getCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($category->getCategoryId(), $updated_category->getCategoryId(), "Category ID is set.");
        $this->assertEquals($changed_category_name, $updated_category->getName(), "Name is set.");
        $this->assertEquals($changed_category_icon, $updated_category->getIcon(), "Icon is set.");
        $this->assertEquals("cups_placements", $updated_category->getActiveColumn(), "Active column is set.");
        $this->assertEquals("Test information", $updated_category->getInfo(), "Info is set.");
        $this->assertEquals($changed_sort, $updated_category->getSort(), "Sort is set.");
        $this->assertEquals($required_ranking, $updated_category->getRequiredCupRanking(), "Ranking 3 is required to get this award.");
        $this->assertNull($updated_category->getRequiredCountOfCups(), "Cups are not required to get this award.");
        $this->assertNull($updated_category->getRequiredCountOfMatches(), "Matches are not required to get this award.");

    }

    public function testIfCupAwardCategoryCanBeSavedWhichRequiresPlayedCups(): void
    {

        $category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $sort = rand(1, 99999);
        $required_cups = rand(1, 100);

        $new_category = new CupAwardCategory();
        $new_category->setName($category_name);
        $new_category->setIcon($category_icon);
        $new_category->setActiveColumn("cups");
        $new_category->setInfo("Test information");
        $new_category->setSort($sort);
        $new_category->setRequiredCountOfCups($required_cups);

        $category = CupAwardCategoryHandler::saveCategory($new_category);

        $this->assertGreaterThan(0, $category->getCategoryId(), "Category ID is set.");

        $saved_category = CupAwardCategoryHandler::getCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($category_name, $saved_category->getName(), "Name is set.");
        $this->assertEquals($category_icon, $saved_category->getIcon(), "Icon is set.");
        $this->assertEquals("cups", $saved_category->getActiveColumn(), "Active column is set.");
        $this->assertEquals("Test information", $saved_category->getInfo(), "Info is set.");
        $this->assertEquals($sort, $saved_category->getSort(), "Sort is set.");
        $this->assertEquals($required_cups, $saved_category->getRequiredCountOfCups(), "Count of cups is required to get this award.");
        $this->assertNull($saved_category->getRequiredCupRanking(), "Ranking is not required to get this award.");
        $this->assertNull($saved_category->getRequiredCountOfMatches(), "Matches are not required to get this award.");

    }

    public function testIfCupAwardCategoryCanBeSavedWhichRequiresPlayedMatches(): void
    {

        $category_name = "Test category " . StringFormatterUtils::getRandomString(10);
        $category_icon = "icon_" . StringFormatterUtils::getRandomString(10) . ".jpg";
        $sort = rand(1, 99999);
        $required_matches = rand(1, 250);

        $new_category = new CupAwardCategory();
        $new_category->setName($category_name);
        $new_category->setIcon($category_icon);
        $new_category->setActiveColumn("cups_matches_playoff");
        $new_category->setInfo("Test information");
        $new_category->setSort($sort);
        $new_category->setRequiredCountOfMatches($required_matches);

        $category = CupAwardCategoryHandler::saveCategory($new_category);

        $this->assertGreaterThan(0, $category->getCategoryId(), "Category ID is set.");

        $saved_category = CupAwardCategoryHandler::getCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($category_name, $saved_category->getName(), "Name is set.");
        $this->assertEquals($category_icon, $saved_category->getIcon(), "Icon is set.");
        $this->assertEquals("cups_matches_playoff", $saved_category->getActiveColumn(), "Active column is set.");
        $this->assertEquals("Test information", $saved_category->getInfo(), "Info is set.");
        $this->assertEquals($sort, $saved_category->getSort(), "Sort is set.");
        $this->assertEquals($required_matches, $saved_category->getRequiredCountOfMatches(), "Count of matches is required to get this award.");
        $this->assertNull($saved_category->getRequiredCupRanking(), "Ranking is not required to get this award.");
        $this->assertNull($saved_category->getRequiredCountOfCups(), "Cups are not required to get this award.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCategoryIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupAwardCategoryHandler::getCategoryByCategoryId(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfCategoryIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        CupAwardCategoryHandler::getCategoryByCategoryId(999999999);

    }

    public function testIfAllAwardCategoriesAreReturned(): void
    {

        $all_award_categories = CupAwardCategoryHandler::getAllCupAwardCategories();

        $this->assertGreaterThan(3, count($all_award_categories), "There are more than 3 categories.");

    }

}