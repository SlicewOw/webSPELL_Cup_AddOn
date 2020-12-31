<?php

namespace myrisk\Cup;

use webspell_ng\User;


class SupportTicketStatus {

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var User $admin
     */
    private $admin;

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

    public function setAdmin(User $admin): void
    {
        $this->admin = $admin;
    }

    public function getAdmin(): User
    {
        return $this->admin;
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
