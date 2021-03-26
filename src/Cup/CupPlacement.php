<?php

namespace myrisk\Cup;


class CupPlacement {

    /**
     * @var ?int $placement_id
     */
    private $placement_id;

    /**
     * @var string $ranking
     */
    private $ranking;

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

}
