<?php

namespace myrisk\Cup;

use webspell_ng\User;

use myrisk\Cup\Team;
use myrisk\Cup\Enum\MatchEnums;


class CupMatch {

    /**
     * @var int $match_id
     */
    private $match_id;

    /**
     * @var int $match_identifier
     */
    private $match_identifier = 1;

    /**
     * @var string $format
     */
    private $format = MatchEnums::CUP_FORMAT_BEST_OF_ONE;

    /**
     * @var \DateTime $date
     */
    private $date;

    /**
     * @var Team|User|null $left_team
     */
    private $left_team = null;

    /**
     * @var Team|User|null $right_team
     */
    private $right_team = null;

    /**
     * @var array<string> $maps
     */
    private $maps = array();

    /**
     * @var bool $is_active
     */
    private $is_active = false;

    /**
     * @var bool $left_team_confirmed
     */
    private $left_team_confirmed = false;

    /**
     * @var bool $right_team_confirmed
     */
    private $right_team_confirmed = false;

    /**
     * @var bool $admin_confirmed
     */
    private $admin_confirmed = false;

    public function setMatchId(int $match_id): void
    {
        $this->match_id = $match_id;
    }

    public function getMatchId(): ?int
    {
        return $this->match_id;
    }

    public function setMatchIdentifier(int $match_identifier): void
    {
        $this->match_identifier = $match_identifier;
    }

    public function getMatchIdentifier(): int
    {
        return $this->match_identifier;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param Team|User $team
     */
    public function setLeftTeam($team): void
    {
        $this->left_team = $team;
    }

    /**
     * @return Team|User|null
     */
    public function getLeftTeam()
    {
        return $this->left_team;
    }

    /**
     * @param Team|User $team
     */
    public function setRightTeam($team): void
    {
        $this->right_team = $team;
    }

    /**
     * @return Team|User|null
     */
    public function getRightTeam()
    {
        return $this->right_team;
    }

    /**
     * @param array<string> $maps
     */
    public function setMaps(array $maps): void
    {
        $this->maps = $maps;
    }

    /**
     * @return array<string>
     */
    public function getMaps(): array
    {
        return $this->maps;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setLeftTeamConfirmed(bool $is_confirmed): void
    {
        $this->left_team_confirmed = $is_confirmed;
    }

    public function isConfirmedByLeftTeam(): bool
    {
        return $this->left_team_confirmed;
    }

    public function setRightTeamConfirmed(bool $is_confirmed): void
    {
        $this->right_team_confirmed = $is_confirmed;
    }

    public function isConfirmedByRightTeam(): bool
    {
        return $this->right_team_confirmed;
    }

    public function setAdminConfirmed(bool $is_confirmed): void
    {
        $this->admin_confirmed = $is_confirmed;
    }

    public function isConfirmedByAdmin(): bool
    {
        return $this->admin_confirmed;
    }

    public function isFinished(): bool
    {
        return $this->admin_confirmed || ($this->left_team_confirmed && $this->right_team_confirmed);
    }

}