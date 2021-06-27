<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\SupportTicketCategory;


class SupportTicketCategoryHandler {

    private const DB_TABLE_NAME_SUPPORT_TICKETS_CATEGORY = "cups_supporttickets_category";

    public static function getTicketCategoryByCategoryId(int $category_id): SupportTicketCategory
    {

        if (!Validator::numericVal()->min(1)->validate($category_id)) {
            throw new \InvalidArgumentException('category_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CATEGORY)
            ->where('categoryID = ?')
            ->setParameter(0, $category_id);

        $category_query = $queryBuilder->executeQuery();
        $category_result = $category_query->fetch();

        if (empty($category_result)) {
            throw new \UnexpectedValueException("unknown_support_ticket_category");
        }

        $category = new SupportTicketCategory();
        $category->setCategoryId($category_result['categoryID']);
        $category->setGermanName($category_result['name_de']);
        $category->setEnglishName($category_result['name_uk']);
        $category->setTemplate($category_result['template']);

        return $category;

    }

    public static function saveCategory(SupportTicketCategory $category): SupportTicketCategory
    {

        if (is_null($category->getCategoryId())) {
            $category = self::insertCategory($category);
        } else {
            self::updateCategory($category);
        }

        return $category;

    }

    private static function insertCategory(SupportTicketCategory $category): SupportTicketCategory
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CATEGORY)
            ->values(
                    [
                        'name_de' => '?',
                        'name_uk' => '?',
                        'template' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $category->getGermanName(),
                        1 => $category->getEnglishName(),
                        2 => $category->getTemplate()
                    ]
                );

        $queryBuilder->executeQuery();

        $category->setCategoryId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $category;

    }

    private static function updateCategory(SupportTicketCategory $category): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_SUPPORT_TICKETS_CATEGORY)
            ->set("name_de", "?")
            ->set("name_uk", "?")
            ->set("template", "?")
            ->where("categoryID = ?")
            ->setParameter(0, $category->getGermanName())
            ->setParameter(1, $category->getEnglishName())
            ->setParameter(2, $category->getTemplate())
            ->setParameter(3, $category->getCategoryId());

        $queryBuilder->executeQuery();

    }

}