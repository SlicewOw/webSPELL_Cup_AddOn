<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\Cup;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\Utils\CupUtils;

final class CupUtilsTest extends TestCase
{

    public function testIfRegisterCupPhaseIsDetected(): void
    {

        $cup = new Cup();
        $cup->setMode(CupEnums::CUP_MODE_1ON1);
        $cup->setCheckInDateTime(new \DateTime("2030-01-01 12:00:00"));
        $cup->setStartDateTime(new \DateTime("2040-01-01 12:00:00"));
        $cup->setStatus(CupEnums::CUP_STATUS_REGISTRATION);

        $this->assertEquals(CupEnums::CUP_PHASE_ADMIN_REGISTER, $cup->getPhase(), "Cup phase is running.");

    }

    public function testIfCheckInCupPhaseIsDetected(): void
    {

        $cup = new Cup();
        $cup->setMode(CupEnums::CUP_MODE_1ON1);
        $cup->setCheckInDateTime(new \DateTime("10 minutes ago"));
        $cup->setStartDateTime(new \DateTime("+5 hours"));

        $this->assertEquals(CupEnums::CUP_PHASE_ADMIN_CHECKIN, $cup->getPhase(), "Cup phase is running.");

    }

    public function testIfFinishedCupPhaseIsDetected(): void
    {

        $cup = new Cup();
        $cup->setStatus(CupEnums::CUP_STATUS_FINISHED);

        $this->assertEquals(CupEnums::CUP_PHASE_FINISHED, $cup->getPhase(), "Cup phase is finished.");

    }

    public function testIfRealCupSizeIsDetected(): void
    {

        $this->assertEquals(2, CupUtils::getSizeByCheckedInParticipants(1), "Real cup size is detected.");
        $this->assertEquals(4, CupUtils::getSizeByCheckedInParticipants(4), "Real cup size is detected.");
        $this->assertEquals(8, CupUtils::getSizeByCheckedInParticipants(6), "Real cup size is detected.");
        $this->assertEquals(16, CupUtils::getSizeByCheckedInParticipants(15), "Real cup size is detected.");
        $this->assertEquals(32, CupUtils::getSizeByCheckedInParticipants(32), "Real cup size is detected.");
        $this->assertEquals(64, CupUtils::getSizeByCheckedInParticipants(62), "Real cup size is detected.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfParticipantCountIsTooHigh(): void
    {

        $this->expectException(InvalidArgumentException::class);

        CupUtils::getSizeByCheckedInParticipants(999999);

    }

    public function testIfEmptyArrayOfParticipantsIsCatched(): void
    {

        $participants_in_random_order = CupUtils::getParticipantsInRandomOrder(
            16,
            array()
        );

        $this->assertEmpty($participants_in_random_order, "Empty participants are not randomized.");

    }

    public function testIfTeamsAreReturnedWithoutWalkoversNextToEachOther(): void
    {

        $participants = array();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();
        $participants[] = new TeamParticipant();

        $participants_in_random_order = CupUtils::getParticipantsInRandomOrder(
            16,
            $participants
        );

        $walkovers_are_next_to_each_other = false;

        for ($index = 0; $index < count($participants_in_random_order); $index += 2) {

            if (is_numeric($participants_in_random_order[$index]) && is_numeric($participants_in_random_order[$index + 1])) {
                $walkovers_are_next_to_each_other = true;
            }

        }

        $this->assertFalse($walkovers_are_next_to_each_other, "Walkovers are NOT next to each other!");

    }

}
