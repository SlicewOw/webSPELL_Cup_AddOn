<?php

namespace myrisk\Cup;

use Respect\Validation\Validator;

use webspell_ng\Game;

use myrisk\Cup\Admin;
use myrisk\Cup\CupSponsor;
use myrisk\Cup\MapPool;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Utils\CupUtils;


class Cup {

    /**
     * @var ?int $cup_id
     */
    private $cup_id;

    /**
     * @var string $cup_name
     */
    private $cup_name;

    /**
     * @var string $cup_mode
     */
    private $cup_mode = CupEnums::CUP_MODE_5ON5;

    /**
     * @var int $cup_size
     */
    private $cup_size = CupEnums::CUP_SIZE_32;

    /**
     * @var int $cup_status
     */
    private $cup_status = CupEnums::CUP_STATUS_REGISTRATION;

    /**
     * @var Game $game
     */
    private $game;

    /**
     * @var ?Rule $cup_rule
     */
    private $cup_rule;

    /**
     * @var bool $map_vote_enabled
     */
    private $map_vote_enabled = false;

    /**
     * @var ?MapPool $map_pool
     */
    private $map_pool;

    /**
     * @var int $maximum_of_penalty_points
     */
    private $maximum_of_penalty_points = 99999;

    /**
     * @var array<CupSponsor> $cup_sponsors
     */
    private $cup_sponsors = array();

    /**
     * @var array<Admin> $admins
     */
    private $admins = array();

    /**
     * @var \DateTime $checkin_datetime
     */
    private $checkin_datetime;

    /**
     * @var \DateTime $start_datetime
     */
    private $start_datetime;

    /**
     * @var array<mixed> $participants
     */
    private $participants = array();

    /**
     * @var string $description
     */
    private $description = "";

    /**
     * @var bool $is_saved
     */
    private $is_saved = false;

    /**
     * @var bool $is_admin_cup
     */
    private $is_admin_cup = false;

    /**
     * @var bool $is_third_placemenent_set
     */
    private $is_third_placemenent_set = false;

    /**
     * @var bool $is_using_servers
     */
    private $is_using_servers = false;

    public function setCupId(int $cup_id): void
    {
        if (!Validator::numericVal()->positive()->min(1)->validate($cup_id)) {
            throw new \InvalidArgumentException("cup_id_value_is_invalid");
        }
        $this->cup_id = $cup_id;
    }

    public function getCupId(): ?int
    {
        return $this->cup_id;
    }

    public function setName(string $cup_name): void
    {
        $this->cup_name = $cup_name;
    }

    public function getName(): ?string
    {
        return $this->cup_name;
    }

    public function setMode(string $cup_mode): void
    {
        $this->cup_mode = $cup_mode;
    }

    public function getMode(): string
    {
        return $this->cup_mode;
    }

    public function isTeamCup(): bool
    {
        return $this->getMode() != CupEnums::CUP_MODE_1ON1;
    }

    // TODO: Implement logic to set this setting per cup (e.g. allow ONLY 5 players per team vs. >= 5 players per team for a 5on5 cup)
    public function getRequiredPlayersPerTeam(): int
    {
        $exploded_mode = explode("on", $this->getMode());
        return (int) $exploded_mode[0];
    }

    public function setSize(int $size): void
    {
        $allowed_sizes = array(
            CupEnums::CUP_SIZE_2,
            CupEnums::CUP_SIZE_4,
            CupEnums::CUP_SIZE_8,
            CupEnums::CUP_SIZE_16,
            CupEnums::CUP_SIZE_32
        );
        if (in_array($size, $allowed_sizes)) {
            $this->cup_size = $size;
        }

    }

    public function getSize(): int
    {
        return $this->cup_size;
    }

    public function getTotalRoundCount(): int
    {
        return (int) log($this->getSize(), 2);
    }

    public function setStatus(int $cup_status): void
    {
        if (!Validator::numericVal()->positive()->between(1, 4)->validate($cup_status)) {
            throw new \InvalidArgumentException("cup_status_value_is_invalid");
        }
        $this->cup_status = $cup_status;
    }

    public function getStatus(): int
    {
        return $this->cup_status;
    }

    public function isRunning(): bool
    {
        return ($this->getStatus() > 1 && $this->getStatus() < 4);
    }

    public function isFinished(): bool
    {
        return ($this->getStatus() == 4);
    }

    public function getPhase(): string
    {
        return CupUtils::getPhaseOfCup($this);
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setRule(Rule $rule): void
    {
        $this->cup_rule = $rule;
    }

    public function getRule(): ?Rule
    {
        return $this->cup_rule;
    }

    public function setMapVoteEnabled(bool $map_vote_enabled): void
    {
        $this->map_vote_enabled = $map_vote_enabled;
    }

    public function isMapVoteDone(): bool
    {
        return $this->map_vote_enabled;
    }

    public function setMapPool(MapPool $map_pool): void
    {
        $this->map_pool = $map_pool;
    }

    public function getMapPool(): ?MapPool
    {
        return $this->map_pool;
    }

    public function setMaximumOfPenaltyPoints(int $maximum_of_penalty_points): void
    {
        $this->maximum_of_penalty_points = $maximum_of_penalty_points;
    }

    public function getMaximumOfPenaltyPoints(): int
    {
        return $this->maximum_of_penalty_points;
    }

    public function addSponsor(CupSponsor $sponsor): void
    {
        array_push($this->cup_sponsors, $sponsor);
    }

    /**
     * @return array<CupSponsor>
     */
    public function getSponsors(): array
    {
        return $this->cup_sponsors;
    }

    public function addAdmin(Admin $admin): void
    {
        array_push($this->admins, $admin);
    }

    /**
     * @return array<Admin>
     */
    public function getAdmins(): array
    {
        return $this->admins;
    }

    public function setCheckInDateTime(\DateTime $datetime): void
    {
        $this->checkin_datetime = $datetime;
    }

    public function getCheckInDateTime(): \DateTime
    {
        return $this->checkin_datetime;
    }

    public function setStartDateTime(\DateTime $datetime): void
    {
        $this->start_datetime = $datetime;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->start_datetime;
    }

    /**
     * @param array<UserParticipant|TeamParticipant> $participants
     */
    public function setCupParticipants(array $participants): void
    {
        $this->participants = $participants;
    }

    /**
     * @return array<UserParticipant|TeamParticipant>
     */
    public function getCupParticipants(): array
    {
        return $this->participants;
    }

    /**
     * @return array<UserParticipant|TeamParticipant>
     */
    public function getCheckedInCupParticipants(): array
    {

        $checked_in_participants = array();

        $tmp_participants = $this->getCupParticipants();
        foreach ($tmp_participants as $participant) {
            if ($participant->getCheckedIn()) {
                array_push($checked_in_participants, $participant);
            }
        }

        return $checked_in_participants;

    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setIsSaved(bool $is_saved): void
    {
        $this->is_saved = $is_saved;
    }

    public function isSaved(): bool
    {
        return $this->is_saved;
    }

    public function setIsAdminCup(bool $is_admin_cup): void
    {
        $this->is_admin_cup = $is_admin_cup;
    }

    public function isAdminCup(): bool
    {
        return $this->is_admin_cup;
    }

    public function isThirdPlacementSet(): bool
    {
        return $this->is_third_placemenent_set;
    }

    public function setIsUsingServers(bool $is_using_servers): void
    {
        $this->is_using_servers = $is_using_servers;
    }

    public function isUsingServers(): bool
    {
        return $this->is_using_servers;
    }

}