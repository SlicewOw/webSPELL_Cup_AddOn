<?php

namespace myrisk\Cup;

use webspell_ng\User;

use myrisk\Cup\Cup;
use myrisk\Cup\CupAwardCategory;
use myrisk\Cup\Team;


class CupAward {

    /**
     * @var ?int $award_id
     */
    private $award_id;

    /**
     * @var CupAwardCategory $category
     */
    private $category;

    /**
     * @var ?Team $team
     */
    private $team;

    /**
     * @var ?User $user
     */
    private $user;

    /**
     * @var ?Cup $cup
     */
    private $cup;

    /**
     * @var ?\DateTime $date
     */
    private $date;

    public function setAwardId(int $award_id): void
    {
        $this->award_id = $award_id;
    }

    public function getAwardId(): ?int
    {
        return $this->award_id;
    }

    public function setCategory(CupAwardCategory $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): CupAwardCategory
    {
        return $this->category;
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

    public function setCup(Cup $cup): void
    {
        $this->cup = $cup;
    }

    public function getCup(): ?Cup
    {
        return $this->cup;
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

}
