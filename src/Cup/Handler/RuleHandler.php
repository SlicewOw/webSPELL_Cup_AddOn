<?php

namespace myrisk\Cup\Handler;

use Respect\Validation\Validator;

use webspell_ng\WebSpellDatabaseConnection;
use webspell_ng\Handler\GameHandler;

use myrisk\Cup\Rule;

class RuleHandler {

    public static function getRuleByRuleId(int $rule_id): Rule
    {

        if (!Validator::numericVal()->min(1)->validate($rule_id)) {
            throw new \InvalidArgumentException('rule_id_value_is_invalid');
        }

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from(WebSpellDatabaseConnection::getTablePrefix() . 'cups_rules')
            ->where('ruleID = ?')
            ->setParameter(0, $rule_id);

        $rule_query = $queryBuilder->executeQuery();
        $rule_result = $rule_query->fetchAssociative();

        if (empty($rule_result)) {
            throw new \InvalidArgumentException('unknown_cup_rule');
        }

        $rule = new Rule();
        $rule->setRuleId($rule_result['ruleID']);
        $rule->setGame(
            GameHandler::getGameByGameId((int) $rule_result['gameID'])
        );
        $rule->setName($rule_result['name']);
        $rule->setText($rule_result['text']);
        $rule->setLastChangeOn(
            new \DateTime($rule_result['date'])
        );

        return $rule;

    }

    public static function saveRule(Rule $rule): Rule
    {

        $queryBuilder = WebSpellDatabaseConnection::getDatabaseConnection()->createQueryBuilder();
        $queryBuilder
            ->insert(WebSpellDatabaseConnection::getTablePrefix() . 'cups_rules')
            ->values(
                    [
                        'gameID' => '?',
                        'name' => '?',
                        'text' => '?',
                        'date' => '?'
                    ]
                )
            ->setParameters(
                    [
                        0 => $rule->getGame()->getGameId(),
                        1 => $rule->getName(),
                        2 => $rule->getText(),
                        3 => $rule->getLastChangeOn()->format("Y-m-d H:i:s")
                    ]
                );

        $queryBuilder->executeQuery();

        $rule->setRuleId(
            (int) WebSpellDatabaseConnection::getDatabaseConnection()->lastInsertId()
        );

        return $rule;

    }

}
