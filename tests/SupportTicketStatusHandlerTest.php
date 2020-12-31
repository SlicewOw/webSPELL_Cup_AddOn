<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\SupportTicketStatus;
use myrisk\Cup\Handler\SupportTicketStatusHandler;


final class SupportTicketStatusHandlerTest extends TestCase
{

    /**
     * @var User $user
     */
    private static $user;

    /**
     * @var User $admin
     */
    private static $admin;

    public static function setUpBeforeClass(): void
    {

        $new_user = new User();
        $new_user->setUsername("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user->setFirstname("Test User " . StringFormatterUtils::getRandomString(10));
        $new_user->setEmail(StringFormatterUtils::getRandomString(10) . "@webspell-ng.de");
        $new_user->setTown("Hamburg");
        $new_user->setBirthday(
            new \DateTime(rand(1992, 2020) . "-" . rand(1, 12) . "-" . rand(1, 28) . " 00:00:00")
        );
        $new_user->setCountry(
            CountryHandler::getCountryByCountryShortcut("at")
        );

        self::$user = UserHandler::saveUser($new_user);

        self::$admin = UserHandler::getUserByUserId(1);

    }

}
