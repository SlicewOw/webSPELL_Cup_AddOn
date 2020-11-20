<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\Admin;
use myrisk\Cup\Cup;

class AdminHandler {

    /**
     * @return array<Admin>
     */
    public static function getAdminsOfCup(Cup $cup): array
    {

        $cup_admins = array();

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('userID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_admin')
            ->where('cupID = ?')
            ->setParameter(0, $cup->getCupId());

        $admin_query = $queryBuilder->execute();

        while ($admin_result = $admin_query->fetch(FetchMode::MIXED)) {

            $cup_admin = self::getAdminByUserId(
                $admin_result['userID'],
                $cup->getCupId()
            );

            array_push($cup_admins, $cup_admin);

        }

        return $cup_admins;

    }

    public static function getAdminByUserId(int $user_id, int $cup_id): Admin
    {

        if (!Validator::numericVal()->min(1)->validate($user_id)) {
            throw new \InvalidArgumentException('user_id_value_is_invalid');
        }

        if (!Validator::numericVal()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException('cup_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_admin')
            ->where('userID = ? AND cupID = ?')
            ->setParameter(0, $user_id)
            ->setParameter(1, $cup_id);

        $admin_query = $queryBuilder->execute();
        $admin_result = $admin_query->fetch(FetchMode::MIXED);

        if (empty($admin_result)) {
            throw new \InvalidArgumentException('unknown_cup_admin');
        }

        $admin = new Admin();
        $admin->setAdminId($admin_result['adminID']);
        $admin->setRight($admin_result['rights']);
        $admin->setUser(
            UserHandler::getUserByUserId((int) $admin_result['userID'])
        );
        return $admin;

    }

    public static function saveAdminToCup(Admin $admin, Cup $cup): Admin
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . 'cups_admin')
            ->values(
                    [
                        'userID' => '?',
                        'cupID' => '?',
                        'rights' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $admin->getUser()->getUserId(),
                        1 => $cup->getCupId(),
                        2 => $admin->getRight()
                    ]
                );

        $queryBuilder->execute();

        $admin->setAdminId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $admin;

    }

}
