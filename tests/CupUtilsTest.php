<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Cup;
use \myrisk\Cup\Enum\CupEnums;

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

        $this->markTestSkipped(
            "This test is blocked by the missing implementation of TeamHandler::isAnyTeamAdmin()"
        );

        $cup = new Cup();
        $cup->setMode(CupEnums::CUP_MODE_1ON1);
        $cup->setCheckInDateTime(new \DateTime("2010-01-01 12:00:00"));
        $cup->setStartDateTime(new \DateTime("2040-01-01 12:00:00"));
        $cup->setStatus(CupEnums::CUP_STATUS_GROUPSTAGE);

        $this->assertEquals(CupEnums::CUP_PHASE_ADMIN_CHECKIN, $cup->getPhase(), "Cup phase is running.");

    }

    public function testIfFinishedCupPhaseIsDetected(): void
    {

        $cup = new Cup();
        $cup->setStatus(CupEnums::CUP_STATUS_FINISHED);

        $this->assertEquals(CupEnums::CUP_PHASE_FINISHED, $cup->getPhase(), "Cup phase is finished.");

    }

}
