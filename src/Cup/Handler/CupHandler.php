<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\GameHandler;

use myrisk\Cup\Cup;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Handler\AdminHandler;
use myrisk\Cup\Handler\CupSponsorHandler;
use myrisk\Cup\Handler\SingleEliminationBracketHandler;
use myrisk\Cup\Utils\CupUtils;

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

        $cup_query = $queryBuilder->executeQuery();
        $cup_result = $cup_query->fetchAssociative();

        if (empty($cup_result)) {
            throw new \InvalidArgumentException('unknown_cup');
        }

        $cup = new Cup();
        $cup->setCupId((int) $cup_result['cupID']);
        $cup->setName($cup_result['name']);
        $cup->setMode($cup_result['mode']);
        $cup->setSize($cup_result['max_size']);
        $cup->setMaximumOfPenaltyPoints((int) $cup_result['max_penalty']);
        $cup->setStatus((int) $cup_result['status']);
        $cup->setDescription($cup_result['description']);
        $cup->setMapVoteEnabled(
            ($cup_result['mapvote_enable'] == 1)
        );
        $cup->setCheckInDateTime(
            new \DateTime($cup_result['checkin_date'])
        );
        $cup->setStartDateTime(
            new \DateTime($cup_result['start_date'])
        );
        $cup->setIsSaved(
            ($cup_result['saved'] == 1)
        );
        $cup->setIsAdminCup(
            ($cup_result['admin_visible'] == 1)
        );
        $cup->setIsUsingServers(
            ($cup_result['server'] == 1)
        );

        $cup->setRule(
            RuleHandler::getRuleByRuleId((int) $cup_result['ruleID'])
        );
        $cup->setGame(
            GameHandler::getGameByGameId((int) $cup_result['gameID'])
        );

        if (!is_null($cup_result['mappool'])) {
            $cup->setMapPool(
                MapPoolHandler::getMapPoolById((int) $cup_result['mappool'])
            );
        }

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

        if (is_null($cup->getCupId())) {
            $cup = self::insertCup($cup);
        } else {
            self::updateCup($cup);
        }

        if (is_null($cup->getCupId())) {
            throw new \InvalidArgumentException("cup_id_is_invalid");
        }

        return self::getCupByCupId($cup->getCupId());

    }

    private static function insertCup(Cup $cup): Cup
    {

        if (is_null($cup->getRule())) {
            throw new \InvalidArgumentException('rule_of_cup_is_not_set_yet');
        }

        $map_pool_id = (!is_null($cup->getMapPool())) ? $cup->getMapPool()->getMapPoolId() : null;

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
                        'mapvote_enable' => '?',
                        'mappool' => '?',
                        'max_penalty' => '?',
                        'saved' => '?',
                        'admin_visible' => '?',
                        'server' => '?',
                        'description' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getName(),
                        1 => $cup->getCheckInDateTime()->format("Y-m-d H:i:s"),
                        2 => $cup->getStartDateTime()->format("Y-m-d H:i:s"),
                        3 => $cup->getMode(),
                        4 => $cup->getSize(),
                        5 => $cup->getStatus(),
                        6 => $cup->getGame()->getGameId(),
                        7 => $cup->getRule()->getRuleId(),
                        8 => $cup->isMapVoteDone() ? 1 : 0,
                        9 => $map_pool_id,
                        10 => $cup->getMaximumOfPenaltyPoints(),
                        11 => $cup->isSaved() ? 1 : 0,
                        12 => $cup->isAdminCup() ? 1 : 0,
                        13 => $cup->isUsingServers() ? 1 : 0,
                        14 => $cup->getDescription()
                    ]
                );

        $queryBuilder->executeQuery();

        $cup->setCupId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $cup;

    }

    private static function updateCup(Cup $cup): void
    {

        if (is_null($cup->getRule())) {
            throw new \InvalidArgumentException('rule_of_cup_is_not_set_yet');
        }

        $map_pool_id = (!is_null($cup->getMapPool())) ? $cup->getMapPool()->getMapPoolId() : null;

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
            ->set("mapvote_enable", "?")
            ->set("mappool", "?")
            ->set("max_penalty", "?")
            ->set("saved", "?")
            ->set("admin_visible", "?")
            ->set("server", "?")
            ->set("description", "?")
            ->where('cupID = ?')
            ->setParameter(0, $cup->getName())
            ->setParameter(1, $cup->getCheckInDateTime()->format("Y-m-d H:i:s"))
            ->setParameter(2, $cup->getStartDateTime()->format("Y-m-d H:i:s"))
            ->setParameter(3, $cup->getMode())
            ->setParameter(4, $cup->getSize())
            ->setParameter(5, $cup->getStatus())
            ->setParameter(6, $cup->getGame()->getGameId())
            ->setParameter(7, $cup->getRule()->getRuleId())
            ->setParameter(8, $cup->isMapVoteDone() ? 1 : 0)
            ->setParameter(9, $map_pool_id)
            ->setParameter(10, $cup->getMaximumOfPenaltyPoints())
            ->setParameter(11, $cup->isSaved() ? 1 : 0)
            ->setParameter(12, $cup->isAdminCup() ? 1 : 0)
            ->setParameter(13, $cup->isUsingServers() ? 1 : 0)
            ->setParameter(14, $cup->getDescription())
            ->setParameter(15, $cup->getCupId());

        $queryBuilder->executeQuery();

    }

    public static function startCup(Cup $cup): void
    {

        // TODO: Implement check if cup is startable

        $cup->setStatus(CupEnums::CUP_STATUS_RUNNING);
        $cup->setSize(
            CupUtils::getSizeByCheckedInParticipants(
                count($cup->getCheckedInCupParticipants())
            )
        );

        self::updateCup($cup);

        SingleEliminationBracketHandler::saveBracket($cup);

    }

    public static function finishCup(Cup $cup): void
    {

        // TODO: Implement check if cup is finishable

        // TODO: Implement creation of placements

        $cup->setStatus(CupEnums::CUP_STATUS_FINISHED);

        self::updateCup($cup);

    }

}
