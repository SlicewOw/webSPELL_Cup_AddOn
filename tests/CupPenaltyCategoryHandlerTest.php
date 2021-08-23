<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\CupPenaltyCategory;
use myrisk\Cup\Handler\CupPenaltyCategoryHandler;

use webspell_ng\Utils\StringFormatterUtils;

final class CupPenaltyCategoryHandlerTest extends TestCase
{

    public function testIfPenaltyCategoryCanBeSavedAndUpdated(): void
    {

        $name_in_german = "Test Kategorie " . StringFormatterUtils::getRandomString(10);
        $name_in_english = "Test Category " . StringFormatterUtils::getRandomString(10);
        $penalty_points = random_int(1, 1000);

        $new_category = new CupPenaltyCategory();
        $new_category->setNameInGerman($name_in_german);
        $new_category->setNameInEnglish($name_in_english);
        $new_category->setPenaltyPoints($penalty_points);
        $new_category->setIsLifetimeBan(true);

        $saved_category = CupPenaltyCategoryHandler::saveCategory($new_category);

        $this->assertInstanceOf(CupPenaltyCategory::class, $saved_category, "Category is of expected type.");
        $this->assertGreaterThan(0, $saved_category->getCategoryId(), "Category ID is set.");
        $this->assertEquals($name_in_german, $saved_category->getNameInGerman(), "Name in german is set.");
        $this->assertEquals($name_in_english, $saved_category->getNameInEnglish(), "Name in english is set.");
        $this->assertEquals($penalty_points, $saved_category->getPenaltyPoints(), "Penalty points are set.");
        $this->assertTrue($saved_category->isLifetimeBan(), "Category is a lifetime ban.");

        $changed_penalty_points = random_int(1, 1000);

        $changed_category = $saved_category;
        $changed_category->setPenaltyPoints($changed_penalty_points);
        $changed_category->setIsLifetimeBan(false);

        $updated_category = CupPenaltyCategoryHandler::saveCategory($changed_category);

        $this->assertInstanceOf(CupPenaltyCategory::class, $updated_category, "Category is of expected type.");
        $this->assertEquals($saved_category->getCategoryId(), $updated_category->getCategoryId(), "Category ID is set.");
        $this->assertEquals($name_in_german, $updated_category->getNameInGerman(), "Name in german is set.");
        $this->assertEquals($name_in_english, $updated_category->getNameInEnglish(), "Name in english is set.");
        $this->assertEquals($changed_penalty_points, $updated_category->getPenaltyPoints(), "Penalty points are set.");
        $this->assertFalse($updated_category->isLifetimeBan(), "Category is a lifetime ban.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCategoryIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupPenaltyCategoryHandler::getCategoryByCategoryId(0);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfCategoryDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        CupPenaltyCategoryHandler::getCategoryByCategoryId(99999999);

    }

}
