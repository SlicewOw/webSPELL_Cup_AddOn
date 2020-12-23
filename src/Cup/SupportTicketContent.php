<?php

namespace myrisk\Cup;

use webspell_ng\User;


class SupportTicketContent {

    /**
     * @var int $content_id
     */
    private $content_id = null;

    /**
     * @var ?\DateTime $date
     */
    private $date;

    /**
     * @var User $poster
     */
    private $poster;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var bool $seen_by_user
     */
    private $seen_by_user = false;

    /**
     * @var bool $seen_by_admin
     */
    private $seen_by_admin = false;

    public function setContentId(int $content_id): void
    {
        $this->content_id = $content_id;
    }

    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTime
    {
        if (is_null($this->date)) {
            return new \DateTime("now");
        }
        return $this->date;
    }

    public function setPoster(User $poster): void
    {
        $this->poster = $poster;
    }

    public function getPoster(): User
    {
        return $this->poster;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setSeenByUser(bool $seen_by_user): void
    {
        $this->seen_by_user = $seen_by_user;
    }

    public function getSeenByUser(): bool
    {
        return $this->seen_by_user;
    }

    public function setSeenByAdmin(bool $seen_by_admin): void
    {
        $this->seen_by_admin = $seen_by_admin;
    }

    public function getSeenByAdmin(): bool
    {
        return $this->seen_by_admin;
    }

}
