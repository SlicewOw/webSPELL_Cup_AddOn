<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \myrisk\Cup\TeamMemberPosition;
use \myrisk\Cup\Handler\TeamMemberPositionHandler;

final class TeamMemberPositionHandlerTest extends TestCase
{

    private function getRandomString(): string
    {
        return bin2hex(random_bytes(10));
    }

    public function testIfCupHandlerReturnsCupInstance(): void
    {

        $sort = rand(1, 999);

        $new_position = new TeamMemberPosition();
        $new_position->setPosition("Test Position " . $this->getRandomString());
        $new_position->setSort($sort);

        $saved_position = TeamMemberPositionHandler::saveTeamMemberPosition($new_position);

        $this->assertInstanceOf(TeamMemberPosition::class, $saved_position);

        $position = TeamMemberPositionHandler::getPositionByPositionId($saved_position->getPositionId());

        $this->assertInstanceOf(TeamMemberPosition::class, $position);
        $this->assertEquals($saved_position->getPositionId(), $position->getPositionId(), "Position ID is the same.");
        $this->assertEquals($saved_position->getPosition(), $position->getPosition(), "Position name is the same.");
        $this->assertEquals($sort, $position->getSort(), "Sort value is the correct.");
        $this->assertEquals($saved_position->getSort(), $position->getSort(), "Sort value is the same.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPositionIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $position = TeamMemberPositionHandler::getPositionByPositionId(-1);

        // This line is hopefully never be reached
        $this->assertLessThan(1, $position->getPositionId());

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPositionDoesNotExist(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamMemberPositionHandler::getPositionByPositionId(99999999);

    }

}
