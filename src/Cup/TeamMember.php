<?php

namespace myrisk\Cup;

use \webspell_ng\User;

class TeamMember {

    /** @var int $member_id */
    private $member_id;

    /** @var User $user */
    private $user;

    /** @var int $position */
    private $position;

    /** @var \DateTime $join_date */
    private $join_date;

    /** @var ?\DateTime $left_date */
    private $left_date = null;

    /** @var ?int $kick_id */
    private $kick_id = null;

    /** @var bool $is_active */
    private $is_active = true;

    public function setMemberId(int $member_id): void
    {
        $this->member_id = $member_id;
    }

    public function getMemberId(): int
    {
        return $this->member_id;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setJoinDate(\DateTime $join_date): void
    {
        $this->join_date = $join_date;
    }

    public function getJoinDate(): \DateTime
    {
        return $this->join_date;
    }

    public function setLeftDate(\DateTime $left_date): void
    {
        $this->left_date = $left_date;
    }

    public function getLeftDate(): ?\DateTime
    {
        return $this->left_date;
    }

    public function setKickId(int $kick_id): void
    {
        $this->kick_id = $kick_id;
    }

    public function getKickId(): ?int
    {
        return $this->kick_id;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

}