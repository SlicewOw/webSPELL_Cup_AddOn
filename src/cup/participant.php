<?php

namespace myrisk\Cup;

class Participant {

    private $participant_id = null;
    private $register_datetime = null;
    private $checkin_datetime = null;

    public function setParticipantId(int $participant_id): void
    {
        $this->participant_id = $participant_id;
    }

    public function getParticipantId(): ?int
    {
        return $this->participant_id;
    }

    public function setRegisterDateTime(\DateTime $datetime): void
    {
        $this->register_datetime = $datetime;
    }

    public function getRegisterDateTime(): ?\DateTime
    {
        return $this->register_datetime;
    }

    public function setCheckInDateTime(\DateTime $datetime): void
    {
        $this->checkin_datetime = $datetime;
    }

    public function getCheckInDateTime(): ?\DateTime
    {
        return $this->checkin_datetime;
    }

}
