<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\SupportTicketCategory;
use myrisk\Cup\Handler\SupportTicketCategoryHandler;

final class SupportTicketCategoryHandlerTest extends TestCase
{

    public function testIfSupportTicketCategoryCanBeSavedAndUpdated(): void
    {

        $german_name = "Test Kategorie " . StringFormatterUtils::getRandomString(10);
        $english_name = "Test category " . StringFormatterUtils::getRandomString(10);

        $new_category = new SupportTicketCategory();
        $new_category->setGermanName($german_name);
        $new_category->setEnglishName($english_name);
        $new_category->setTemplate("default");

        $category = SupportTicketCategoryHandler::saveCategory($new_category);

        $this->assertGreaterThan(0, $category->getCategoryId(), "Category ID is set.");

        $saved_category = SupportTicketCategoryHandler::getTicketCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($german_name, $saved_category->getGermanName(), "German name is set.");
        $this->assertEquals($english_name, $saved_category->getEnglishName(), "English name is set.");
        $this->assertEquals("default", $saved_category->getTemplate(), "Template is set.");

        $changed_german_name = "Test Kategorie " . StringFormatterUtils::getRandomString(10);
        $changed_english_name = "Test category " . StringFormatterUtils::getRandomString(10);

        $saved_category->setGermanName($changed_german_name);
        $saved_category->setEnglishName($changed_english_name);

        SupportTicketCategoryHandler::saveCategory($saved_category);

        $updated_category = SupportTicketCategoryHandler::getTicketCategoryByCategoryId($category->getCategoryId());

        $this->assertEquals($saved_category->getCategoryId(), $updated_category->getCategoryId(), "Category ID is set.");
        $this->assertEquals($changed_german_name, $updated_category->getGermanName(), "German name is set.");
        $this->assertEquals($changed_english_name, $updated_category->getEnglishName(), "English name is set.");
        $this->assertEquals("default", $updated_category->getTemplate(), "Template is set.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCategoryIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        SupportTicketCategoryHandler::getTicketCategoryByCategoryId(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfCategoryIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        SupportTicketCategoryHandler::getTicketCategoryByCategoryId(999999999);

    }

}