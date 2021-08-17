<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use webspell_ng\Sponsor;
use webspell_ng\User;

use myrisk\Cup\Admin;
use myrisk\Cup\Cup;
use myrisk\Cup\CupSponsor;
use myrisk\Cup\Enum\CupEnums;

final class CupTest extends TestCase
{

    public function testIfCupInstanceCanBeCreated(): void
    {

        $datetime_now = new DateTime('now');
        $datetime_later = new DateTime('2025-05-01 13:37:00');

        $sponsor = new CupSponsor();
        $sponsor->setSponsor(new Sponsor());

        $admin = new Admin();
        $admin->setUser(new User());

        $cup = new Cup();
        $cup->setCupId(1337);
        $cup->setName("Test Cup Name");
        $cup->setSize(CupEnums::CUP_SIZE_8);
        $cup->setMode(CupEnums::CUP_MODE_5ON5);
        $cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $cup->setCheckInDateTime($datetime_now);
        $cup->setStartDateTime($datetime_later);
        $cup->addSponsor($sponsor);
        $cup->addAdmin($admin);

        $this->assertInstanceOf(Cup::class, $cup);
        $this->assertEquals(1337, $cup->getCupId(), "Cup ID is set.");
        $this->assertEquals("Test Cup Name", $cup->getName(), "Cup name is set.");
        $this->assertEquals(CupEnums::CUP_SIZE_8, $cup->getSize(), "Cup size is set.");
        $this->assertEquals(3, $cup->getTotalRoundCount(), "Total count of rounds is set.");
        $this->assertEquals("5on5", $cup->getMode(), "Cup mode is set.");
        $this->assertEquals(5, $cup->getRequiredPlayersPerTeam(), "5on5 cup requires 5 players.");
        $this->assertEquals(3, $cup->getStatus(), "Cup status is set.");
        $this->assertTrue($cup->isRunning(), "Cup status is 'running'.");
        $this->assertFalse($cup->isFinished(), "Cup status is not 'finished'.");
        $this->assertEquals($datetime_now, $cup->getCheckInDateTime(), "Cup check-in datetime is set.");
        $this->assertEquals($datetime_later, $cup->getStartDateTime(), "Cup start datetime is set.");
        $this->assertEquals(0, count($cup->getCupParticipants()), "Cup participant count is expected.");
        $this->assertEquals(0, count($cup->getCheckedInCupParticipants()), "Cup participants which are checked in are expected.");
        $this->assertEquals(1, count($cup->getSponsors()), "Cup spoonsor count is expected.");
        $this->assertEquals(1, count($cup->getAdmins()), "Cup admin count is expected.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = new Cup();
        $cup->setCupId(0);

    }

    public function testIfInvalidArgumentExceptionIsThrownIfCupStatusIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $cup = new Cup();
        $cup->setStatus(5);

    }

}
