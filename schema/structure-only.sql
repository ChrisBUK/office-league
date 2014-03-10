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
/*Table structure for table `competition` */

DROP TABLE IF EXISTS `competition`;

CREATE TABLE `competition` (
  `comp_id` int(6) NOT NULL auto_increment,
  `comp_name` char(64) NOT NULL,
  `comp_owner` int(6) NOT NULL,
  PRIMARY KEY  (`comp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

/*Table structure for table `competition_season` */

DROP TABLE IF EXISTS `competition_season`;

CREATE TABLE `competition_season` (
  `cs_comp` int(6) NOT NULL,
  `cs_season` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `competition_table` */

DROP TABLE IF EXISTS `competition_table`;

CREATE TABLE `competition_table` (
  `ctb_id` int(6) NOT NULL auto_increment,
  `ctb_competition` int(6) NOT NULL,
  `ctb_season` int(6) NOT NULL,
  `ctb_team` int(6) NOT NULL,
  `ctb_played` int(6) NOT NULL default '0',
  `ctb_won` int(2) NOT NULL default '0',
  `ctb_drawn` int(2) NOT NULL default '0',
  `ctb_lost` int(2) NOT NULL default '0',
  `ctb_points` int(3) NOT NULL default '0',
  `ctb_score_for` int(3) NOT NULL default '0',
  `ctb_score_against` int(3) NOT NULL default '0',
  `ctb_score_diff` int(3) NOT NULL default '0',
  PRIMARY KEY  (`ctb_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Table structure for table `competition_team` */

DROP TABLE IF EXISTS `competition_team`;

CREATE TABLE `competition_team` (
  `ctm_id` int(6) NOT NULL auto_increment,
  `ctm_comp` int(6) NOT NULL,
  `ctm_team` int(6) NOT NULL,
  PRIMARY KEY  (`ctm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

/*Table structure for table `season` */

DROP TABLE IF EXISTS `season`;

CREATE TABLE `season` (
  `seas_id` int(6) NOT NULL auto_increment,
  `seas_owner` int(6) NOT NULL,
  `seas_name` char(64) NOT NULL,
  PRIMARY KEY  (`seas_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `team` */

DROP TABLE IF EXISTS `team`;

CREATE TABLE `team` (
  `team_id` int(6) NOT NULL auto_increment,
  `team_owner` int(6) NOT NULL,
  `team_name` char(64) NOT NULL,
  `team_created` datetime NOT NULL,
  `team_updated` datetime default NULL,
  PRIMARY KEY  (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
