<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\GameHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Cup;
use myrisk\Cup\Handler\AdminHandler;
use myrisk\Cup\Handler\CupSponsorHandler;

class CupHandler {

    private const DB_TABLE_NAME_CUPS = "cups";

    public static function getCupByCupId(int $cup_id): Cup
    {

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->where('cupID = ?')
            ->setParameter(0, $cup_id);

        $cup_query = $queryBuilder->execute();
        $cup_result = $cup_query->fetch(FetchMode::MIXED);

        if (empty($cup_result)) {
            throw new \InvalidArgumentException('unknown_cup');
        }

        $cup = new Cup();
        $cup->setCupId($cup_result['cupID']);
        $cup->setName($cup_result['name']);
        $cup->setMode($cup_result['mode']);
        $cup->setSize($cup_result['max_size']);
        $cup->setStatus($cup_result['status']);
        $cup->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($cup_result['checkin_date']));
        $cup->setStartDateTime(DateUtils::getDateTimeByMktimeValue($cup_result['start_date']));
        $cup->setIsSaved(
            ($cup_result['saved'] == 1)
        );
        $cup->setIsAdminCup(
            ($cup_result['admin_visible'] == 1)
        );

        $cup->setRule(
            RuleHandler::getRuleByRuleId((int) $cup_result['ruleID'])
        );
        $cup->setGame(
            GameHandler::getGameByGameId((int) $cup_result['gameID'])
        );

        $cup = CupSponsorHandler::getSponsorsOfCup($cup);

        $cup->setCupParticipants(
            ParticipantHandler::getParticipantsOfCup($cup)
        );

        return self::setAdminsOfCup($cup);

    }

    private static function setAdminsOfCup(Cup $cup): Cup
    {

        $admins = AdminHandler::getAdminsOfCup($cup);
        foreach ($admins as $admin) {
            $cup->addAdmin($admin);
        }

        return $cup;

    }

    public static function saveCup(Cup $cup): Cup
    {

        if (is_null($cup->getRule())) {
            throw new \InvalidArgumentException('rule_of_cup_is_not_set_yet');
        }

        if (is_null($cup->getCupId())) {
            $cup = self::insertCup($cup);
        } else {
            self::updateCup($cup);
        }

        return self::getCupByCupId($cup->getCupId());

    }

    private static function insertCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->values(
                    [
                        'name' => '?',
                        'checkin_date' => '?',
                        'start_date' => '?',
                        'mode' => '?',
                        'max_size' => '?',
                        'status' => '?',
                        'gameID' => '?',
                        'ruleID' => '?',
                        'saved' => '?',
                        'admin_visible' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getName(),
                        1 => $cup->getCheckInDateTime()->getTimestamp(),
                        2 => $cup->getStartDateTime()->getTimestamp(),
                        3 => $cup->getMode(),
                        4 => $cup->getSize(),
                        5 => $cup->getStatus(),
                        6 => $cup->getGame()->getGameId(),
                        7 => $cup->getRule()->getRuleId(),
                        8 => $cup->isSaved() ? 1 : 0,
                        9 => $cup->isAdminCup() ? 1 : 0
                    ]
                );

        $queryBuilder->execute();

        $cup->setCupId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $cup;

    }

    private static function updateCup(Cup $cup): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS)
            ->set("name", "?")
            ->set("checkin_date", "?")
            ->set("start_date", "?")
            ->set("mode", "?")
            ->set("max_size", "?")
            ->set("status", "?")
            ->set("gameID", "?")
            ->set("ruleID", "?")
            ->set("saved", "?")
            ->set("admin_visible", "?")
            ->where('cupID = ?')
            ->setParameter(0, $cup->getName())
            ->setParameter(1, $cup->getCheckInDateTime()->getTimestamp())
            ->setParameter(2, $cup->getStartDateTime()->getTimestamp())
            ->setParameter(3, $cup->getMode())
            ->setParameter(4, $cup->getSize())
            ->setParameter(5, $cup->getStatus())
            ->setParameter(6, $cup->getGame()->getGameId())
            ->setParameter(7, $cup->getRule()->getRuleId())
            ->setParameter(8, $cup->isSaved() ? 1 : 0)
            ->setParameter(9, $cup->isAdminCup() ? 1 : 0)
            ->setParameter(10, $cup->getCupId());

        $queryBuilder->execute();

    }

}
