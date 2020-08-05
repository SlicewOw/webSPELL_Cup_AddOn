<?php

namespace myrisk\Cup;

class Participant {

    private int $participant_id;
    private int $team_id;
    private bool $is_checked_in = false;
    private \DateTime $register_datetime;
    private \DateTime $checkin_datetime;

    public function setParticipantId(int $participant_id): void
    {
        $this->participant_id = $participant_id;
    }

    public function getParticipantId(): ?int
    {
        return $this->participant_id;
    }

    public function setTeamId(int $team_id): void
    {
        $this->team_id = $team_id;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
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
