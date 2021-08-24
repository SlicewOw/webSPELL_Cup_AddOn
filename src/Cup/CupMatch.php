<?php

namespace myrisk\Cup;

use webspell_ng\User;

use myrisk\Cup\MapVote;
use myrisk\Cup\Team;
use myrisk\Cup\Enum\MatchEnums;


class CupMatch {

    /**
     * @var int $match_id
     */
    private $match_id;

    /**
     * @var int $round_identifier
     */
    private $round_identifier = 1;

    /**
     * @var int $match_identifier
     */
    private $match_identifier = 1;

    /**
     * @var bool $is_winner_bracket
     */
    private $is_winner_bracket = true;

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
     * @var bool $left_team_walkover
     */
    private $left_team_walkover = false;

    /**
     * @var int $left_team_result
     */
    private $left_team_result = 0;

    /**
     * @var Team|User|null $right_team
     */
    private $right_team = null;

    /**
     * @var bool $right_team_walkover
     */
    private $right_team_walkover = false;

    /**
     * @var int $right_team_result
     */
    private $right_team_result = 0;

    /**
     * @var bool $is_map_vote_enabled
     */
    private $is_map_vote_enabled = false;

    /**
     * @var ?MapVote $map_vote
     */
    private $map_vote;

    /**
     * @var array<string> $server_details
     */
    private $server_details = array();

    /**
     * @var bool $is_active
     */
    private $is_active = false;

    /**
     * @var bool $is_admin_match
     */
    private $is_admin_match = false;

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

    public function setRoundIdentifier(int $round_identifier): void
    {
        $this->round_identifier = $round_identifier;
    }

    public function getRoundIdentifier(): int
    {
        return $this->round_identifier;
    }

    public function setMatchIdentifier(int $match_identifier): void
    {
        $this->match_identifier = $match_identifier;
    }

    public function getMatchIdentifier(): int
    {
        return $this->match_identifier;
    }

    public function setIsWinnerBracket(bool $is_winner_bracket): void
    {
        $this->is_winner_bracket = $is_winner_bracket;
    }

    public function isWinnerBracket(): bool
    {
        return $this->is_winner_bracket;
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

    public function setIsLeftTeamWalkover(bool $left_team_walkover): void
    {
        $this->left_team_walkover = $left_team_walkover;
    }

    public function isLeftTeamWalkover(): bool
    {
        return $this->left_team_walkover;
    }

    public function setLeftTeamResult(int $left_team_result): void
    {
        $this->left_team_result = $left_team_result;
    }

    public function getLeftTeamResult(): int
    {
        return $this->left_team_result;
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

    public function setIsRightTeamWalkover(bool $right_team_walkover): void
    {
        $this->right_team_walkover = $right_team_walkover;
    }

    public function isRightTeamWalkover(): bool
    {
        return $this->right_team_walkover;
    }

    public function setRightTeamResult(int $right_team_result): void
    {
        $this->right_team_result = $right_team_result;
    }

    public function getRightTeamResult(): int
    {
        return $this->right_team_result;
    }

    public function setIsMapVoteEnabled(bool $is_map_vote_enabled): void
    {
        $this->is_map_vote_enabled = $is_map_vote_enabled;
    }

    public function isMapVoteEnabled(): bool
    {
        return $this->is_map_vote_enabled;
    }

    public function setMapVote(MapVote $map_vote): void
    {
        $this->map_vote = $map_vote;
    }

    public function getMapVote(): MapVote
    {
        if (is_null($this->map_vote)) {
            return new MapVote();
        }
        return $this->map_vote;
    }

    /**
     * @param array<mixed> $server_details
     */
    public function setServerDetails(array $server_details): void
    {
        $this->server_details = $server_details;
    }

    /**
     * @return array<mixed>
     */
    public function getServerDetails(): array
    {
        return $this->server_details;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsAdminMatch(bool $is_admin_match): void
    {
        $this->is_admin_match = $is_admin_match;
    }

    public function isAdminMatch(): bool
    {
        return $this->is_admin_match;
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