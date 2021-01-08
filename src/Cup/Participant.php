<?php

namespace myrisk\Cup;

class Participant {

    /**
     * @var int $participant_id
     */
    private $participant_id;

    /**
     * @var bool $is_checked_in
     */
    private $is_checked_in = false;

    /**
     * @var ?\DateTime $register_datetime
     */
    private $register_datetime;

    /**
     * @var ?\DateTime $checkin_datetime
     */
    private $checkin_datetime;

    public function setParticipantId(int $participant_id): void
    {
        $this->participant_id = $participant_id;
    }

    public function getParticipantId(): ?int
    {
        return $this->participant_id;
    }

    public function setCheckedIn(bool $is_checked_in): void
    {
        $this->is_checked_in = $is_checked_in;
    }

    public function getCheckedIn(): bool
    {
        return $this->is_checked_in;
    }

    public function setRegisterDateTime(\DateTime $datetime): void
    {
        $this->register_datetime = $datetime;
    }

    public function getRegisterDateTime(): \DateTime
    {
        if (is_null($this->register_datetime)) {
            return new \DateTime("now");
        }
        return $this->register_datetime;
    }

    public function setCheckInDateTime(?\DateTime $datetime): void
    {
        $this->checkin_datetime = $datetime;
    }

    public function getCheckInDateTime(): ?\DateTime
    {
        return $this->checkin_datetime;
    }

}
