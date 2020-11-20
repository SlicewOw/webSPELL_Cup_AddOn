<?php

namespace myrisk\Cup;

use \webspell_ng\Game;

class Rule {

    /**
     * @var int $rule_id
     */
    private $rule_id;

    /**
     * @var Game $game
     */
    private $game;

    /**
     * @var string $rule_name
     */
    private $rule_name;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var \DateTime $last_change_on
     */
    private $last_change_on;

    public function setRuleId(int $rule_id): void
    {
        $this->rule_id = $rule_id;
    }

    public function getRuleId(): int
    {
        return $this->rule_id;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setName(string $rule_name): void
    {
        $this->rule_name = $rule_name;
    }

    public function getName(): string
    {
        return $this->rule_name;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setLastChangeOn(\DateTime $last_change_on): void
    {
        $this->last_change_on = $last_change_on;
    }

    public function getLastChangeOn(): \DateTime
    {
        return $this->last_change_on;
    }

}
