<?php

namespace myrisk\Cup;

use webspell_ng\DataStatus;
use webspell_ng\Game;


class MapPool extends DataStatus {

    /**
     * @var ?int $pool_id
     */
    private $pool_id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var Game $game
     */
    private $game;

    /**
     * @var array<string> $maps
     */
    private $maps = array();

    public function setMapPoolId(int $pool_id): void
    {
        $this->pool_id = $pool_id;
    }

    public function getMapPoolId(): ?int
    {
        return $this->pool_id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param array<string> $maps
     */
    public function setMaps(array $maps): void
    {
        $this->maps = $maps;
    }

    /**
     * @return array<string>
     */
    public function getMaps(): array
    {
        return $this->maps;
    }

}
