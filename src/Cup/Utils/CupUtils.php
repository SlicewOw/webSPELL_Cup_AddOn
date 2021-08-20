<?php

namespace myrisk\Cup\Utils;

use myrisk\Cup\Cup;
use myrisk\Cup\TeamParticipant;
use myrisk\Cup\UserParticipant;
use myrisk\Cup\Enum\CupEnums;
use myrisk\Cup\Utils\TeamUtils;


class CupUtils {

    public static function getPhaseOfCup(Cup $cup): string
    {

        if ($cup->isFinished()) {
            $phase = CupEnums::CUP_PHASE_FINISHED;
        } else if ($cup->isRunning()) {
            $phase = CupEnums::CUP_PHASE_RUNNING;
        } else {
            $phase = self::getPhaseOfCupWhichIsNotStartedYet($cup);
        }

        return $phase;

    }

    private static function getPhaseOfCupWhichIsNotStartedYet(Cup $cup): string
    {

        $now = new \DateTime("now");

        if (!$cup->isTeamCup() || TeamUtils::isUserAnyTeamAdmin()) {
            $phase = ($now <= $cup->getCheckInDateTime()) ? CupEnums::CUP_PHASE_ADMIN_REGISTER : CupEnums::CUP_PHASE_ADMIN_CHECKIN;
        } else if (TeamUtils::isUserAnyTeamMember()) {
            $phase = ($now <= $cup->getCheckInDateTime()) ? CupEnums::CUP_PHASE_REGISTER : CupEnums::CUP_PHASE_CHECKIN;
        } else {
            $phase = CupEnums::CUP_PHASE_RUNNING;
        }

        return $phase;

    }

    public static function getSizeByCheckedInParticipants(int $checked_in_participants): int
    {

        if ($checked_in_participants < 3) {
            $new_cup_size = 2;
        } else if ($checked_in_participants < 5) {
            $new_cup_size = 4;
        } else if ($checked_in_participants < 9) {
            $new_cup_size = 8;
        } else if ($checked_in_participants < 17) {
            $new_cup_size = 16;
        } else if ($checked_in_participants < 33) {
            $new_cup_size = 32;
        } else if ($checked_in_participants < 65) {
            $new_cup_size = 64;
        } else {
            throw new \InvalidArgumentException("cannot_map_checked_in_cup_participants_to_cup_size");
        }

        return $new_cup_size;

    }

    /**
     * @param array<UserParticipant|TeamParticipant> $participants
     * @return array<UserParticipant|TeamParticipant|int>
     */
    public static function getParticipantsInRandomOrder(int $cup_size, array $participants): array
    {

        if (empty($participants)) {
            return array();
        }

        $participants_in_random_order = array_fill(0, $cup_size, 0);

        $participants = $participants;
        foreach ($participants as $index => $participant) {
            $participants_in_random_order[$index] = $participant;
        }

        shuffle($participants_in_random_order);

        $participants_count = count($participants_in_random_order);
        for ($x = 0; $x < $participants_count; $x += 2) {

            $left_team_is_walkover = (!isset($participants_in_random_order[$x]) || is_numeric($participants_in_random_order[$x]));
            $right_team_is_walkover = (!isset($participants_in_random_order[$x + 1]) || is_numeric($participants_in_random_order[$x + 1]));

            if ($left_team_is_walkover && $right_team_is_walkover) {
                return self::getParticipantsInRandomOrder($cup_size, $participants);
            }

        }

        return $participants_in_random_order;

    }

}
