<?php

namespace myrisk\Cup;

class Rule {

    private int $rule_id;
    private int $game_id;
    private string $rule_name;
    private string $text;
    private \DateTime $last_change_on;

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
