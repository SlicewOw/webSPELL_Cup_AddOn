<?php

namespace myrisk\Cup;

class CupParticipant {

    private $participant_id = null;
    private $checkin_datetime = null;
    private $start_datetime = null;

    public function setParticipantId(int $participant_id): void
    {
        $this->participant_id = $participant_id;
    }

    public function getParticipantId(): ?int
    {
        return $this->participant_id;
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

}
