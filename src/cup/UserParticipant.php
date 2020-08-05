<?php

namespace myrisk\Cup;

use webspell_ng\User;

class UserParticipant extends Participant {

    private User $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
