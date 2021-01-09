<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \webspell_ng\Handler\GameHandler;
use \webspell_ng\Handler\UserHandler;
use \webspell_ng\Utils\StringFormatterUtils;

use \myrisk\Cup\Admin;
use \myrisk\Cup\Cup;
use \myrisk\Cup\Rule;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Handler\AdminHandler;
use \myrisk\Cup\Handler\CupHandler;
use \myrisk\Cup\Handler\RuleHandler;

final class AdminHandlerTest extends TestCase
{

    public function testIfAdminHandlerReturnsArrayOfAdminInstances(): void
    {

        $game = GameHandler::getGameByGameId(3);

        $rule = new Rule();
        $rule->setGame($game);
        $rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $rule->setText(StringFormatterUtils::getRandomString(10));
        $rule->setLastChangeOn(new \DateTime("2020-01-01 23:59:59"));

        $rule = RuleHandler::saveRule($rule);

        $new_cup = new Cup();
        $new_cup->setName("Test Cup Name " . StringFormatterUtils::getRandomString(10));
        $new_cup->setMode(CupEnums::CUP_MODE_5ON5);
        $new_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_cup->setStatus(CupEnums::CUP_STATUS_PLAYOFFS);
        $new_cup->setCheckInDateTime(new \DateTime("now"));
        $new_cup->setStartDateTime(new \DateTime("2024-09-04 13:37:11"));
        $new_cup->setGame($game);
        $new_cup->setRule($rule);

        $new_cup = CupHandler::saveCup($new_cup);

        $new_admin = new Admin();
        $new_admin->setRight(3);
        $new_admin->setUser(
            UserHandler::getUserByUserId(1)
        );

        $saved_admin = AdminHandler::saveAdminToCup($new_admin, $new_cup);

        $this->assertInstanceOf(Admin::class, $saved_admin);
        $this->assertGreaterThan(0, $saved_admin->getAdminId(), "Admin ID is set.");

        $cup = CupHandler::getCupByCupId($new_cup->getCupId());

        $cup_admins = $cup->getAdmins();
        $this->assertEquals(1, count($cup_admins), "Cup admin is set.");
        $this->assertGreaterThan(0, $cup_admins[0]->getAdminId(), "Admin ID is set.");
        $this->assertEquals(3, $cup_admins[0]->getRight(), "Admin right is set.");
        $this->assertEquals(1, $cup_admins[0]->getUser()->getUserId(), "User ID is set of admin.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfUserIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $admin = AdminHandler::getAdminByUserId(-1, 1);

        // This line is hopefully never be reached
        $this->assertLessThan(1, $admin->getAdminId());

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $admin = AdminHandler::getAdminByUserId(1, -1);

        // This line is hopefully never be reached
        $this->assertLessThan(1, $admin->getAdminId());

    }

    public function testIfInvalidArgumentExceptionIsThrownIfAdminDoesNotExist(): void
    {

        $this->expectException(InvalidArgumentException::class);

        AdminHandler::getAdminByUserId(99999, 99999);

    }

}
