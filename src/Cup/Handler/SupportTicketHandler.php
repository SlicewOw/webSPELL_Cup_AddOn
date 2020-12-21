<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\User;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;
use webspell_ng\Utils\DateUtils;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\Enum\SupportTicketEnums;


class SupportTicketHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS = "cups_supporttickets";

    public static function getTicketByTicketId(int $ticket_id): SupportTicket
    {

        if (!Validator::numericVal()->min(1)->validate($ticket_id)) {
            throw new \InvalidArgumentException('ticket_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->where('ticketID = ?')
            ->setParameter(0, $ticket_id);

        $ticket_query = $queryBuilder->execute();
        $ticket_result = $ticket_query->fetch(FetchMode::MIXED);

        if (empty($ticket_result)) {
            throw new \UnexpectedValueException('unknown_support_ticket');
        }

        $ticket = new SupportTicket();
        $ticket->setTicketId($ticket_result['ticketID']);
        $ticket->setSubject($ticket_result['name']);
        $ticket->setText($ticket_result['text']);
        $ticket->setStatus($ticket_result['status']);
        $ticket->setStartDate(
            DateUtils::getDateTimeByMktimeValue($ticket_result['start_date'])
        );
        $ticket->setOpener(
            UserHandler::getUserByUserId($ticket_result['userID'])
        );
        if (!is_null($ticket_result['take_date'])) {
            $ticket->setTakeDate(
                DateUtils::getDateTimeByMktimeValue($ticket_result['take_date'])
            );
        }
        if (!is_null($ticket_result['closed_date'])) {
            $ticket->setCloseDate(
                DateUtils::getDateTimeByMktimeValue($ticket_result['closed_date'])
            );
        }
        if (!is_null($ticket_result['adminID'])) {
            $ticket->setAdmin(
                UserHandler::getUserByUserId($ticket_result['adminID'])
            );
        }
        if (!is_null($ticket_result['closed_by_id'])) {
            $ticket->setCloser(
                UserHandler::getUserByUserId($ticket_result['closed_by_id'])
            );
        }

        return $ticket;

    }

    public static function saveTicket(SupportTicket $ticket): SupportTicket
    {

        if (is_null($ticket->getTicketId())) {
            $ticket = self::insertTicket($ticket);
        } else {
            self::updateTicket($ticket);
        }

        return $ticket;

    }

    private static function insertTicket(SupportTicket $ticket): SupportTicket
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->values(
                    [
                        'name' => '?',
                        'text' => '?',
                        'start_date' => '?',
                        'userID' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $ticket->getSubject(),
                        1 => $ticket->getText(),
                        2 => $ticket->getStartDate()->getTimestamp(),
                        3 => $ticket->getOpener()->getUserId()
                    ]
                );

        $queryBuilder->execute();

        $ticket->setTicketId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $ticket;

    }

    private static function updateTicket(SupportTicket $ticket): void
    {

        $take_timestamp = (!is_null($ticket->getTakeDate())) ? $ticket->getTakeDate()->getTimestamp() : null;
        $close_timestamp = (!is_null($ticket->getCloseDate())) ? $ticket->getCloseDate()->getTimestamp() : null;

        $admin_id = (!is_null($ticket->getAdmin())) ? $ticket->getAdmin()->getUserId() : null;
        $closed_by_id = (!is_null($ticket->getCloser())) ? $ticket->getCloser()->getUserId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->set("name", "?")
            ->set("text", "?")
            ->set("start_date", "?")
            ->set("take_date", "?")
            ->set("closed_date", "?")
            ->set("userID", "?")
            ->set("adminID", "?")
            ->set("closed_by_id", "?")
            ->set("status", "?")
            ->where("ticketID", "?")
            ->setParameter(0, $ticket->getSubject())
            ->setParameter(1, $ticket->getText())
            ->setParameter(2, $ticket->getStartDate()->getTimestamp())
            ->setParameter(3, $take_timestamp)
            ->setParameter(4, $close_timestamp)
            ->setParameter(5, $ticket->getOpener()->getUserId())
            ->setParameter(6, $admin_id)
            ->setParameter(7, $closed_by_id)
            ->setParameter(8, $ticket->getStatus())
            ->setParameter(9, $ticket->getTicketId());

        $queryBuilder->execute();

    }

    public static function takeTicket(int $ticket_id, User $admin): void
    {

        $ticket = self::getTicketByTicketId($ticket_id);

        $ticket->setAdmin($admin);
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_IN_PROGRESS);
        $ticket->setTakeDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

    public static function closeTicket(int $ticket_id, User $admin): void
    {

        $ticket = self::getTicketByTicketId($ticket_id);

        $ticket->setCloser($admin);
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_DONE);
        $ticket->setCloseDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

}
