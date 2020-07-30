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
     * Cup format
     */
    const CUP_FORMAT_BEST_OF_ONE = "bo1";
    const CUP_FORMAT_BEST_OF_THREE = "bo3";
    const CUP_FORMAT_BEST_OF_FIVE = "bo5";

}
