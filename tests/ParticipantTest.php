<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\Participant;

final class ParticipantTest extends TestCase
{

    public function testIfDateTimeIsSetAutomatically(): void
    {

        $participant = new Participant();

        $this->assertGreaterThan(new \DateTime("1 minute ago"), $participant->getRegisterDateTime());

    }

}
