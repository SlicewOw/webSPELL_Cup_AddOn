<?php

namespace myrisk\Cup;

use DateTime;

use \myrisk\Cup\CupParticipant;

class Cup {

    private $cup_id = null;
    private $cup_name = null;
    private $cup_format = null;
    private $cup_status = null;

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

    public function setFormat(string $cup_format): void
    {
        $this->cup_format = $cup_format;
    }

    public function getFormat(): ?string
    {
        return $this->cup_format;
    }

    public function setStatus(string $cup_status): void
    {
        $this->cup_status = $cup_status;
    }

    public function getStatus(): ?string
    {
        return $this->cup_status;
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

    public function addCupParticipant(\myrisk\Cup\Participant $participant): void
    {
        array_push($this->participants, $participant);
    }

    public function getCupParticipants(): array
    {
        return $this->participants;
    }

}