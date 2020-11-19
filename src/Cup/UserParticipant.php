<?php

namespace myrisk\Cup;

use webspell_ng\User;

class UserParticipant extends Participant {

    /**
     * @var User $user
     */
    private $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
