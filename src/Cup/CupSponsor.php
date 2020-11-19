<?php

namespace myrisk\Cup;

use \webspell_ng\Sponsor;

class CupSponsor {

    /**
     * @var int $cup_sponsor_id
     */
    private $cup_sponsor_id;

    /**
     * @var Sponsor $sponsor
     */
    private $sponsor;

    public function setCupSponsorId(int $cup_sponsor_id): void
    {
        $this->cup_sponsor_id = $cup_sponsor_id;
    }

    public function getCupSponsorId(): int
    {
        return $this->cup_sponsor_id;
    }

    public function setSponsor(Sponsor $sponsor): void
    {
        $this->sponsor = $sponsor;
    }

    public function getSponsor(): Sponsor
    {
        return $this->sponsor;
    }


}
