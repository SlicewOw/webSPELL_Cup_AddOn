<?php

namespace myrisk\Cup;

use DateTime;

use \Respect\Validation\Validator;

use \myrisk\Cup\Participant;
use \myrisk\Cup\Enum\CupEnums;
use \myrisk\Cup\Enum\MatchEnums;

class Cup {

    private $cup_id = null;
    private $cup_name = null;
    private $cup_mode = CupEnums::CUP_MODE_5ON5;
    private $cup_status = CupEnums::CUP_STATUS_REGISTRATION;

    private $checkin_datetime = null;
    private $start_datetime = null;

    private $participants = array();

    public function setCupId(int $cup_id): void
    {
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

    public function setCheckInDateTime(DateTime $datetime): void
    {
        $this->checkin_datetime = $datetime;
    }

    public function getCheckInDateTime(): ?DateTime
    {
        return $this->checkin_datetime;
    }

    public function setStartDateTime(DateTime $datetime): void
    {
        $this->start_datetime = $datetime;
    }

    public function getStartDateTime(): ?DateTime
    {
        return $this->start_datetime;
    }

    public function addCupParticipant(Participant $participant): void
    {
        array_push($this->participants, $participant);
    }

    public function getCupParticipants(): array
    {
        return $this->participants;
    }

}