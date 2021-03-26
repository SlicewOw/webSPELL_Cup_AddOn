<?php

namespace myrisk\Cup\Utils;

use webspell_ng\UserSession;
use webspell_ng\Handler\UserHandler;

use myrisk\Cup\Team;
use myrisk\Cup\Handler\TeamHandler;

class TeamUtils {

    public static function isUserTeamMember(Team $team): bool
    {

        if (UserSession::getUserId() < 1) {
            return false;
        }

        $teams_of_user = TeamHandler::getTeamsOfUser(
            UserHandler::getUserByUserId(UserSession::getUserId())
        );

        foreach ($teams_of_user as $team_of_user) {

            if ($team->getTeamId() == $team_of_user->getTeamId()) {
                return true;
            }

        }

        return false;

    }

    public static function isUserTeamAdmin(Team $team): bool
    {

        $team_admin = $team->getTeamAdmin();
        if (is_null($team_admin) || (UserSession::getUserId() < 1)) {
            return false;
        }

        return $team_admin->getUser()->getUserId() == UserSession::getUserId();

    }

}
