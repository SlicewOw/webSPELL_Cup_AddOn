<?php

namespace myrisk\Cup;

class Sponsor {

    private int $cup_sponsor_id;
    private int $sponsor_id;

    public function setCupSponsorId(int $cup_sponsor_id): void
    {
        $this->cup_sponsor_id = $cup_sponsor_id;
    }

    public function getCupSponsorId(): int
    {
        return $this->cup_sponsor_id;
    }

    public function setSponsorId(int $sponsor_id): void
    {
        $this->sponsor_id = $sponsor_id;
    }

    public function getSponsorId(): int
    {
        return $this->sponsor_id;
    }


}
