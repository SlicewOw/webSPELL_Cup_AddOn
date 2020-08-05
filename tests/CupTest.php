<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Cup;
use \myrisk\Cup\TeamParticipant;
use \myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\UserParticipant;

final class CupTest extends TestCase
{

    public function testIfCupInstanceCanBeCreated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $cup_participant_01 = new TeamParticipant();
        $cup_participant_01->setParticipantId(100);
        $cup_participant_01->setTeamId(12345);
        $cup_participant_01->setCheckedIn(true);
        $cup_participant_01->setRegisterDateTime($datetime_now);
        $cup_participant_01->setCheckInDateTime($datetime_later);

        $cup_participant_02 = new UserParticipant();
        $cup_participant_02->setParticipantId(200);
        $cup_participant_02->setTeamId(54321);
        $cup_participant_02->setCheckedIn(true);
        $cup_participant_02->setRegisterDateTime($datetime_now);
        $cup_participant_02->setCheckInDateTime($datetime_later);

        $cup = new Cup();
        $cup->setCupId(1337);
        $cup->setName("Test Cup Name");
        $cup->setMode(CupEnums::CUP_MODE_5ON5);
        $cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $cup->setCheckInDateTime($datetime_now);
        $cup->setStartDateTime($datetime_later);
        $cup->addCupParticipant($cup_participant_01);
        $cup->addCupParticipant($cup_participant_02);

        $this->assertInstanceOf(Cup::class, $cup);
        $this->assertEquals(1337, $cup->getCupId(), "Cup ID is set.");
        $this->assertEquals("Test Cup Name", $cup->getName(), "Cup name is set.");
        $this->assertEquals("5on5", $cup->getMode(), "Cup mode is set.");
        $this->assertEquals(3, $cup->getStatus(), "Cup status is set.");
        $this->assertEquals($datetime_now, $cup->getCheckInDateTime(), "Cup check-in datetime is set.");
        $this->assertEquals($datetime_later, $cup->getStartDateTime(), "Cup start datetime is set.");
        $this->assertEquals(2, count($cup->getCupParticipants()), "Cup participant count is expected.");

        $this->assertInstanceOf(TeamParticipant::class, $cup->getCupParticipants()[0]);
        $this->assertEquals(100, $cup->getCupParticipants()[0]->getParticipantId(), "Cup participant ID is set.");
        $this->assertEquals(12345, $cup->getCupParticipants()[0]->getTeamId(), "Cup participant team ID is set.");
        $this->assertEquals($datetime_now, $cup->getCupParticipants()[0]->getRegisterDateTime(), "Cup participant register datetime is set.");
        $this->assertEquals($datetime_later, $cup->getCupParticipants()[0]->getCheckInDateTime(), "Cup participant check-in datetime is set.");
        $this->assertTrue($cup->getCupParticipants()[0]->getCheckedIn(), "Cup participant is checked in.");

        $this->assertInstanceOf(UserParticipant::class, $cup->getCupParticipants()[1]);
        $this->assertEquals(200, $cup->getCupParticipants()[1]->getParticipantId(), "Cup participant ID is set.");
        $this->assertEquals(54321, $cup->getCupParticipants()[1]->getTeamId(), "Cup participant team ID is set.");
        $this->assertEquals($datetime_now, $cup->getCupParticipants()[1]->getRegisterDateTime(), "Cup participant register datetime is set.");
        $this->assertEquals($datetime_later, $cup->getCupParticipants()[1]->getCheckInDateTime(), "Cup participant check-in datetime is set.");
        $this->assertTrue($cup->getCupParticipants()[1]->getCheckedIn(), "Cup participant is checked in.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupStatusIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = new Cup();
        $cup->setStatus(5);

    }

}
