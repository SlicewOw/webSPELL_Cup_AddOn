<?php

namespace myrisk\Cup\Handler;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\SponsorHandler;

use myrisk\Cup\Cup;
use myrisk\Cup\CupSponsor;

class CupSponsorHandler {

    public static function getSponsorsOfCup(Cup $cup): Cup
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_sponsors')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $sponsor_query = $queryBuilder->executeQuery();

        while ($sponsor_result = $sponsor_query->fetch()) {

            $sponsor = new CupSponsor();
            $sponsor->setCupSponsorId($sponsor_result['id']);
            $sponsor->setSponsor(
                SponsorHandler::getSponsorBySponsorId($sponsor_result['sponsorID'])
            );

            $cup->addSponsor($sponsor);

        }

        return $cup;

    }

    public static function saveSponsorToCup(CupSponsor $sponsor, Cup $cup): CupSponsor
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . 'cups_sponsors')
            ->values(
                    [
                        'cupID' => '?',
                        'sponsorID' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $cup->getCupId(),
                        1 => $sponsor->getSponsor()->getSponsorId()
                    ]
                );

        $queryBuilder->executeQuery();

        $sponsor->setCupSponsorId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $sponsor;

    }

}
