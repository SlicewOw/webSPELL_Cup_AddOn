<?php

namespace myrisk\Cup;

class Rule {

    /** @var int $rule_id */
    private $rule_id;

    /** @var int $game_id */
    private $game_id;

    /** @var string $rule_name */
    private $rule_name;

    /** @var string $text */
    private $text;

    /** @var \DateTime $last_change_on */
    private $last_change_on;

    public function setRuleId(int $rule_id): void
    {
        $this->rule_id = $rule_id;
    }

    public function getRuleId(): int
    {
        return $this->rule_id;
    }

    public function setGameId(int $game_id): void
    {
        $this->game_id = $game_id;
    }

    public function getGameId(): int
    {
        return $this->game_id;
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
