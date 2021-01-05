<?php

namespace myrisk\Cup\Handler;

use Doctrine\DBAL\FetchMode;
use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;

use myrisk\Cup\CupAwardCategory;

class CupAwardCategoryHandler {

    private const DB_TABLE_NAME_CUPS_AWARDS_CATEGORY = "cups_awards_category";

    public static function getCategoryByCategoryId(int $category_id): CupAwardCategory
    {

        if (!Validator::numericVal()->min(1)->validate($category_id)) {
            throw new \InvalidArgumentException('category_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS_CATEGORY)
            ->where('categoryID = ?')
            ->setParameter(0, $category_id);

        $category_query = $queryBuilder->execute();
        $category_result = $category_query->fetch(FetchMode::MIXED);

        if (empty($category_result)) {
            throw new \UnexpectedValueException('unknown_cup_award_category');
        }

        $category = new CupAwardCategory();
        $category->setCategoryId($category_result['categoryID']);
        $category->setName($category_result['name']);
        $category->setIcon($category_result['icon']);
        $category->setActiveColumn($category_result['active_column']);
        $category->setInfo($category_result['description']);
        $category->setSort((int) $category_result['sort']);

        if (!is_null($category_result['cup_ranking'])) {
            $category->setRequiredCupRanking((int) $category_result['cup_ranking']);
        }

        if (!is_null($category_result['count_of_cups'])) {
            $category->setRequiredCountOfCups((int) $category_result['count_of_cups']);
        }

        if (!is_null($category_result['count_of_matches'])) {
            $category->setRequiredCountOfMatches((int) $category_result['count_of_matches']);
        }

        return $category;

    }

    /**
     * @return array<CupAwardCategory>
     */
    public static function getAllCupAwardCategories(): array
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('categoryID')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS_CATEGORY)
            ->orderBy("sort", "ASC");

        $category_query = $queryBuilder->execute();

        $award_categories = array();

        while ($category_result = $category_query->fetch(FetchMode::MIXED))
        {
            array_push(
                $award_categories,
                self::getCategoryByCategoryId((int) $category_result['categoryID'])
            );
        }

        return $award_categories;

    }

    public static function saveCategory(CupAwardCategory $category): CupAwardCategory
    {

        if (is_null($category->getCategoryId())) {
            $category = self::insertCategory($category);
        } else {
            self::updateCategory($category);
        }

        return self::getCategoryByCategoryId($category->getCategoryId());

    }

    private static function insertCategory(CupAwardCategory $category): CupAwardCategory
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS_CATEGORY)
            ->values(
                    [
                        'name' => '?',
                        'icon' => '?',
                        'active_column' => '?',
                        'cup_ranking' => '?',
                        'count_of_cups' => '?',
                        'count_of_matches' => '?',
                        'sort' => '?',
                        'description' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $category->getName(),
                        1 => $category->getIcon(),
                        2 => $category->getActiveColumn(),
                        3 => $category->getRequiredCupRanking(),
                        4 => $category->getRequiredCountOfCups(),
                        5 => $category->getRequiredCountOfMatches(),
                        6 => $category->getSort(),
                        7 => $category->getInfo()
                    ]
                );

        $queryBuilder->execute();

        $category->setCategoryId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $category;

    }

    private static function updateCategory(CupAwardCategory $category): void
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->update(WebSpellDatabaseConnection::getTablePrefix() . self::DB_TABLE_NAME_CUPS_AWARDS_CATEGORY)
            ->set("name", "?")
            ->set("icon", "?")
            ->set("active_column", "?")
            ->set("cup_ranking", "?")
            ->set("count_of_cups", "?")
            ->set("count_of_matches", "?")
            ->set("sort", "?")
            ->set("description", "?")
            ->where('categoryID = ?')
            ->setParameter(0, $category->getName())
            ->setParameter(1, $category->getIcon())
            ->setParameter(2, $category->getActiveColumn())
            ->setParameter(3, $category->getRequiredCupRanking())
            ->setParameter(4, $category->getRequiredCountOfCups())
            ->setParameter(5, $category->getRequiredCountOfMatches())
            ->setParameter(6, $category->getSort())
            ->setParameter(7, $category->getInfo())
            ->setParameter(8, $category->getCategoryId());

        $queryBuilder->execute();

    }

}