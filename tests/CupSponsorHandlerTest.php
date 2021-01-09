<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use \webspell_ng\Sponsor;
use \webspell_ng\Handler\GameHandler;
use \webspell_ng\Utils\StringFormatterUtils;

use \myrisk\Cup\Cup;
use \myrisk\Cup\CupSponsor;
use \myrisk\Cup\Rule;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Handler\CupHandler;
use \myrisk\Cup\Handler\CupSponsorHandler;
use \myrisk\Cup\Handler\RuleHandler;
use webspell_ng\Handler\SponsorHandler;

final class CupSponsorHandlerTest extends TestCase
{

    public function testIfCupSponsorHandlerReturnsArrayOfAdminInstances(): void
    {

        $game = GameHandler::getGameByGameId(3);

        $rule = new Rule();
        $rule->setGame($game);
        $rule->setName("Test Rule " . StringFormatterUtils::getRandomString(10));
        $rule->setText(StringFormatterUtils::getRandomString(10));
        $rule->setLastChangeOn(new \DateTime("2020-01-01 23:59:59"));

        $rule = RuleHandler::saveRule($rule);

        $new_cup = new Cup();
        $new_cup->setName("Test Cup Name " . StringFormatterUtils::getRandomString(10));
        $new_cup->setMode(CupEnums::CUP_MODE_5ON5);
        $new_cup->setSize(CupEnums::CUP_SIZE_8);
        $new_cup->setStatus(CupEnums::CUP_STATUS_PLAYOFFS);
        $new_cup->setCheckInDateTime(new \DateTime("now"));
        $new_cup->setStartDateTime(new \DateTime("2024-09-04 13:37:11"));
        $new_cup->setGame($game);
        $new_cup->setRule($rule);

        $new_cup = CupHandler::saveCup($new_cup);

        $new_sponsor = new Sponsor();
        $new_sponsor->setName("Test Sponsor " . StringFormatterUtils::getRandomString(10));
        $new_sponsor->setHomepage("https://cup.myrisk-ev.de");
        $new_sponsor->setDate(new \DateTime("2020-08-25"));
        $new_sponsor->setInfo("");
        $new_sponsor->setBanner("");
        $new_sponsor->setBannerSmall("");

        $saved_sponsor = SponsorHandler::saveSponsor($new_sponsor);

        $new_cup_sponsor = new CupSponsor();
        $new_cup_sponsor->setSponsor($saved_sponsor);

        $saved_cup_sponsor = CupSponsorHandler::saveSponsorToCup($new_cup_sponsor, $new_cup);

        $this->assertInstanceOf(CupSponsor::class, $saved_cup_sponsor);
        $this->assertGreaterThan(0, $saved_cup_sponsor->getCupSponsorId(), "Sponsor ID is set.");

        $cup = CupHandler::getCupByCupId($new_cup->getCupId());

        $cup_sponsors = $cup->getSponsors();
        $this->assertEquals(1, count($cup_sponsors), "Cup sponsor is set.");
        $this->assertGreaterThan(0, $cup_sponsors[0]->getCupSponsorId(), "Cup sponsor ID ID is set.");
        $this->assertEquals($new_cup_sponsor->getSponsor()->getSponsorId(), $cup_sponsors[0]->getSponsor()->getSponsorId(), "Sponsor ID is set.");

    }

}
