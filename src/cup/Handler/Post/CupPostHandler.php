<?php

namespace myrisk\Cup\Handler\Post;

use Respect\Validation\Validator;

use webspell_ng\Handler\GameHandler;

use myrisk\Cup\Cup;
use myrisk\Cup\Handler\CupHandler;
use myrisk\Cup\Handler\RuleHandler;

class CupPostHandler {

    /**
     * @param array<mixed> $post_values
     */
    public static function saveCupByPostValues(array $post_values): Cup
    {

        $cup = new Cup();

        if (isset($post_values['cup_name'])) {
            $cup->setName($post_values['cup_name']);
        }

        if (isset($post_values['cup_mode'])) {
            $cup->setMode($post_values['cup_mode']);
        }

        if (isset($post_values['cup_status'])) {
            $cup->setStatus($post_values['cup_status']);
        }

        if (isset($post_values['checkin_date'])) {
            $cup->setCheckInDateTime($post_values['checkin_date']);
        }

        if (isset($post_values['start_date'])) {
            $cup->setStartDateTime($post_values['start_date']);
        }

        if (isset($post_values['game_id'])) {
            $cup->setGame(
                GameHandler::getGameByGameId($post_values['game_id'])
            );
        }

        if (isset($post_values['rule_id'])) {
            $cup->setRule(
                RuleHandler::getRuleByRuleId($post_values['rule_id'])
            );
        }

        return CupHandler::saveCup($cup);

    }

}
