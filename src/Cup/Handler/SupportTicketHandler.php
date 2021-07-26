<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\UserSession;
use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Exception\AccessDeniedException;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\Enum\SupportTicketEnums;
use myrisk\Cup\Handler\SupportTicketCategoryHandler;
use myrisk\Cup\Handler\SupportTicketStatusHandler;

class SupportTicketHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS = "cups_supporttickets";

    public static function getTicketByTicketId(int $ticket_id): SupportTicket
    {

        if (!Validator::numericVal()->min(1)->validate($ticket_id)) {
            throw new \InvalidArgumentException('ticket_id_value_is_invalid');
        }

        if (UserSession::getUserId() < 1) {
            throw new AccessDeniedException();
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->where('ticketID = ?')
            ->setParameter(0, $ticket_id);

        $ticket_query = $queryBuilder->executeQuery();
        $ticket_result = $ticket_query->fetchAssociative();

        if (empty($ticket_result)) {
            throw new \UnexpectedValueException('unknown_support_ticket');
        }

        $ticket = new SupportTicket();
        $ticket->setTicketId($ticket_result['ticketID']);
        $ticket->setSubject($ticket_result['name']);
        $ticket->setText($ticket_result['text']);
        $ticket->setStatus($ticket_result['status']);
        $ticket->setStartDate(
            new \DateTime($ticket_result['start_date'])
        );
        $ticket->setOpener(
            UserHandler::getUserByUserId($ticket_result['userID'])
        );
        if (!is_null($ticket_result['take_date'])) {
            $ticket->setTakeDate(
                new \DateTime($ticket_result['take_date'])
            );
        }
        if (!is_null($ticket_result['closed_date'])) {
            $ticket->setCloseDate(
                new \DateTime($ticket_result['closed_date'])
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
            SupportTicketStatusHandler::getTicketStatusByTicketId($ticket_id)
        );

        $ticket->setContent(
            SupportTicketContentHandler::getTicketContentByTicketId($ticket_id)
        );

        return $ticket;

    }

    /**
     * @return array<SupportTicket>
     */
    public static function getOpenSupportTickets(): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('ticketID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS)
            ->where('status = ?')
            ->setParameter(0, SupportTicketEnums::TICKET_STATUS_OPEN);

        $ticket_query = $queryBuilder->executeQuery();

        $ticket_array = array();

        $ticket_results = $ticket_query->fetchAllAssociative();
        foreach ($ticket_results as $ticket_result)
        {
            array_push(
                $ticket_array,
                self::getTicketByTicketId((int) $ticket_result['ticketID'])
            );
        }

        return $ticket_array;

    }

    /**
     * @return array<SupportTicket>
     */
    public static function getSupportTicketsWithNewContent(): array
    {

        $tickets_with_new_content = array();

        $support_tickets = self::getSupportTicketsOfUser();
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
    public static function getSupportTicketsOfUser(): array
    {

        if (UserSession::getUserId() < 1) {
            return array();
        }

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
            ->setParameter(0, UserSession::getUserId())
            ->setParameter(1, UserSession::getUserId());

        $ticket_query = $queryBuilder->executeQuery();

        $ticket_array = array();

        $ticket_results = $ticket_query->fetchAllAssociative();
        foreach ($ticket_results as $ticket_result)
        {
            array_push(
                $ticket_array,
                self::getTicketByTicketId((int) $ticket_result['ticketID'])
            );
        }

        return $ticket_array;

    }

    public static function saveTicket(SupportTicket $ticket): SupportTicket
    {

        if (is_null($ticket->getTicketId())) {
            $ticket = self::insertTicket($ticket);
        } else {
            self::updateTicket($ticket);
        }

        if (is_null($ticket->getTicketId())) {
            throw new \InvalidArgumentException("ticket_id_is_invalid");
        }

        return self::getTicketByTicketId($ticket->getTicketId());

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
                        2 => $ticket->getStartDate()->format("Y-m-d H:i:s"),
                        3 => $ticket->getOpener()->getUserId(),
                        4 => $category_id,
                        5 => $cup_id,
                        6 => $match_id,
                        7 => $team_id,
                        8 => $opponent_id
                    ]
                );

        $queryBuilder->executeQuery();

        $ticket->setTicketId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $ticket;

    }

    private static function updateTicket(SupportTicket $ticket): void
    {

        $take_timestamp = (!is_null($ticket->getTakeDate())) ? $ticket->getTakeDate()->format("Y-m-d H:i:s") : null;
        $close_timestamp = (!is_null($ticket->getCloseDate())) ? $ticket->getCloseDate()->format("Y-m-d H:i:s") : null;

        $admin_id = (!is_null($ticket->getAdmin())) ? $ticket->getAdmin()->getUserId() : null;
        $category_id = !is_null($ticket->getCategory()) ? $ticket->getCategory()->getCategoryId() : null;
        $closed_by_id = $ticket->isClosed() ? $ticket->getCloser()->getUserId() : null;

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
            ->setParameter(11, $ticket->getStartDate()->format("Y-m-d H:i:s"))
            ->setParameter(12, $ticket->getStatus())
            ->setParameter(13, $ticket->getOpener()->getUserId())
            ->setParameter(14, $ticket->getTicketId());

        $queryBuilder->executeQuery();

    }

    public static function takeTicket(int $ticket_id): void
    {

        if (UserSession::getUserId() < 1) {
            throw new AccessDeniedException();
        }

        $ticket = self::getTicketByTicketId($ticket_id);

        $ticket->setAdmin(
            UserHandler::getUserByUserId(UserSession::getUserId())
        );
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_IN_PROGRESS);
        $ticket->setTakeDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

    public static function closeTicket(int $ticket_id): void
    {

        if (UserSession::getUserId() < 1) {
            throw new AccessDeniedException();
        }

        $ticket = self::getTicketByTicketId($ticket_id);

        $ticket->setCloser(
            UserHandler::getUserByUserId(UserSession::getUserId())
        );
        $ticket->setStatus(SupportTicketEnums::TICKET_STATUS_DONE);
        $ticket->setCloseDate(new \DateTime("now"));

        self::updateTicket($ticket);

    }

}
