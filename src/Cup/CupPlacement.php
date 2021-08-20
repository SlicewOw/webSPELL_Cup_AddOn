<?php

namespace myrisk\Cup;

use webspell_ng\User;

use myrisk\Cup\Team;


class CupPlacement {

    /**
     * @var ?int $placement_id
     */
    private $placement_id;

    /**
     * @var string $ranking
     */
    private $ranking;

    /**
     * @var User|Team $receiver
     */
    private $receiver;

    public function setPlacementId(int $placement_id): void
    {
        $this->placement_id = $placement_id;
    }

    public function getPlacementId(): ?int
    {
        return $this->placement_id;
    }

    public function setRanking(string $ranking): void
    {
        $this->ranking = $ranking;
    }

    public function getRanking(): ?string
    {
        return $this->ranking;
    }

    /**
     * @param User|Team $receiver
     */
    public function setReceiver($receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return User|Team
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

}
