<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Cup;
use \myrisk\Cup\Participant;
use \myrisk\Cup\Enum\CupEnums;

final class CupTest extends TestCase
{

    public function testIfCupInstanceCanBeCreated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $cup_participant = new Participant();
        $cup_participant->setParticipantId(100);
        $cup_participant->setRegisterDateTime($datetime_now);
        $cup_participant->setCheckInDateTime($datetime_later);

        $cup = new Cup();
        $cup->setCupId(1337);
        $cup->setName("Test Cup Name");
        $cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $cup->setCheckInDateTime($datetime_now);
        $cup->setStartDateTime($datetime_later);
        $cup->addCupParticipant($cup_participant);

        $this->assertInstanceOf(Cup::class, $cup);
        $this->assertEquals(1337, $cup->getCupId(), "Cup ID is set.");
        $this->assertEquals("Test Cup Name", $cup->getName(), "Cup name is set.");
        $this->assertEquals(3, $cup->getStatus(), "Cup status is set.");
        $this->assertEquals($datetime_now, $cup->getCheckInDateTime(), "Cup check-in datetime is set.");
        $this->assertEquals($datetime_later, $cup->getStartDateTime(), "Cup start datetime is set.");
        $this->assertEquals(1, count($cup->getCupParticipants()), "Cup participant count is expected.");

        $this->assertInstanceOf(Participant::class, $cup->getCupParticipants()[0]);
        $this->assertEquals(100, $cup->getCupParticipants()[0]->getParticipantId(), "Cup participant ID is set.");
        $this->assertEquals($datetime_now, $cup->getCupParticipants()[0]->getRegisterDateTime(), "Cup participant register datetime is set.");
        $this->assertEquals($datetime_later, $cup->getCupParticipants()[0]->getCheckInDateTime(), "Cup participant check-in datetime is set.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupStatusIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = new Cup();
        $cup->setStatus(5);

    }

}
