<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\CupMatch;

final class CupMatchTest extends TestCase
{

    public function testIfRunningCupMatchIsNotFinished(): void
    {

        $match = new CupMatch();

        $this->assertFalse($match->isFinished(), "Match is not finished.");

    }

    public function testIfMatchIsFinishedIfAdminConfirmed(): void
    {

        $match = new CupMatch();
        $match->setLeftTeamConfirmed(false);
        $match->setRightTeamConfirmed(false);
        $match->setAdminConfirmed(true);

        $this->assertTrue($match->isFinished(), "Match is finished.");

    }

    public function testIfMatchIsFinishedIfTeamsConfirmed(): void
    {

        $match = new CupMatch();
        $match->setLeftTeamConfirmed(true);
        $match->setRightTeamConfirmed(false);
        $match->setAdminConfirmed(false);

        $this->assertFalse($match->isFinished(), "Match is not finished.");

        $match->setRightTeamConfirmed(true);

        $this->assertTrue($match->isFinished(), "Match is finished.");

    }
}
