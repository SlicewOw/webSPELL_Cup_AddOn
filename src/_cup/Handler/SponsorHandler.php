<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\Cup;
use myrisk\Cup\Sponsor;

class SponsorHandler {

    public static function getSponsorByCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_sponsors')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $sponsor_query = $queryBuilder->execute();

        while ($sponsor_result = $sponsor_query->fetch(FetchMode::MIXED)) {

            $sponsor = new Sponsor();
            $sponsor->setCupSponsorId($sponsor_result['id']);
            $sponsor->setSponsorId($sponsor_result['sponsorID']);

            $cup->addSponsor($sponsor);

        }

        return $cup;

    }

}
