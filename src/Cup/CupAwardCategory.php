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
     * @var int $required_value
     */
    private $required_value = 1;

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

    public function setRequiredValue(int $required_value): void
    {
        $this->required_value = $required_value;
    }

    public function getRequiredValue(): int
    {
        return $this->required_value;
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
