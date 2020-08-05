<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\Cup;
use myrisk\Cup\Participant;
use myrisk\Cup\Utils\DateUtils;

class CupHandler {

    public static function getCupByCupId(int $cup_id): Cup
    {

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups')
            ->where('cupID = ?')
            ->setParameter(0, $cup_id);

        $cup_query = $queryBuilder->execute();
        $cup_result = $cup_query->fetchAll();

        $cup = new Cup();
        $cup->setCupId($cup_result[0]['cupID']);
        $cup->setName($cup_result[0]['name']);
        $cup->setMode($cup_result[0]['mode']);
        $cup->setStatus($cup_result[0]['status']);
        $cup->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($cup_result[0]['checkin_date']));
        $cup->setStartDateTime(DateUtils::getDateTimeByMktimeValue($cup_result[0]['start_date']));

        $cup = CupHandler::getCupParticipantsOfCup($cup);

        return $cup;

    }

    private static function getCupParticipantsOfCup(\myrisk\Cup\Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_teilnehmer')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $cup_participant_query = $queryBuilder->execute();

        while ($cup_participant_result = $cup_participant_query->fetch()) {

            $particpant = new Participant();
            $particpant->setParticipantId($cup_participant_result['ID']);
            $particpant->setTeamId($cup_participant_result['teamID']);
            $particpant->setCheckedIn($cup_participant_result['checked_in']);
            $particpant->setRegisterDateTime(DateUtils::getDateTimeByMktimeValue($cup_participant_result['date_register']));

            $date_checking = $cup_participant_result['date_checkin'];
            if (Validator::numericVal()->min(1)->validate($date_checking)) {
                $particpant->setCheckInDateTime(DateUtils::getDateTimeByMktimeValue($date_checking));
            }

            $cup->addCupParticipant($particpant);

        }

        return $cup;

    }

}
