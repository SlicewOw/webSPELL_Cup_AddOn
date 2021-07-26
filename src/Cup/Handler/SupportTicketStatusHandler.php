<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\UserSession;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\SupportTicketStatus;

class SupportTicketStatusHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS_STATUS = "cups_supporttickets_status";

    public static function getTicketStatusByTicketId(int $ticket_id): SupportTicketStatus
    {

        if (!Validator::numericVal()->min(1)->validate($ticket_id)) {
            throw new \InvalidArgumentException('ticket_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_STATUS)
            ->where('ticketID = ?', 'userID = ?')
            ->setParameter(0, $ticket_id)
            ->setParameter(1, UserSession::getUserId());

        $status_query = $queryBuilder->executeQuery();
        $status_result = $status_query->fetchAssociative();

        if (empty($status_result)) {
            return self::insertStatus($ticket_id, UserSession::getUserId());
        }

        $status = new SupportTicketStatus();
        $status->setUser(
            UserHandler::getUserByUserId($status_result['userID'])
        );
        $status->setDate(
            new \DateTime($status_result['date'])
        );

        return $status;

    }

    public static function saveStatus(int $ticket_id, SupportTicketStatus $status): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_STATUS)
            ->values(
                    [
                        'ticketID' => '?',
                        'userID' => '?',
                        'date' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $ticket_id,
                        1 => $status->getUser()->getUserId(),
                        2 => $status->getDate()->format("Y-m-d H:i:s")
                    ]
                );

        $queryBuilder->executeQuery();

    }

    private static function insertStatus(int $ticket_id, int $user_id): SupportTicketStatus
    {

        $new_status = new SupportTicketStatus();
        $new_status->setUser(
            UserHandler::getUserByUserId($user_id)
        );
        $new_status->setDate(
            new \DateTime("now")
        );

        self::saveStatus(
            $ticket_id,
            $new_status
        );

        return self::getTicketStatusByTicketId($ticket_id);

    }

    public static function updateStatus(int $ticket_id, SupportTicketStatus $status): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_STATUS)
            ->set("date", "?")
            ->where("ticketID = ?", "userID = ?")
            ->setParameter(0, $status->getDate()->format("Y-m-d H:i:s"))
            ->setParameter(1, $ticket_id)
            ->setParameter(2, $status->getUser()->getUserId());

        $queryBuilder->executeQuery();

    }

}
