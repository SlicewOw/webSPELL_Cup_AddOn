<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\User;
use webspell_ng\UserSession;
use webspell_ng\Handler\CountryHandler;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\SupportTicketContent;
use myrisk\Cup\Handler\SupportTicketContentHandler;
use myrisk\Cup\Handler\SupportTicketHandler;

final class SupportTicketContentHandlerTest extends TestCase
{

    /**
     * @var SupportTicket $ticket
     */
    private static $ticket;

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

        $new_ticket = new SupportTicket();
        $new_ticket->setOpener(self::$user);
        $new_ticket->setSubject("Test Ticket " . StringFormatterUtils::getRandomString(10, 2));
        $new_ticket->setText("Test Content \n " . StringFormatterUtils::getRandomString(10, 2) . " \n " . StringFormatterUtils::getRandomString(10, 2));

        UserSession::setUserSession(
            self::$admin->getUserId()
        );

        self::$ticket = SupportTicketHandler::saveTicket($new_ticket);

        SupportTicketHandler::takeTicket(self::$ticket->getTicketId());

    }

    public function testIfSupportTicketContentCanBeSavedAndUpdated(): void
    {

        $ticket_without_content = SupportTicketHandler::getTicketByTicketId(self::$ticket->getTicketId());

        $this->assertEquals(0, count($ticket_without_content->getContent()), "Ticket content is not set yet.");

        $content_text = "Test Content " . StringFormatterUtils::getRandomString(10, 2);
        $content_date = new \DateTime("2020-12-24 17:00:01");

        $new_content = new SupportTicketContent();
        $new_content->setPoster(self::$user);
        $new_content->setText($content_text);
        $new_content->setDate($content_date);

        SupportTicketContentHandler::saveContent(self::$ticket, $new_content);

        $ticket_with_content = SupportTicketHandler::getTicketByTicketId(self::$ticket->getTicketId());

        $ticket_content_array = $ticket_with_content->getContent();

        $this->assertEquals(1, count($ticket_content_array), "Ticket content is set.");

        $ticket_content = $ticket_content_array[0];

        $this->assertGreaterThan(0, $ticket_content->getContentId(), "Content ID is set.");
        $this->assertEquals($content_text, $ticket_content->getText(), "Content text is set.");
        $this->assertEquals($content_date, $ticket_content->getDate(), "Content date is set.");

        $changed_content_date = new \DateTime(((int) date("Y") + 1) . "-12-24 17:00:01");

        $ticket_content->setDate($changed_content_date);

        SupportTicketContentHandler::saveContent(self::$ticket, $ticket_content);

        $ticket_with_changed_content = SupportTicketHandler::getTicketByTicketId(self::$ticket->getTicketId());

        $ticket_changed_content_array = $ticket_with_changed_content->getContent();

        $this->assertEquals(1, count($ticket_changed_content_array), "Ticket content is set.");

        $changed_ticket_content = $ticket_changed_content_array[0];

        $this->assertGreaterThan(0, $changed_ticket_content->getContentId(), "Content ID is set.");
        $this->assertEquals($content_text, $changed_ticket_content->getText(), "Content text is set.");
        $this->assertEquals($changed_content_date, $changed_ticket_content->getDate(), "Content date is set.");

        $this->assertGreaterThan(0, count(SupportTicketHandler::getSupportTicketsWithNewContent()), "User has unreaded content.");

    }

    public function testIfDateIsAutomaticallySet(): void
    {

        $content_text = "Test Content " . StringFormatterUtils::getRandomString(10, 2);

        $new_content = new SupportTicketContent();
        $new_content->setPoster(self::$user);
        $new_content->setText($content_text);

        SupportTicketContentHandler::saveContent(self::$ticket, $new_content);

        $ticket_with_content = SupportTicketHandler::getTicketByTicketId(self::$ticket->getTicketId());

        $ticket_content_array = $ticket_with_content->getContent();

        $this->assertEquals(2, count($ticket_content_array), "Ticket content is set.");

        $ticket_content_01 = $ticket_content_array[0];

        $this->assertGreaterThan(0, $ticket_content_01->getContentId(), "Content ID is set.");
        $this->assertEquals($content_text, $ticket_content_01->getText(), "Content text is set.");
        $this->assertGreaterThan(0, $ticket_content_01->getDate()->getTimestamp(), "Content date is set.");

        $ticket_content_02 = $ticket_content_array[1];

        $this->assertGreaterThan(0, $ticket_content_02->getContentId(), "Content ID is set.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfTicketIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        SupportTicketContentHandler::getTicketContentByTicketId(-1);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfContentCannotBeSaved(): void
    {

        $this->expectException(InvalidArgumentException::class);

        SupportTicketContentHandler::saveContent(new SupportTicket(), new SupportTicketContent());

    }

}
