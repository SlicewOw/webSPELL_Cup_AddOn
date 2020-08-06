<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\Cup;
use \myrisk\Cup\Handler\CupHandler;

final class CupHandlerTest extends TestCase
{

    public function testIfCupHandlerReturnsCupInstance(): void
    {

        $cup = CupHandler::getCupByCupId(1);

        $this->assertInstanceOf(Cup::class, $cup);

    }

}
