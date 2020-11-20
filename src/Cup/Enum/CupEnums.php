<?php

namespace myrisk\Cup\Enum;

abstract class CupEnums
{

    /**
     * Cup status
     */
    const CUP_STATUS_REGISTRATION = 1;
    const CUP_STATUS_CHECKIN = 2;
    const CUP_STATUS_RUNNING = 3;
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

}
