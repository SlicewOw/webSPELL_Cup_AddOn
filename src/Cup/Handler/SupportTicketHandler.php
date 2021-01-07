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
use myrisk\Cup\Handler\SupportTicketCategoryHandler;
use myrisk\Cup\Handler\SupportTicketStatusHandler;


class SupportTicketHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS = "cups_supporttickets";

    public static function getTicketByTicketId(int $ticket_id, int $user_id): SupportTicket
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
        if (!is_null($ticket_result['categoryID'])) {
            $ticket->setCategory(
                SupportTicketCategoryHandler::getTicketCategoryByCategoryId($ticket_result['categoryID'])
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
        if (!is_null($ticket_result['cupID'])) {
            $ticket->setCup(
                CupHandler::getCupByCupId((int) $ticket_result['cupID'])
            );
        }

        // TODO: Add CupMatch to Support Ticket if related handler is implemented

        if (!is_null($ticket_result['teamID'])) {
            $ticket->setTeam(
                TeamHandler::getTeamByTeamId($ticket_result['teamID'])
            );
        }
        if (!is_null($ticket_result['opponentID'])) {
            $ticket->setOpponent(
                TeamHandler::getTeamByTeamId($ticket_result['opponentID'])
            );
        }

        $ticket->setUserStatus(
            // TODO: Use UserSession if exists in webSPELL NG
            SupportTicketStatusHandler::getTicketStatusByTicketId($ticket_id, $user_id)
        );

        $ticket->setContent(
            SupportTicketContentHandler::getTicketContentByTicketId($ticket_id)
        );

        return $ticket;

    }

    /**
     * @return array<SupportTicket>
     */
    public static function getOpenSupportTickets(int $user_id): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('ticketID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->where('status = ?')
            ->setParameter(0, SupportTicketEnums::TICKET_STATUS_OPEN);

        $ticket_query = $queryBuilder->execute();

        $ticket_array = array();

        while ($ticket_result = $ticket_query->fetch(FetchMode::MIXED))
        {
            array_push(
                $ticket_array,
                self::getTicketByTicketId($ticket_result['ticketID'], $user_id)
            );
        }

        return $ticket_array;

    }

    /**
     * @return array<SupportTicket>
     */
    public static function getSupportTicketsWithNewContent(User $user): array
    {

        $tickets_with_new_content = array();

        $support_tickets = self::getSupportTicketsOfUser($user);
        foreach ($support_tickets as $support_ticket) {

            $ticket_content_array = $support_ticket->getContent();
            foreach ($ticket_content_array as $ticket_content) {

                if ($ticket_content->getDate() > $support_ticket->getUserStatus()->getDate()) {
                    array_push(
                        $tickets_with_new_content,
                        $support_ticket
                    );
                }

            }

        }

        return $tickets_with_new_content;

    }

    /**
     * @return array<SupportTicket>
     */
    public static function getSupportTicketsOfUser(User $user): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('ticketID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq('adminID', '?'),
                    $queryBuilder->expr()->eq('userID', '?')
                )
            )
            ->setParameter(0, $user->getUserId())
            ->setParameter(1, $user->getUserId());

        $ticket_query = $queryBuilder->execute();

        $ticket_array = array();

        while ($ticket_result = $ticket_query->fetch(FetchMode::MIXED))
        {
            array_push(
                $ticket_array,
                self::getTicketByTicketId((int) $ticket_result['ticketID'], $user->getUserId())
            );
        }

        return $ticket_array;

    }

    public static function saveTicket(SupportTicket $ticket, int $user_id): SupportTicket
    {

        if (is_null($ticket->getTicketId())) {
            $ticket = self::insertTicket($ticket);
        } else {
            self::updateTicket($ticket);
        }

        return self::getTicketByTicketId($ticket->getTicketId(), $user_id);

    }

    private static function insertTicket(SupportTicket $ticket): SupportTicket
    {

        $category_id = !is_null($ticket->getCategory()) ? $ticket->getCategory()->getCategoryId() : null;

        $cup_id = (!is_null($ticket->getCup())) ? $ticket->getCup()->getCupId() : null;
        $match_id = (!is_null($ticket->getMatch())) ? $ticket->getMatch()->getMatchId() : null;
        $team_id = (!is_null($ticket->getTeam())) ? $ticket->getTeam()->getTeamId() : null;
        $opponent_id = (!is_null($ticket->getOpponent())) ? $ticket->getOpponent()->getTeamId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->values(
                    [
                        'name' => '?',
                        'text' => '?',
                        'start_date' => '?',
                        'userID' => '?',
                        'categoryID' => '?',
                        'cupID' => '?',
                        'matchID' => '?',
                        'teamID' => '?',
                        'opponentID' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $ticket->getSubject(),
                        1 => $ticket->getText(),
                        2 => $ticket->getStartDate()->getTimestamp(),
                        3 => $ticket->getOpener()->getUserId(),
                        4 => $category_id,
                        5 => $cup_id,
                        6 => $match_id,
                        7 => $team_id,
                        8 => $opponent_id
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
        $category_id = !is_null($ticket->getCategory()) ? $ticket->getCategory()->getCategoryId() : null;
        $closed_by_id = (!is_null($ticket->getCloser())) ? $ticket->getCloser()->getUserId() : null;

        $cup_id = (!is_null($ticket->getCup())) ? $ticket->getCup()->getCupId() : null;
        $match_id = (!is_null($ticket->getMatch())) ? $ticket->getMatch()->getMatchId() : null;
        $team_id = (!is_null($ticket->getTeam())) ? $ticket->getTeam()->getTeamId() : null;
        $opponent_id = (!is_null($ticket->getOpponent())) ? $ticket->getOpponent()->getTeamId() : null;

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->set("name", "?")
            ->set("text", "?")
            ->set("take_date", "?")
            ->set("closed_date", "?")
            ->set("adminID", "?")
            ->set("categoryID", "?")
            ->set("closed_by_id", "?")
            ->set("cupID", "?")
            ->set("matchID", "?")
            ->set("teamID", "?")
            ->set("opponentID", "?")
            ->set("start_date", "?")
            ->set("status", "?")
            ->set("userID", "?")
            ->where("ticketID = ?")
            ->setParameter(0, $ticket->getSubject())
            ->setParameter(1, $ticket->getText())
            ->setParameter(2, $take_timestamp)
            ->setParameter(3, $close_timestamp)
            ->setParameter(4, $admin_id)
            ->setParameter(5, $category_id)
            ->setParameter(6, $closed_by_id)
            ->setParameter(7, $cup_id)
            ->setParameter(8, $match_id)
            ->setParameter(9, $team_id)
            ->setParameter(10, $opponent_id)
            ->setParameter(11, $ticket->getStartDate()->getTimestamp())
            ->setParameter(12, $ticket->getStatus())
            ->setParameter(13, $ticket->getOpener()->getUserId())
            ->setParameter(14, $ticket->getTicketId());

        $queryBuilder->execute();

    }

    public static function takeTicket(int $ticket_id, User $admin): void
    {

        $ticket = self::getTicketByTicketId($ticket_id, $admin->getUserId());

        $ticket->setAdmin($admin);
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_IN_PROGRESS);
        $ticket->setTakeDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

    public static function closeTicket(int $ticket_id, User $user): void
    {

        $ticket = self::getTicketByTicketId($ticket_id, $user->getUserId());

        $ticket->setCloser($user);
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_DONE);
        $ticket->setCloseDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

}
