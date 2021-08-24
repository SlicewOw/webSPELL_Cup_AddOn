<?php

namespace myrisk\Cup;


class MapVote {

    /**
     * @var array<string> $all_maps
     */
    private $all_maps = array();

    /**
     * @var array<string> $open_maps
     */
    private $open_maps = array();

    /**
     * @var array<string> $banned_maps_of_left_team
     */
    private $banned_maps_of_left_team = array();

    /**
     * @var array<string> $banned_maps_of_right_team
     */
    private $banned_maps_of_right_team = array();

    /**
     * @var array<string> $picked_maps
     */
    private $picked_maps = array();

    /**
     * @param array<mixed> $map_vote
     */
    public function __construct(array $map_vote = array())
    {
        if (isset($map_vote["list"])) {
            $this->all_maps = $map_vote["list"];
        }
        if (isset($map_vote["open"])) {
            $this->open_maps = $map_vote["open"];
        }
        if (isset($map_vote["banned"])) {
            $banned_maps = $map_vote["banned"];
            if (isset($banned_maps["team1"])) {
                $this->banned_maps_of_left_team = $banned_maps["team1"];
            }
            if (isset($banned_maps["team2"])) {
                $this->banned_maps_of_right_team = $banned_maps["team2"];
            }
        }
        if (isset($map_vote["picked"])) {
            $this->picked_maps = $map_vote["picked"];
        }
    }

    /**
     * @return array<string>
     */
    public function getAllMaps(): array
    {
        return $this->all_maps;
    }

    /**
     * @return array<string>
     */
    public function getOpenMaps(): array
    {
        return $this->open_maps;
    }

    /**
     * @return array<string>
     */
    public function getBannedMapsOfLeftTeam(): array
    {
        return $this->banned_maps_of_left_team;
    }

    /**
     * @return array<string>
     */
    public function getBannedMapsOfRightTeam(): array
    {
        return $this->banned_maps_of_right_team;
    }

    /**
     * @return array<string>
     */
    public function getPickedMaps(): array
    {
        return $this->picked_maps;
    }

    public function getSerialized(): string
    {
        return serialize(
            array(
                'list' => $this->getAllMaps(),
                'open' => $this->getOpenMaps(),
                'banned' => array(
                    "team1" => $this->getBannedMapsOfLeftTeam(),
                    "team2" => $this->getBannedMapsOfRightTeam()
                ),
                'picked' => $this->getPickedMaps()
            )
        );
    }

}
