<?php

namespace myrisk\Cup;

use myrisk\Cup\Team;

class TeamParticipant extends Participant {

    private $team = null;

    public function setTeam(\myrisk\Cup\Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

}
