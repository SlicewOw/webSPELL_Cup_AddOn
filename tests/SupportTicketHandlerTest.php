<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\Enum\SupportTicketEnums;
use myrisk\Cup\Handler\SupportTicketHandler;


final class SupportTicketHandlerTest extends TestCase
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

    public function testIfSupportTicketCanBeSavedAndUpdated(): void
    {

        $count_of_open_tickets = count(SupportTicketHandler::getOpenSupportTickets(self::$user->getUserId()));

        $ticket_subject = "Test Ticket " . StringFormatterUtils::getRandomString(10, 2);
        $ticket_text = "Test Content \n " . StringFormatterUtils::getRandomString(10, 2) . " \n " . StringFormatterUtils::getRandomString(10, 2);

        $new_ticket = new SupportTicket();
        $new_ticket->setOpener(self::$user);
        $new_ticket->setSubject($ticket_subject);
        $new_ticket->setText($ticket_text);

        $ticket = SupportTicketHandler::saveTicket($new_ticket, self::$user->getUserId());

        $this->assertEquals($count_of_open_tickets + 1, count(SupportTicketHandler::getOpenSupportTickets(self::$user->getUserId())), "Ticket is saved as open ticket.");
        $this->assertGreaterThan(0, $ticket->getTicketId(), "Ticket ID is set.");
        $this->assertEquals($ticket_subject, $ticket->getSubject(), "Subject is set.");
        $this->assertEquals($ticket_text, $ticket->getText(), "Text is set.");
        $this->assertEquals(SupportTicketEnums::TICKET_STATUS_OPEN, $ticket->getStatus(), "Ticket status is set.");
        $this->assertNull($ticket->getAdmin(), "Admin is not set yet.");
        $this->assertNull($ticket->getCloser(), "Admin is not set yet.");

        $changed_ticket_subject = "Test Ticket " . StringFormatterUtils::getRandomString(10, 2);
        $changed_ticket_text = "Test Content \n " . StringFormatterUtils::getRandomString(10, 2) . " \n " . StringFormatterUtils::getRandomString(10, 2);

        $ticket->setSubject($changed_ticket_subject);
        $ticket->setText($changed_ticket_text);

        SupportTicketHandler::saveTicket($ticket, self::$user->getUserId());

        SupportTicketHandler::takeTicket($ticket->getTicketId(), self::$admin);

        $changed_ticket = SupportTicketHandler::getTicketByTicketId($ticket->getTicketId(), self::$user->getUserId());

        $this->assertEquals($count_of_open_tickets, count(SupportTicketHandler::getOpenSupportTickets(self::$user->getUserId())), "Ticket is not counted as open ticket.");
        $this->assertEquals($ticket->getTicketId(), $changed_ticket->getTicketId(), "Ticket ID is set.");
        $this->assertEquals($changed_ticket_subject, $changed_ticket->getSubject(), "Subject is set.");
        $this->assertEquals($changed_ticket_text, $changed_ticket->getText(), "Text is set.");
        $this->assertEquals(SupportTicketEnums::TICKET_STATUS_IN_PROGRESS, $changed_ticket->getStatus(), "Ticket status is set.");
        $this->assertEquals(self::$admin->getUserId(), $changed_ticket->getAdmin()->getUserId(), "Admin is set.");
        $this->assertNull($ticket->getCloser(), "Admin is not set yet.");

        SupportTicketHandler::closeTicket($ticket->getTicketId(), self::$admin);

        $closed_ticket = SupportTicketHandler::getTicketByTicketId($ticket->getTicketId(), self::$user->getUserId());

        $this->assertEquals($ticket->getTicketId(), $closed_ticket->getTicketId(), "Ticket ID is set.");
        $this->assertEquals($ticket->getSubject(), $closed_ticket->getSubject(), "Subject is set.");
        $this->assertEquals($ticket->getText(), $closed_ticket->getText(), "Text is set.");
        $this->assertEquals(SupportTicketEnums::TICKET_STATUS_DONE, $closed_ticket->getStatus(), "Ticket status is set.");
        $this->assertEquals(self::$user->getUserId(), $closed_ticket->getOpener()->getUserId(), "Opener is set.");
        $this->assertEquals(self::$admin->getUserId(), $closed_ticket->getAdmin()->getUserId(), "Admin is set.");
        $this->assertEquals(self::$admin->getUserId(), $closed_ticket->getCloser()->getUserId(), "Closer is set.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfTicketIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        SupportTicketHandler::getTicketByTicketId(-1, 1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfTicketIdDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        SupportTicketHandler::getTicketByTicketId(999999999, 1);

    }

}
