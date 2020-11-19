<?php

namespace myrisk\Cup;

class TeamMemberPosition {

    /**
     * @var int $position_id
     */
    private $position_id;

    /**
     * @var string $position
     */
    private $position;

    /**
     * @var int $sort
     */
    private $sort;

    public function setPositionId(int $position_id): void
    {
        $this->position_id = $position_id;
    }

    public function getPositionId(): int
    {
        return $this->position_id;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

}