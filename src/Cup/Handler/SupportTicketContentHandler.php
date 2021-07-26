<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\SupportTicket;
use myrisk\Cup\SupportTicketContent;


class SupportTicketContentHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS_CONTENT = "cups_supporttickets_content";

    /**
     * @return array<SupportTicketContent>
     */
    public static function getTicketContentByTicketId(int $ticket_id): array
    {

        if (!Validator::numericVal()->min(1)->validate($ticket_id)) {
            throw new \InvalidArgumentException('ticket_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CONTENT)
            ->where('ticketID = ?')
            ->setParameter(0, $ticket_id)
            ->orderBy("date", "ASC");

        $content_query = $queryBuilder->executeQuery();

        $content_array = array();

        $content_results = $content_query->fetchAllAssociative();
        foreach ($content_results as $content_result)
        {

            $content = new SupportTicketContent();
            $content->setContentId((int) $content_result['contentID']);
            $content->setText($content_result['text']);
            $content->setDate(
                new \DateTime($content_result['date'])
            );
            $content->setPoster(
                UserHandler::getUserByUserId($content_result['userID'])
            );

            array_push($content_array, $content);

        }

        return $content_array;

    }

    public static function saveContent(SupportTicket $ticket, SupportTicketContent $content): void
    {

        if (is_null($ticket->getTicketId())) {
            throw new \InvalidArgumentException('ticket_is_not_saved_yet');
        }

        if (is_null($content->getContentId())) {
            self::insertContent($ticket, $content);
        } else {
            self::updateContent($content);
        }

    }

    private static function insertContent(SupportTicket $ticket, SupportTicketContent $content): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CONTENT)
            ->values(
                    [
                        'ticketID' => '?',
                        'date' => '?',
                        'userID' => '?',
                        'text' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $ticket->getTicketId(),
                        1 => $content->getDate()->format("Y-m-d H:i:s"),
                        2 => $content->getPoster()->getUserId(),
                        3 => $content->getText()
                    ]
                );

        $queryBuilder->executeQuery();

    }

    private static function updateContent(SupportTicketContent $content): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CONTENT)
            ->set("date", "?")
            ->set("userID", "?")
            ->set("text", "?")
            ->where("contentID = ?")
            ->setParameter(0, $content->getDate()->format("Y-m-d H:i:s"))
            ->setParameter(1, $content->getPoster()->getUserId())
            ->setParameter(2, $content->getText())
            ->setParameter(3, $content->getContentId());

        $queryBuilder->executeQuery();

    }

}
