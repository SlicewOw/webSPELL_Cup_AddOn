<?php

namespace myrisk\Cup\Enum;

abstract class CupEnums
{

    /**
     * Cup status
     */
    const CUP_STATUS_REGISTRATION = 1;
    const CUP_STATUS_GROUPSTAGE = 2;
    const CUP_STATUS_PLAYOFFS = 3;
    const CUP_STATUS_FINISHED = 4;

    /**
     * Cup phases
     */
    const CUP_PHASE_ADMIN_REGISTER = "admin_register";
    const CUP_PHASE_REGISTER = "register";
    const CUP_PHASE_ADMIN_CHECKIN = "admin_checkin";
    const CUP_PHASE_CHECKIN = "checkin";
    const CUP_PHASE_RUNNING = "running";
    const CUP_PHASE_FINISHED = "finished";

    /**
     * Cup mode
     */
    const CUP_MODE_1ON1 = "1on1";
    const CUP_MODE_2ON2 = "2on2";
    const CUP_MODE_5ON5 = "5on5";
    const CUP_MODE_11ON11 = "11on11";

    /**
     * Cup size
     */
    const CUP_SIZE_2 = 2;
    const CUP_SIZE_4 = 4;
    const CUP_SIZE_8 = 8;
    const CUP_SIZE_16 = 16;
    const CUP_SIZE_32 = 32;
    const CUP_SIZE_64 = 64;

    /**
     * Logs
     */
    const CUP_PARTICIPANT_JOINED = "cup_joined";
    const CUP_PARTICIPANT_CHECKED_IN = "cup_checked_in";
    const CUP_PARTICIPANT_LEFT = "cup_left";

    /**
     * Cup placements
     */
    const CUP_PLACEMENT_1 = "1";
    const CUP_PLACEMENT_2 = "2";
    const CUP_PLACEMENT_3 = "3";
    const CUP_PLACEMENT_4 = "4";
    const CUP_PLACEMENT_3_4 = "3-4";
    const CUP_PLACEMENT_5_8 = "5-8";
    const CUP_PLACEMENT_9_16 = "9-16";
    const CUP_PLACEMENT_17_32 = "17-32";
    const CUP_PLACEMENT_33_64 = "33-64";

}
