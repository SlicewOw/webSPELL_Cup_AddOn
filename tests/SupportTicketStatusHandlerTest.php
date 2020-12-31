<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use myrisk\Cup\Handler\SupportTicketStatusHandler;


final class SupportTicketStatusHandlerTest extends TestCase
{

    public function testIfInvalidArgumentExceptionIsThrownIfTicketIdIdInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        SupportTicketStatusHandler::getTicketStatusByTicketId(-1, 1);

    }

}
