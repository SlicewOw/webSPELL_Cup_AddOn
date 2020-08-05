<?php

namespace myrisk\Cup;

use myrisk\Cup\User;

class UserParticipant extends Participant {

    private User $user;

    public function setUser(\myrisk\Cup\User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
