<?php declare(strict_types=1);

use myrisk\Cup\Enum\TeamEnums;
use PHPUnit\Framework\TestCase;

use webspell_ng\Utils\StringFormatterUtils;

use myrisk\Cup\TeamMemberPosition;
use myrisk\Cup\Handler\TeamMemberPositionHandler;

final class TeamMemberPositionHandlerTest extends TestCase
{

    public function testIfCupHandlerReturnsCupInstance(): void
    {

        $sort = rand(1, 999);

        $new_position = new TeamMemberPosition();
        $new_position->setPosition("Test Position " . StringFormatterUtils::getRandomString(10));
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

    public function testIfTeamMemberPositionCoachisReturned(): void
    {

        $coach_position = TeamMemberPositionHandler::getCoachPosition();

        $this->assertInstanceOf(TeamMemberPosition::class, $coach_position);
        $this->assertGreaterThan(0, $coach_position->getPositionId(), "Position ID is the same.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_COACH, $coach_position->getPosition(), "Position name is the same.");
        $this->assertGreaterThan(0, $coach_position->getSort(), "Sort value is the correct.");

    }

    public function testIfTeamMemberPositionCaptainisReturned(): void
    {

        $captain_position = TeamMemberPositionHandler::getCaptainPosition();

        $this->assertInstanceOf(TeamMemberPosition::class, $captain_position);
        $this->assertGreaterThan(0, $captain_position->getPositionId(), "Position ID is the same.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_CAPTAIN, $captain_position->getPosition(), "Position name is the same.");
        $this->assertGreaterThan(0, $captain_position->getSort(), "Sort value is the correct.");

    }

    public function testIfTeamMemberPositionPlayerisReturned(): void
    {

        $player_position = TeamMemberPositionHandler::getPlayerPosition();

        $this->assertInstanceOf(TeamMemberPosition::class, $player_position);
        $this->assertGreaterThan(0, $player_position->getPositionId(), "Position ID is the same.");
        $this->assertEquals(TeamEnums::TEAM_MEMBER_POSITION_PLAYER, $player_position->getPosition(), "Position name is the same.");
        $this->assertGreaterThan(0, $player_position->getSort(), "Sort value is the correct.");

    }

    public function testIfInvalidArgumentExceptionIsThrownIfPositionIdIsInvalid(): void
    {

        $this->expectException(InvalidArgumentException::class);

        TeamMemberPositionHandler::getPositionByPositionId(-1);

    }

    public function testIfUnexpectedValueExceptionIsThrownIfPositionDoesNotExist(): void
    {

        $this->expectException(UnexpectedValueException::class);

        TeamMemberPositionHandler::getPositionByPositionId(99999999);

    }

}
