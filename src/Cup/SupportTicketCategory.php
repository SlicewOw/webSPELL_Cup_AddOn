<?php

namespace myrisk\Cup;


class SupportTicketCategory {

    /**
     * @var int $category_id
     */
    private $category_id = null;

    /**
     * @var string $name_german
     */
    private $name_german;

    /**
     * @var string $name_english
     */
    private $name_english;

    /**
     * @var string $template
     */
    private $template = "default";

    public function setCategoryId(int $category_id): void
    {
        $this->category_id = $category_id;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setGermanName(string $german_name): void
    {
        $this->name_german = $german_name;
    }

    public function getGermanName(): string
    {
        return $this->name_german;
    }

    public function setEnglishName(string $english_name): void
    {
        $this->name_english = $english_name;
    }

    public function getEnglishName(): string
    {
        return $this->name_english;
    }
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

}
