<?php

namespace myrisk\Cup;

use webspell_ng\User;

use myrisk\Cup\CupPenaltyCategory;
use myrisk\Cup\Team;


class CupPenalty {

    /**
     * @var ?int $penalty_id
     */
    private $penalty_id;

    /**
     * @var CupPenaltyCategory $category
     */
    private $category;

    /**
     * @var User $admin
     */
    private $admin;

    /**
     * @var ?\DateTime $date
     */
    private $date;

    /**
     * @var int $duration_time
     */
    private $duration_time = 60 * 60 * 24;

    /**
     * @var ?Team $team
     */
    private $team = null;

    /**
     * @var ?User $user
     */
    private $user = null;

    /**
     * @var string $comment
     */
    private $comment;

    /**
     * @var bool $is_deleted
     */
    private $is_deleted = false;

    public function setPenaltyId(int $penalty_id): void
    {
        $this->penalty_id = $penalty_id;
    }

    public function getPenaltyId(): ?int
    {
        return $this->penalty_id;
    }

    public function setPenaltyCategory(CupPenaltyCategory $category): void
    {
        $this->category = $category;
    }

    public function getPenaltyCategory(): CupPenaltyCategory
    {
        return $this->category;
    }

    public function setAdmin(User $admin): void
    {
        $this->admin = $admin;
    }

    public function getAdmin(): User
    {
        return $this->admin;
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

    public function setDurationTime(int $duration_time): void
    {
        $this->duration_time = $duration_time;
    }

    public function getDurationTime(): int
    {
        return $this->duration_time;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setIsDeleted(bool $is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }

    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

}
