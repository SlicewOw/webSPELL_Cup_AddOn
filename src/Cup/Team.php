<?php

namespace myrisk\Cup;

use \myrisk\Cup\TeamMember;
use \myrisk\Cup\Enum\TeamEnums;


class Team {

    /**
     * @var int $team_id
     */
    private $team_id;

    /**
     * @var \DateTime $date
     */
    private $date;

    /**
     * @var string $team_name
     */
    private $team_name;

    /**
     * @var string $team_tag
     */
    private $team_tag;

    /**
     * @var string $country
     */
    private $country = "de";

    /**
     * @var ?string $homepage
     */
    private $homepage;

    /**
     * @var ?string $logotype
     */
    private $logotype;

    /**
     * @var bool $is_deleted
     */
    private $is_deleted = false;

    /**
     * @var array<TeamMember> $members
     */
    private $members = array();

    public function setTeamId(int $team_id): void
    {
        $this->team_id = $team_id;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setCreationDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getCreationDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setName(string $team_name): void
    {
        $this->team_name = $team_name;
    }

    public function getName(): ?string
    {
        return $this->team_name;
    }

    public function setTag(string $team_tag): void
    {
        $this->team_tag = $team_tag;
    }

    public function getTag(): ?string
    {
        return $this->team_tag;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setHomepage(string $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function setLogotype(string $logotype): void
    {
        $this->logotype = $logotype;
    }

    public function getLogotype(): ?string
    {
        return $this->logotype;
    }

    public function setIsDeleted(bool $is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }

    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

    public function addMember(TeamMember $members): void
    {
        array_push($this->members, $members);
    }

    /**
     * @return array<TeamMember>
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    public function getTeamAdmin(): ?TeamMember
    {

        $tmp_members = $this->getMembers();
        foreach ($tmp_members as $member) {
            if ($member->getPosition()->getPosition() == TeamEnums::TEAM_MEMBER_POSITION_ADMIN) {
                return $member;
            }
        }

        return null;

    }

}
