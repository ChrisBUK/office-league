/*
SQLyog Enterprise v11.24 (64 bit)
MySQL - 5.0.67-community-nt : Database - office_league
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `competition_fixture` */

DROP TABLE IF EXISTS `competition_fixture`;

CREATE TABLE `competition_fixture` (
  `fix_id` int(6) NOT NULL auto_increment,
  `fix_season` int(6) NOT NULL,
  `fix_competition` int(6) NOT NULL,
  `fix_round` int(6) NOT NULL,
  `fix_home_team` int(6) NOT NULL,
  `fix_away_team` int(6) NOT NULL,
  `fix_played` tinyint(1) NOT NULL default '0',
  `fix_home_score` int(2) NOT NULL default '0',
  `fix_away_score` int(2) NOT NULL default '0',
  PRIMARY KEY  (`fix_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `competition_team` */

DROP TABLE IF EXISTS `competition_team`;

CREATE TABLE `competition_team` (
  `ctm_id` int(6) NOT NULL auto_increment,
  `ctm_season_instance` int(6) NOT NULL,
  `ctm_competition` int(6) NOT NULL,
  `ctm_team` int(6) NOT NULL,
  `ctm_played` int(2) NOT NULL default '0',
  `ctm_won` int(2) NOT NULL default '0',
  `ctm_drawn` int(2) NOT NULL default '0',
  `ctm_lost` int(2) NOT NULL default '0',
  `ctm_points` int(3) NOT NULL default '0',
  `ctm_score_for` int(3) NOT NULL default '0',
  `ctm_score_against` int(3) NOT NULL default '0',
  `ctm_score_diff` int(3) NOT NULL default '0',
  `ctm_current_pos` int(2) NOT NULL default '0',
  `ctm_previous_pos` int(2) NOT NULL default '0',
  `ctm_promoted` tinyint(1) NOT NULL default '0',
  `ctm_relegated` tinyint(1) NOT NULL default '0',
  `ctm_winners` tinyint(1) NOT NULL default '0',
  `ctm_runners_up` tinyint(1) NOT NULL default '0',
  `ctm_knocked_out_round` int(2) default NULL,
  PRIMARY KEY  (`ctm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*Table structure for table `competition_type` */

DROP TABLE IF EXISTS `competition_type`;

CREATE TABLE `competition_type` (
  `comp_type_id` int(6) NOT NULL auto_increment,
  `comp_name` char(64) NOT NULL,
  `comp_rank` int(3) NOT NULL default '1',
  `comp_owner` int(6) NOT NULL default '1',
  `comp_format` char(6) NOT NULL default 'LEAGUE',
  `comp_total_places` int(3) NOT NULL default '0',
  `comp_promo_places` int(2) NOT NULL default '0',
  `comp_releg_places` int(2) NOT NULL default '0',
  `comp_promo_into` int(6) default NULL,
  `comp_releg_into` int(6) default NULL,
  `comp_points_win` int(1) NOT NULL default '3',
  `comp_points_draw` int(1) NOT NULL default '1',
  `comp_points_lose` int(1) NOT NULL default '0',
  `comp_rounds` int(2) default '0',
  `comp_rules` char(255) default NULL,
  PRIMARY KEY  (`comp_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `season_instance` */

DROP TABLE IF EXISTS `season_instance`;

CREATE TABLE `season_instance` (
  `sin_id` int(6) NOT NULL auto_increment,
  `sin_season_type` int(5) NOT NULL,
  `sin_began` datetime default NULL,
  `sin_ended` datetime default NULL,
  PRIMARY KEY  (`sin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `season_type` */

DROP TABLE IF EXISTS `season_type`;

CREATE TABLE `season_type` (
  `seas_type_id` int(6) NOT NULL auto_increment,
  `seas_owner` int(6) NOT NULL,
  `seas_name` char(64) NOT NULL,
  `seas_current_id` int(6) NOT NULL default '0',
  PRIMARY KEY  (`seas_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `season_type_competition` */

DROP TABLE IF EXISTS `season_type_competition`;

CREATE TABLE `season_type_competition` (
  `stc_id` int(6) NOT NULL auto_increment,
  `stc_season_type` int(6) NOT NULL,
  `stc_competition` int(6) NOT NULL,
  PRIMARY KEY  (`stc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `team` */

DROP TABLE IF EXISTS `team`;

CREATE TABLE `team` (
  `team_id` int(6) NOT NULL auto_increment,
  `team_owner` int(6) NOT NULL,
  `team_name` char(64) NOT NULL,
  `team_created` datetime NOT NULL,
  `team_updated` datetime default NULL,
  PRIMARY KEY  (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Table structure for table `user_account` */

DROP TABLE IF EXISTS `user_account`;

CREATE TABLE `user_account` (
  `uac_id` int(6) NOT NULL auto_increment,
  `uac_email` char(128) NOT NULL,
  `uac_passwd` char(32) NOT NULL,
  `uac_created_date` datetime NOT NULL,
  `uac_created_ip` char(32) NOT NULL,
  `uac_lastvisit_date` datetime NOT NULL,
  `uac_lastvisit_ip` char(32) NOT NULL,
  PRIMARY KEY  (`uac_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
