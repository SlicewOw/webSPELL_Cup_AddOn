<?php

namespace myrisk\Cup;


class CupAwardCategory {

    /**
     * @var ?int $category_id
     */
    private $category_id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $icon
     */
    private $icon;

    /**
     * @var string $active_column
     */
    private $active_column;

    /**
     * @var ?int $required_cup_ranking
     */
    private $required_cup_ranking = null;

    /**
     * @var ?int $required_count_of_cups
     */
    private $required_count_of_cups = null;

    /**
     * @var ?int $required_count_of_matches
     */
    private $required_count_of_matches = null;

    /**
     * @var int $sort
     */
    private $sort = 1;

    /**
     * @var string $info
     */
    private $info = "";

    public function setCategoryId(int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setActiveColumn(string $active_column): void
    {
        $this->active_column = $active_column;
    }

    public function getActiveColumn(): string
    {
        return $this->active_column;
    }

    public function setRequiredCupRanking(int $required_cup_ranking): void
    {
        $this->required_cup_ranking = $required_cup_ranking;
    }

    public function getRequiredCupRanking(): ?int
    {
        return $this->required_cup_ranking;
    }

    public function setRequiredCountOfCups(int $required_count_of_cups): void
    {
        $this->required_count_of_cups = $required_count_of_cups;
    }

    public function getRequiredCountOfCups(): ?int
    {
        return $this->required_count_of_cups;
    }

    public function setRequiredCountOfMatches(int $required_count_of_matches): void
    {
        $this->required_count_of_matches = $required_count_of_matches;
    }

    public function getRequiredCountOfMatches(): ?int
    {
        return $this->required_count_of_matches;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setInfo(string $info): void
    {
        $this->info = $info;
    }

    public function getInfo(): string
    {
        return $this->info;
    }

}
