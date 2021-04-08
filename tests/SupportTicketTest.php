<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\SupportTicket;


final class SupportTicketTest extends TestCase
{

    public function testIfMinutesAreDetected(): void
    {

        $ticket = new SupportTicket();
        $ticket->setStartDate(new \DateTime("5 minutes ago"));

        $this->assertEquals("5min", $ticket->isOpenSince(), "Minutes are detected");

    }

    public function testIfHoursAreDetected(): void
    {

        $ticket = new SupportTicket();
        $ticket->setStartDate(new \DateTime("12 hours ago"));

        $this->assertEquals("12h", $ticket->isOpenSince(), "Hours are detected");

    }

    public function testIfDaysAreDetected(): void
    {

        $date = new \DateTime("20 days 1 hour ago");

        $ticket = new SupportTicket();
        $ticket->setStartDate($date);

        $this->assertEquals("20d", $ticket->isOpenSince(), "Days are detected");

    }

}