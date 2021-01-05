<?php

namespace myrisk\Cup;

use webspell_ng\User;


class TeamLog {

    /**
     * @var string $team_name
     */
    private $team_name;

    /**
     * @var ?\DateTime $date
     */
    private $date;

    /**
     * @var ?User $kicked_by_user
     */
    private $kicked_by_user = null;

    /**
     * @var ?int $parent_id
     */
    private $parent_id;

    /**
     * @var string $info
     */
    private $info;

    public function setTeamName(string $team_name): void
    {
        $this->team_name = $team_name;
    }

    public function getTeamName(): string
    {
        return $this->team_name;
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

    public function setKickedByUser(User $kicked_by_user): void
    {
        $this->kicked_by_user = $kicked_by_user;
    }

    public function getKickedByUser(): ?User
    {
        return $this->kicked_by_user;
    }

    public function setParentId(int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
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
