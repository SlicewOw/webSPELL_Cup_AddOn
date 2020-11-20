<?php

namespace myrisk\Cup;

use webspell_ng\User;

class Admin {

    /**
     * @var int $admin_id
     */
    private $admin_id;

    /**
     * @var int $right
     */
    private $right;

    /**
     * @var User $user
     */
    private $user;

    public function setAdminId(int $admin_id): void
    {
        $this->admin_id = $admin_id;
    }

    public function getAdminId(): int
    {
        return $this->admin_id;
    }

    public function setRight(int $right): void
    {
        $this->right = $right;
    }

    public function getRight(): int
    {
        return $this->right;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}
