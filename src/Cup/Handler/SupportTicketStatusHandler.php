<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\SupportTicketStatus;


class SupportTicketStatusHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS_STATUS = "cups_supporttickets_status";

    /**
     * @return array<SupportTicketStatus>
     */
    public static function getTicketStatusByTicketId(int $ticket_id): array
    {

        if (!Validator::numericVal()->min(1)->validate($ticket_id)) {
            throw new \InvalidArgumentException('ticket_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_STATUS)
            ->where('ticketID = ?')
            ->setParameter(0, $ticket_id)
            ->orderBy("date", "ASC");

        $content_query = $queryBuilder->execute();

        $content_array = array();

        while ($content_result = $content_query->fetch(FetchMode::MIXED))
        {

            $content = new SupportTicketStatus();
            $content->setDate(
                DateUtils::getDateTimeByMktimeValue($content_result['ticket_seen_date'])
            );
            $content->setUser(
                UserHandler::getUserByUserId($content_result['primary_id'])
            );
            $content->setAdmin(
                UserHandler::getUserByUserId($content_result['admin'])
            );

            array_push($content_array, $content);

        }

        return $content_array;

    }

    public static function saveStatus(SupportTicket $ticket, SupportTicketStatus $status): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_STATUS)
            ->values(
                    [
                        'ticket_id' => '?',
                        'primary_id' => '?',
                        'admin' => '?',
                        'ticket_seen_date' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $ticket->getTicketId(),
                        1 => $status->getUser()->getUserId(),
                        2 => $status->getAdmin()->getUserId(),
                        3 => $status->getDate()->getTimestamp()
                    ]
                );

        $queryBuilder->execute();

    }

}
