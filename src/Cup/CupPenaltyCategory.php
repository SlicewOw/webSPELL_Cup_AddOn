<?php

namespace myrisk\Cup;


class CupPenaltyCategory {

    /**
     * @var ?int $category_id
     */
    private $category_id;

    /**
     * @var string $name_german
     */
    private $name_german;

    /**
     * @var string $name_english
     */
    private $name_english;

    /**
     * @var int $penalty_points
     */
    private $penalty_points = 1;

    /**
     * @var bool $is_lifetime_ban
     */
    private $is_lifetime_ban = false;

    public function setCategoryId(int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setNameInGerman(string $name_german): void
    {
        $this->name_german = $name_german;
    }

    public function getNameInGerman(): string
    {
        return $this->name_german;
    }

    public function setNameInEnglish(string $name_english): void
    {
        $this->name_english = $name_english;
    }

    public function getNameInEnglish(): string
    {
        return $this->name_english;
    }

    public function setPenaltyPoints(int $penalty_points): void
    {
        $this->penalty_points = $penalty_points;
    }

    public function getPenaltyPoints(): int
    {
        return $this->penalty_points;
    }

    public function setIsLifetimeBan(bool $is_lifetime_ban): void
    {
        $this->is_lifetime_ban = $is_lifetime_ban;
    }

    public function isLifetimeBan(): bool
    {
        return $this->is_lifetime_ban;
    }

}
