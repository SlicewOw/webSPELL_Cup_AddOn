<?php

namespace myrisk\Cup;

use myrisk\Cup\Team;

class TeamParticipant extends Participant {

    /**
     * @var Team $team
     */
    private $team;

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

}
