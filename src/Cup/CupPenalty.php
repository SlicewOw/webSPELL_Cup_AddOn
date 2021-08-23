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
     * @var ?\DateTime $until_date
     */
    private $until_date;

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

    public function setDateUntilPenaltyIsActive(\DateTime $until_date): void
    {
        $this->until_date = $until_date;
    }

    public function getDateUntilPenaltyIsActive(): \DateTime
    {
        if (is_null($this->until_date)) {
            $until_date = new \DateTime("now");
            if ($this->getPenaltyCategory()->isLifetimeBan()) {
                $until_date->add(
                    new \DateInterval("P50Y")
                );
            } else {
                $until_date->add(
                    new \DateInterval("P1D")
                );
            }
            return $until_date;
        }
        return $this->until_date;
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

    public function isActive(): bool
    {
        return (!$this->isDeleted() && new \DateTime("now") < $this->getDateUntilPenaltyIsActive());
    }

}
