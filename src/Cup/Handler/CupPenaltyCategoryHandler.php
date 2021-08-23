<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\CupPenaltyCategory;

class CupPenaltyCategoryHandler {

    private const DB_TABLE_NAME_PENALTY_CATEGORY = "cups_penalty_category";

    public static function getCategoryByCategoryId(int $category_id): CupPenaltyCategory
    {

        if (!Validator::numericVal()->min(1)->validate($category_id)) {
            error_log("[CUSTOM] Penalty category ID must be greater than 0, but found: " . $category_id);
            throw new \InvalidArgumentException('category_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY_CATEGORY)
            ->where('reasonID = ?')
            ->setParameter(0, $category_id);

        $category_query = $queryBuilder->executeQuery();
        $category_result = $category_query->fetchAssociative();

        if (empty($category_result)) {
            throw new \UnexpectedValueException('unknown_cup_penalty_category');
        }

        $category = new CupPenaltyCategory();
        $category->setCategoryId((int) $category_result['reasonID']);
        $category->setNameInGerman($category_result['name_de']);
        $category->setNameInEnglish($category_result['name_uk']);
        $category->setPenaltyPoints((int) $category_result['points']);
        $category->setIsLifetimeBan(
            ($category_result['lifetime'] == 1)
        );

        return $category;

    }

    public static function saveCategory(CupPenaltyCategory $category): CupPenaltyCategory
    {

        if (is_null($category->getCategoryId())) {
            $category = self::insertCategory($category);
        } else {
            self::updateCategory($category);
        }

        if (is_null($category->getCategoryId())) {
            throw new \InvalidArgumentException("category_id_is_invalid");
        }

        return self::getCategoryByCategoryId($category->getCategoryId());

    }

    private static function insertCategory(CupPenaltyCategory $category): CupPenaltyCategory
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY_CATEGORY)
            ->values(
                    [
                        'name_de' => '?',
                        'name_uk' => '?',
                        'points' => '?',
                        'lifetime' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $category->getNameInGerman(),
                        1 => $category->getNameInEnglish(),
                        2 => $category->getPenaltyPoints(),
                        3 => $category->isLifetimeBan() ? 1 : 0
                    ]
                );

        $queryBuilder->executeQuery();

        $category->setCategoryId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $category;

    }

    private static function updateCategory(CupPenaltyCategory $category): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_PENALTY_CATEGORY)
            ->set("name_de", "?")
            ->set("name_uk", "?")
            ->set("points", "?")
            ->set("lifetime", "?")
            ->where('reasonID = ?')
            ->setParameter(0, $category->getNameInGerman())
            ->setParameter(1, $category->getNameInEnglish())
            ->setParameter(2, $category->getPenaltyPoints())
            ->setParameter(3, $category->isLifetimeBan() ? 1 : 0)
            ->setParameter(4, $category->getCategoryId());

        $queryBuilder->executeQuery();

    }

}
