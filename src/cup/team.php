<?php

namespace myrisk\Cup;

class Team {

    private $team_id = null;
    private $team_name = null;
    private $team_tag = null;
    private $country = "de";
    private $homepage = null;

    public function setTeamId(int $team_id): void
    {
        $this->team_id = $team_id;
    }

    public function getTeamId(): ?int
    {
        return $this->team_id;
    }

    public function setName(string $team_name): void
    {
        $this->team_name = $team_name;
    }

    public function getName(): ?string
    {
        return $this->team_name;
    }

    public function setTag(string $team_tag): void
    {
        $this->team_tag = $team_tag;
    }

    public function getTag(): ?string
    {
        return $this->team_tag;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setHomepage(string $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

}