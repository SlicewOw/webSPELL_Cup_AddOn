<?php

namespace myrisk\Cup;

use webspell_ng\User;


class SupportTicketStatus {

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var ?\DateTime $date
     */
    private $date = null;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

}
