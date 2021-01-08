USE `webspell_ng`;

CREATE TABLE `ws_p40_cups` (
  `cupID` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `cup_icon` varchar(200) COLLATE latin1_german1_ci DEFAULT NULL,
  `cup_banner` varchar(200) COLLATE latin1_german1_ci DEFAULT NULL,
  `registration` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT 'open',
  `priority` varchar(50) COLLATE latin1_german1_ci NOT NULL DEFAULT 'normal',
  `elimination` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT 'single',
  `checkin_date` int(11) NOT NULL,
  `start_date` int(11) NOT NULL,
  `gameID` int(11) NOT NULL DEFAULT 0,
  `server` int(11) NOT NULL DEFAULT 0,
  `mapvote_enable` int(11) NOT NULL DEFAULT 0,
  `mappool` int(11) NOT NULL DEFAULT 0,
  `mode` varchar(255) COLLATE latin1_german1_ci NOT NULL DEFAULT '5on5',
  `ruleID` int(11) NOT NULL,
  `max_size` int(11) NOT NULL,
  `max_penalty` int(11) NOT NULL DEFAULT 12,
  `groupstage` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `hits` int(11) NOT NULL DEFAULT 0,
  `hits_teams` int(11) NOT NULL DEFAULT 0,
  `hits_groups` int(11) NOT NULL DEFAULT 0,
  `hits_bracket` int(11) NOT NULL DEFAULT 0,
  `hits_rules` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE latin1_german1_ci DEFAULT NULL,
  `saved` int(11) NOT NULL DEFAULT 0,
  `admin_visible` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


CREATE TABLE `ws_p40_cups_admin` (
  `adminID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `rights` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Cup awards
--

CREATE TABLE `ws_p40_cups_awards` (
  `awardID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  `teamID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `cupID` int(11) DEFAULT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_awards` ADD PRIMARY KEY (`awardID`), ADD UNIQUE KEY `awardID` (`awardID`), ADD UNIQUE KEY `teamID` (`teamID`,`categoryID`);
ALTER TABLE `ws_p40_cups_awards` MODIFY `awardID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Cup award categories
--

CREATE TABLE `ws_p40_cups_awards_category` (
  `categoryID` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `icon` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `active_column` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `cup_ranking` int(11) DEFAULT NULL,
  `count_of_cups` int(11) DEFAULT NULL,
  `count_of_matches` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 1,
  `description` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_awards_category` ADD PRIMARY KEY (`categoryID`);
ALTER TABLE `ws_p40_cups_awards_category` MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE `ws_p40_cups_gameaccounts` (
  `gameaccID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `category` varchar(3) COLLATE latin1_german1_ci NOT NULL,
  `value` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `smurf` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `deleted_date` int(11) NOT NULL DEFAULT 0,
  `deleted_seen` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_gameaccounts_banned` (
  `id` int(11) NOT NULL,
  `game` varchar(3) COLLATE latin1_german1_ci NOT NULL,
  `game_id` int(11) NOT NULL,
  `value` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `unique_id` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `description` text COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_gameaccounts_csgo` (
  `gameaccID` int(11) NOT NULL,
  `validated` int(11) NOT NULL DEFAULT 0,
  `date` int(11) DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `vac_bann` int(11) NOT NULL DEFAULT 0,
  `bann_date` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_gameaccounts_lol` (
  `gameaccID` int(11) NOT NULL,
  `unique_id` int(11) NOT NULL,
  `region` varchar(20) COLLATE latin1_german1_ci NOT NULL DEFAULT 'euw',
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL,
  `division` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `rank` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_gameaccounts_mc` (
  `gameaccID` int(11) NOT NULL,
  `unique_id` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_gameaccounts_profiles` (
  `profileID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `url` varchar(100) COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_mappool` (
  `mappoolID` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `game` varchar(3) COLLATE latin1_german1_ci NOT NULL,
  `gameID` int(11) NOT NULL DEFAULT 0,
  `maps` text COLLATE latin1_german1_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_matches_playoff` (
  `matchID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `wb` int(11) NOT NULL DEFAULT 0,
  `runde` int(11) NOT NULL,
  `spiel` int(11) NOT NULL,
  `format` varchar(3) COLLATE latin1_german1_ci NOT NULL DEFAULT 'bo1',
  `date` int(11) NOT NULL DEFAULT 0,
  `mapvote` int(11) NOT NULL DEFAULT 0,
  `team1` int(11) NOT NULL,
  `team1_freilos` int(11) NOT NULL DEFAULT 0,
  `ergebnis1` int(11) NOT NULL DEFAULT 0,
  `team2` int(11) NOT NULL,
  `team2_freilos` int(11) NOT NULL DEFAULT 0,
  `ergebnis2` int(11) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0,
  `comments` int(11) NOT NULL DEFAULT 1,
  `team1_confirmed` int(11) NOT NULL DEFAULT 0,
  `team2_confirmed` int(11) NOT NULL DEFAULT 0,
  `admin_confirmed` int(11) NOT NULL DEFAULT 0,
  `maps` text COLLATE latin1_german1_ci NOT NULL,
  `server` text COLLATE latin1_german1_ci NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `bot` int(11) NOT NULL DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_matches_playoff_screens` (
  `screenshotID` int(11) NOT NULL,
  `matchID` int(11) NOT NULL,
  `file` varchar(100) COLLATE latin1_german1_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_matches_playoff_screens_category` (
  `categoryID` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE latin1_german1_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_penalty` (
  `ppID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL DEFAULT 0,
  `date` int(11) NOT NULL,
  `duration_time` int(11) NOT NULL DEFAULT 0,
  `teamID` int(11) NOT NULL DEFAULT 0,
  `userID` int(11) NOT NULL DEFAULT 0,
  `reasonID` int(11) NOT NULL DEFAULT 0,
  `comment` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_penalty_category` (
  `reasonID` int(11) NOT NULL,
  `name_de` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `name_uk` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `lifetime` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Cups placements
--

CREATE TABLE `ws_p40_cups_placements` (
  `pID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `ranking` varchar(20) COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_placements` ADD PRIMARY KEY (`pID`),  ADD UNIQUE KEY `unique_placements` (`cupID`,`teamID`);
ALTER TABLE `ws_p40_cups_placements` MODIFY `pID` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `ws_p40_cups_policy` (
  `id` int(11) NOT NULL,
  `content` text COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_prizes` (
  `preisID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `preis` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `platzierung` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_rules` (
  `ruleID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_screenshots` (
  `screenID` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `image` int(11) NOT NULL,
  `is_file` int(11) NOT NULL DEFAULT 1,
  `deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_sponsors` (
  `id` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `sponsorID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_streams` (
  `streamID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `livID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Support Tickets
--

CREATE TABLE `ws_p40_cups_supporttickets` (
  `ticketID` int(11) NOT NULL,
  `start_date` int(11) NOT NULL,
  `take_date` int(11) DEFAULT NULL,
  `closed_date` int(11) DEFAULT NULL,
  `closed_by_id` int(11) DEFAULT NULL,
  `userID` int(11) NOT NULL,
  `opponent_adminID` int(11) DEFAULT NULL,
  `adminID` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `screenshot` varchar(255) COLLATE latin1_german1_ci DEFAULT NULL,
  `categoryID` int(11) DEFAULT NULL,
  `cupID` int(11) DEFAULT NULL,
  `teamID` int(11) DEFAULT NULL,
  `opponentID` int(11) DEFAULT NULL,
  `matchID` int(11) DEFAULT NULL,
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_supporttickets` ADD PRIMARY KEY (`ticketID`), ADD UNIQUE KEY `ticketID` (`ticketID`);
ALTER TABLE `ws_p40_cups_supporttickets` MODIFY `ticketID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Support Tickets Category
--

CREATE TABLE `ws_p40_cups_supporttickets_category` (
  `categoryID` int(11) NOT NULL,
  `name_de` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `name_uk` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `template` varchar(255) COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_supporttickets_category` ADD PRIMARY KEY (`categoryID`), ADD UNIQUE KEY `categoryID` (`categoryID`);
ALTER TABLE `ws_p40_cups_supporttickets_category` MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `ws_p40_cups_supporttickets_category`
(`name_de`, `name_uk`, `template`)
VALUES
('Normale Support Anfrage', 'Normal support ticket', 'default');

--
-- Support Tickets content
--

CREATE TABLE `ws_p40_cups_supporttickets_content` (
  `contentID` int(11) NOT NULL,
  `ticketID` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `text` text COLLATE latin1_german1_ci NOT NULL,
  `new` int(1) NOT NULL DEFAULT 1,
  `new_admin` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_supporttickets_content` ADD PRIMARY KEY (`contentID`), ADD UNIQUE KEY `contentID` (`contentID`);
ALTER TABLE `ws_p40_cups_supporttickets_content` MODIFY `contentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Support Tickets history
--

CREATE TABLE `ws_p40_cups_supporttickets_status` (
  `ticketID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_supporttickets_status` ADD UNIQUE KEY `ticketID` (`ticketID`,`userID`);

CREATE TABLE `ws_p40_cups_team` (
  `userID` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `position` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `description` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_teams` (
  `teamID` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `name` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `tag` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `userID` int(11) NOT NULL,
  `country` varchar(4) COLLATE latin1_german1_ci NOT NULL DEFAULT 'de',
  `hp` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `logotype` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_german1_ci NOT NULL,
  `hits` int(11) NOT NULL DEFAULT 0,
  `deleted` int(11) NOT NULL DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_teams_comments` (
  `teamID` int(11) NOT NULL,
  `date` int(11) NOT NULL DEFAULT 0,
  `userID` int(11) NOT NULL DEFAULT 0,
  `comment` text COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_teams_log` (
  `teamID` int(11) NOT NULL,
  `teamName` varchar(100) COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `kicked_id` int(11) DEFAULT NULL,
  `action` varchar(255) COLLATE latin1_german1_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

CREATE TABLE `ws_p40_cups_teams_member` (
  `memberID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `join_date` int(11) NOT NULL,
  `left_date` int(11) DEFAULT NULL,
  `kickID` int(11) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

--
-- Cup Team Member Position
--

CREATE TABLE `ws_p40_cups_teams_position` (
  `positionID` int(11) NOT NULL,
  `name` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `counter` int(11) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_teams_position` ADD PRIMARY KEY (`positionID`), ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `ws_p40_cups_teams_position` MODIFY `positionID` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `ws_p40_cups_teams_position` (`name`, `sort`) VALUES
("Admin", 1),
("Captain", 2),
("Coach", 3),
("Player", 4);

--
-- Cup Team Member Position
--

CREATE TABLE `ws_p40_cups_participants` (
  `ID` int(11) NOT NULL,
  `cupID` int(11) NOT NULL,
  `teamID` int(11) NOT NULL,
  `checked_in` int(1) NOT NULL DEFAULT 0,
  `date_register` int(11) NOT NULL DEFAULT 0,
  `date_checkin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `ws_p40_cups_participants` ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `unique_participants` (`cupID`,`teamID`);
ALTER TABLE `ws_p40_cups_participants` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Other tables
--

CREATE TABLE `ws_p40_cups_teams_social` (
  `teamID` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `value` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


ALTER TABLE `ws_p40_cups`
  ADD PRIMARY KEY (`cupID`);

ALTER TABLE `ws_p40_cups_admin`
  ADD PRIMARY KEY (`adminID`),
  ADD UNIQUE KEY `adminID` (`adminID`);

ALTER TABLE `ws_p40_cups_gameaccounts`
  ADD PRIMARY KEY (`gameaccID`),
  ADD UNIQUE KEY `gameaccID` (`gameaccID`);

ALTER TABLE `ws_p40_cups_gameaccounts_banned`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD UNIQUE KEY `value` (`value`);

ALTER TABLE `ws_p40_cups_gameaccounts_csgo`
  ADD UNIQUE KEY `csgo_gameaccount_id_unique` (`gameaccID`);

ALTER TABLE `ws_p40_cups_gameaccounts_lol`
  ADD PRIMARY KEY (`gameaccID`);

ALTER TABLE `ws_p40_cups_gameaccounts_mc`
  ADD PRIMARY KEY (`gameaccID`);

ALTER TABLE `ws_p40_cups_gameaccounts_profiles`
  ADD PRIMARY KEY (`profileID`);

ALTER TABLE `ws_p40_cups_mappool`
  ADD UNIQUE KEY `mappoolID` (`mappoolID`),
  ADD UNIQUE KEY `unique_name` (`name`,`gameID`);

ALTER TABLE `ws_p40_cups_matches_playoff`
  ADD PRIMARY KEY (`matchID`),
  ADD UNIQUE KEY `matchID` (`matchID`);

ALTER TABLE `ws_p40_cups_matches_playoff_screens`
  ADD PRIMARY KEY (`screenshotID`);

ALTER TABLE `ws_p40_cups_matches_playoff_screens_category`
  ADD PRIMARY KEY (`categoryID`);

ALTER TABLE `ws_p40_cups_penalty`
  ADD PRIMARY KEY (`ppID`),
  ADD UNIQUE KEY `ppID` (`ppID`);

ALTER TABLE `ws_p40_cups_penalty_category`
  ADD PRIMARY KEY (`reasonID`),
  ADD UNIQUE KEY `reasonID` (`reasonID`);

ALTER TABLE `ws_p40_cups_policy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `ws_p40_cups_prizes`
  ADD PRIMARY KEY (`preisID`),
  ADD UNIQUE KEY `preisID` (`preisID`);

ALTER TABLE `ws_p40_cups_rules`
  ADD PRIMARY KEY (`ruleID`),
  ADD UNIQUE KEY `ruleID` (`ruleID`);

ALTER TABLE `ws_p40_cups_screenshots`
  ADD PRIMARY KEY (`screenID`);

ALTER TABLE `ws_p40_cups_sponsors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `ws_p40_cups_streams`
  ADD UNIQUE KEY `streamID` (`streamID`);

ALTER TABLE `ws_p40_cups_team`
  ADD PRIMARY KEY (`userID`);

ALTER TABLE `ws_p40_cups_teams`
  ADD PRIMARY KEY (`teamID`);

ALTER TABLE `ws_p40_cups_teams_comments`
  ADD PRIMARY KEY (`teamID`,`date`);

ALTER TABLE `ws_p40_cups_teams_member`
  ADD PRIMARY KEY (`memberID`),
  ADD UNIQUE KEY `memberID` (`memberID`);

ALTER TABLE `ws_p40_cups_teams_social`
  ADD PRIMARY KEY (`teamID`,`category_id`);


ALTER TABLE `ws_p40_cups`
  MODIFY `cupID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_gameaccounts`
  MODIFY `gameaccID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_gameaccounts_banned`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_gameaccounts_profiles`
  MODIFY `profileID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_mappool`
  MODIFY `mappoolID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_matches_playoff`
  MODIFY `matchID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_matches_playoff_screens`
  MODIFY `screenshotID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_matches_playoff_screens_category`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_penalty`
  MODIFY `ppID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_penalty_category`
  MODIFY `reasonID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_policy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_prizes`
  MODIFY `preisID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_rules`
  MODIFY `ruleID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_screenshots`
  MODIFY `screenID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_sponsors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_streams`
  MODIFY `streamID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_teams`
  MODIFY `teamID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ws_p40_cups_teams_member`
  MODIFY `memberID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Foreign Keys
--
ALTER TABLE `ws_p40_cups_awards` ADD FOREIGN KEY (`categoryID`) REFERENCES `ws_p40_cups_awards_category`(`categoryID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `ws_p40_cups_gameaccounts_csgo` ADD CONSTRAINT `FK_CSGO_GammeaccountID` FOREIGN KEY (`gameaccID`) REFERENCES `ws_p40_cups_gameaccounts` (`gameaccID`) ON DELETE CASCADE;

COMMIT;