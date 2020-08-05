<?php

namespace myrisk\Cup\Handler;

use DateTime;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\Cup;

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
        $cup->setCheckInDateTime(new DateTime($cup_result[0]['checkin_date']));
        $cup->setStartDateTime(new DateTime($cup_result[0]['start_date']));

        return $cup;

    }

}
