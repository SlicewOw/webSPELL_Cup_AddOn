<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\User;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\CupAward;
use myrisk\Cup\Team;

class CupAwardHandler {

    private const DB_TABLE_NAME_CUPS_AWARDS = "cups_awards";

    public static function getAwardByAwardId(int $award_id): CupAward
    {

        if (!Validator::numericVal()->min(1)->validate($award_id)) {
            throw new \InvalidArgumentException('award_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS)
            ->where('awardID = ?')
            ->setParameter(0, $award_id);

        $award_query = $queryBuilder->execute();
        $award_result = $award_query->fetch(FetchMode::MIXED);

        if (empty($award_result)) {
            throw new \UnexpectedValueException('unknown_cup_award');
        }

        $award = new CupAward();
        $award->setAwardId((int) $award_result['awardID']);
        $award->setCategory(
            CupAwardCategoryHandler::getCategoryByCategoryId((int) $award_result['categoryID'])
        );
        $award->setDate(
            DateUtils::getDateTimeByMktimeValue((int) $award_result['date'])
        );

        if (!is_null($award_result['teamID'])) {
            $award->setTeam(
                TeamHandler::getTeamByTeamId((int) $award_result['teamID'])
            );
        }

        if (!is_null($award_result['userID'])) {
            $award->setUser(
                UserHandler::getUserByUserId((int) $award_result['userID'])
            );
        }

        if (!is_null($award_result['cupID'])) {
            $award->setCup(
                CupHandler::getCupByCupId((int) $award_result['cupID'])
            );
        }

        return $award;

    }

    /**
     * @return array<CupAward>
     */
    public static function getCupAwardsOfUser(User $user): array
    {
        return self::getAwardsByParameters("userID", $user->getUserId());
    }

    /**
     * @return array<CupAward>
     */
    public static function getCupAwardsOfTeam(Team $team): array
    {
        return self::getAwardsByParameters("teamID", $team->getTeamId());
    }

    /**
     * @return array<CupAward>
     */
    private static function getAwardsByParameters(string $column_name, int $parent_id): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('awardID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS)
            ->where($column_name . ' = ?')
            ->setParameter(0, $parent_id);

        $award_query = $queryBuilder->execute();

        $awards_of_interest = array();

        while ($award_result = $award_query->fetch(FetchMode::MIXED))
        {
            array_push(
                $awards_of_interest,
                self::getAwardByAwardId((int) $award_result['awardID'])
            );
        }

        return $awards_of_interest;

    }

    public static function saveAward(CupAward $award): CupAward
    {

        if (is_null($award->getTeam()) && is_null($award->getUser())) {
            throw new \InvalidArgumentException("someone_needs_to_get_this_award");
        }

        if (is_null($award->getAwardId())) {
            $award = self::insertAward($award);
        } else {
            self::updateAward($award);
        }

        return self::getAwardByAwardId($award->getAwardId());

    }

    private static function insertAward(CupAward $award): CupAward
    {

        $team_id = (!is_null($award->getTeam())) ? $award->getTeam()->getTeamId() : null;
        $user_id = (!is_null($award->getUser())) ? $award->getUser()->getUserId() : null;
        $cup_id = (!is_null($award->getCup())) ? $award->getCup()->getCupId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS)
            ->values(
                    [
                        'categoryID' => '?',
                        'teamID' => '?',
                        'userID' => '?',
                        'cupID' => '?',
                        'date' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $award->getCategory()->getCategoryId(),
                        1 => $team_id,
                        2 => $user_id,
                        3 => $cup_id,
                        4 => $award->getdate()->getTimestamp()
                    ]
                );

        $queryBuilder->execute();

        $award->setAwardId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $award;

    }

    private static function updateAward(CupAward $award): void
    {

        $team_id = (!is_null($award->getTeam())) ? $award->getTeam()->getTeamId() : null;
        $user_id = (!is_null($award->getUser())) ? $award->getUser()->getUserId() : null;
        $cup_id = (!is_null($award->getCup())) ? $award->getCup()->getCupId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS)
            ->set("categoryID", "?")
            ->set("teamID", "?")
            ->set("userID", "?")
            ->set("cupID", "?")
            ->set("date", "?")
            ->where('awardID = ?')
            ->setParameter(0, $award->getCategory()->getCategoryId())
            ->setParameter(1, $team_id)
            ->setParameter(2, $user_id)
            ->setParameter(3, $cup_id)
            ->setParameter(4, $award->getdate()->getTimestamp())
            ->setParameter(5, $award->getAwardId());

        $queryBuilder->execute();

    }

}