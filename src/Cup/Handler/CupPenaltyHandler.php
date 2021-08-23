<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\User;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\CupPenalty;
use myrisk\Cup\Team;
use myrisk\Cup\Handler\CupPenaltyCategoryHandler;
use myrisk\Cup\Handler\TeamHandler;

class CupPenaltyHandler {

    private const DB_TABLE_NAME_PENALTY = "cups_penalty";

    /**
     * @return array<CupPenalty>
     */
    public static function getPenaltiesOfUser(User $user): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('ppID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY)
            ->where('userID = ?')
            ->setParameter(0, $user->getUserId());

        $penalty_query = $queryBuilder->executeQuery();
        $penalty_results = $penalty_query->fetchAllAssociative();

        $penalties_of_user = array();

        foreach ($penalty_results as $penalty_result) {
            array_push(
                $penalties_of_user,
                self::getPenaltyByPenaltyId((int) $penalty_result['ppID'])
            );
        }

        return $penalties_of_user;

    }

    /**
     * @return array<CupPenalty>
     */
    public static function getPenaltiesOfTeam(Team $team): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('ppID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY)
            ->where('teamID = ?')
            ->setParameter(0, $team->getTeamId());

        $penalty_query = $queryBuilder->executeQuery();
        $penalty_results = $penalty_query->fetchAllAssociative();

        $penalties_of_team = array();

        foreach ($penalty_results as $penalty_result) {
            array_push(
                $penalties_of_team,
                self::getPenaltyByPenaltyId((int) $penalty_result['ppID'])
            );
        }

        return $penalties_of_team;

    }

    public static function getPenaltyByPenaltyId(int $penalty_id): CupPenalty
    {

        if (!Validator::numericVal()->min(1)->validate($penalty_id)) {
            error_log("[CUSTOM] Penalty ID must be greater than 0, but found: " . $penalty_id);
            throw new \InvalidArgumentException('penalty_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY)
            ->where('ppID = ?')
            ->setParameter(0, $penalty_id);

        $penalty_query = $queryBuilder->executeQuery();
        $penalty_result = $penalty_query->fetchAssociative();

        if (empty($penalty_result)) {
            throw new \UnexpectedValueException('unknown_cup_penalty');
        }

        $penalty = new CupPenalty();
        $penalty->setPenaltyId((int) $penalty_result['ppID']);
        $penalty->setComment($penalty_result['comment']);
        $penalty->setAdmin(
            UserHandler::getUserByUserId((int) $penalty_result['adminID'])
        );
        $penalty->setDate(
            new \DateTime($penalty_result['date'])
        );
        $penalty->setDateUntilPenaltyIsActive(
            new \DateTime($penalty_result['until_date'])
        );
        $penalty->setPenaltyCategory(
            CupPenaltyCategoryHandler::getCategoryByCategoryId((int) $penalty_result['reasonID'])
        );
        $penalty->setIsDeleted(
            ($penalty_result['deleted'] == 1)
        );
        if (!is_null($penalty_result['teamID'])) {
            $penalty->setTeam(
                TeamHandler::getTeamByTeamId((int) $penalty_result['teamID'])
            );
        }
        if (!is_null($penalty_result['userID'])) {
            $penalty->setUser(
                UserHandler::getUserByUserId((int) $penalty_result['userID'])
            );
        }

        return $penalty;

    }

    public static function savePenalty(CupPenalty $penalty): CupPenalty
    {

        if (is_null($penalty->getTeam()) && is_null($penalty->getUser())) {
            throw new \InvalidArgumentException("penalty_is_not_received_by_anyone");
        }

        if (!is_null($penalty->getTeam()) && !is_null($penalty->getUser())) {
            throw new \InvalidArgumentException("penalty_cannot_be_received_by_multiple_receivers");
        }

        if (is_null($penalty->getPenaltyId())) {
            $penalty = self::insertPenalty($penalty);
        } else {
            self::updatePenalty($penalty);
        }

        if (is_null($penalty->getPenaltyId())) {
            throw new \InvalidArgumentException("penalty_id_is_invalid");
        }

        return self::getPenaltyByPenaltyId($penalty->getPenaltyId());

    }

    private static function insertPenalty(CupPenalty $penalty): CupPenalty
    {

        $team_id = !is_null($penalty->getTeam()) ? $penalty->getTeam()->getTeamId() : null;
        $user_id = !is_null($penalty->getUser()) ? $penalty->getUser()->getUserId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY)
            ->values(
                    [
                        'adminID' => '?',
                        'date' => '?',
                        'until_date' => '?',
                        'teamID' => '?',
                        'userID' => '?',
                        'reasonID' => '?',
                        'comment' => '?',
                        'deleted' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $penalty->getAdmin()->getUserId(),
                        1 => $penalty->getDate()->format("Y-m-d H:i:s"),
                        2 => $penalty->getDateUntilPenaltyIsActive()->format("Y-m-d H:i:s"),
                        3 => $team_id,
                        4 => $user_id,
                        5 => $penalty->getPenaltyCategory()->getCategoryId(),
                        6 => $penalty->getComment(),
                        7 => $penalty->isDeleted() ? 1 : 0
                    ]
                );

        $queryBuilder->executeQuery();

        $penalty->setPenaltyId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $penalty;

    }

    private static function updatePenalty(CupPenalty $penalty): void
    {

        $team_id = !is_null($penalty->getTeam()) ? $penalty->getTeam()->getTeamId() : null;
        $user_id = !is_null($penalty->getUser()) ? $penalty->getUser()->getUserId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY)
            ->set("adminID", "?")
            ->set("date", "?")
            ->set("until_date", "?")
            ->set("teamID", "?")
            ->set("userID", "?")
            ->set("reasonID", "?")
            ->set("comment", "?")
            ->set("deleted", "?")
            ->where('ppID = ?')
            ->setParameter(0, $penalty->getAdmin()->getUserId())
            ->setParameter(1, $penalty->getDate()->format("Y-m-d H:i:s"))
            ->setParameter(2, $penalty->getDateUntilPenaltyIsActive()->format("Y-m-d H:i:s"))
            ->setParameter(3, $team_id)
            ->setParameter(4, $user_id)
            ->setParameter(5, $penalty->getPenaltyCategory()->getCategoryId())
            ->setParameter(6, $penalty->getComment())
            ->setParameter(7, $penalty->isDeleted() ? 1 : 0)
            ->setParameter(8, $penalty->getPenaltyId());

        $queryBuilder->executeQuery();

    }

}
