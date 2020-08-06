<?php

namespace myrisk\Cup;

use \Respect\Validation\Validator;

use \myrisk\Cup\Participant;
use \myrisk\Cup\Enum\CupEnums;

class Cup {

    /** @var int $cup_id */
    private $cup_id;

    /** @var string $cup_name */
    private $cup_name;

    /** @var string $cup_mode */
    private $cup_mode = CupEnums::CUP_MODE_5ON5;

    /** @var int $cup_status */
    private $cup_status = CupEnums::CUP_STATUS_REGISTRATION;

    /** @var ?Rule $cup_rule */
    private $cup_rule;

    /** @var array<Sponsor> $participants */
    private $cup_sponsors = array();

    /** @var \DateTime $checkin_datetime */
    private $checkin_datetime;

    /** @var \DateTime $start_datetime */
    private $start_datetime;

    /** @var array<mixed> $participants */
    private $participants = array();

    public function setCupId(int $cup_id): void
    {
        $this->cup_id = $cup_id;
    }

    public function getCupId(): int
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

    public function getMode(): ?string
    {
        return $this->cup_mode;
    }

    public function setStatus(int $cup_status): void
    {
        if (!Validator::numericVal()->positive()->between(1, 4)->validate($cup_status)) {
            throw new \InvalidArgumentException("cup_status_value_is_invalid");
        }
        $this->cup_status = $cup_status;
    }

    public function getStatus(): ?int
    {
        return $this->cup_status;
    }

    public function setRule(Rule $rule): void
    {
        $this->cup_rule = $rule;
    }

    public function getRule(): ?Rule
    {
        return $this->cup_rule;
    }

    public function addSponsor(Sponsor $sponsor): void
    {
        array_push($this->cup_sponsors, $sponsor);
    }

    /**
     * @return array<Sponsor>
     */
    public function getSponsors(): array
    {
        return $this->cup_sponsors;
    }

    public function setCheckInDateTime(\DateTime $datetime): void
    {
        $this->checkin_datetime = $datetime;
    }

    public function getCheckInDateTime(): ?\DateTime
    {
        return $this->checkin_datetime;
    }

    public function setStartDateTime(\DateTime $datetime): void
    {
        $this->start_datetime = $datetime;
    }

    public function getStartDateTime(): ?\DateTime
    {
        return $this->start_datetime;
    }

    public function addCupParticipant(Participant $participant): void
    {
        array_push($this->participants, $participant);
    }

    /**
     * @return array<Participant>
     */
    public function getCupParticipants(): array
    {
        return $this->participants;
    }

}