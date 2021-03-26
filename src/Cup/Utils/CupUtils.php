<?php

namespace myrisk\Cup\Utils;

use myrisk\Cup\Cup;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Utils\TeamUtils;


class CupUtils {

    public static function getPhaseOfCup(Cup $cup): string
    {

        $cup_status = $cup->getStatus();

        if ($cup_status == 4) {
            $phase = CupEnums::CUP_PHASE_FINISHED;
        } else if ($cup_status > 1) {
            $phase = CupEnums::CUP_PHASE_RUNNING;
        } else {
            $phase = self::getPhaseOfCupWhichIsNotStartedYet($cup);
        }

        return $phase;

    }

    private static function getPhaseOfCupWhichIsNotStartedYet(Cup $cup): string
    {

        $now = new \DateTime("now");

        if (($cup->getMode() == CupEnums::CUP_MODE_1ON1) || TeamUtils::isUserAnyTeamAdmin()) {
            $phase = ($now <= $cup->getCheckInDateTime()) ? CupEnums::CUP_PHASE_ADMIN_REGISTER : CupEnums::CUP_PHASE_ADMIN_CHECKIN;
        } else if (TeamUtils::isUserAnyTeamMember()) {
            $phase = ($now <= $cup->getCheckInDateTime()) ? CupEnums::CUP_PHASE_REGISTER : CupEnums::CUP_PHASE_CHECKIN;
        } else {
            $phase = CupEnums::CUP_PHASE_RUNNING;
        }

        return $phase;

    }

}
