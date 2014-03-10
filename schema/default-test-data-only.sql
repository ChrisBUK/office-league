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
/*Data for the table `competition` */

insert  into `competition`(`comp_id`,`comp_name`,`comp_owner`) values (1,'SSDM Premier',1),(2,'SSDM Championship',1),(3,'SSDM League One',1),(4,'SSDM League Two',1),(5,'SSDM Cup',1);

/*Data for the table `competition_fixture` */

insert  into `competition_fixture`(`fix_id`,`fix_season`,`fix_competition`,`fix_round`,`fix_home_team`,`fix_away_team`,`fix_played`,`fix_home_score`,`fix_away_score`) values (1,1,1,1,1,2,0,0,0),(2,1,1,1,3,4,0,0,0),(3,1,1,2,1,3,0,0,0),(4,1,1,2,2,4,0,0,0),(5,1,1,3,1,4,0,0,0),(6,1,1,3,2,3,0,0,0),(7,1,2,1,5,6,0,0,0),(8,1,2,1,7,8,0,0,0),(9,1,2,2,5,7,0,0,0),(10,1,2,2,6,8,0,0,0),(11,1,2,3,5,8,0,0,0),(12,1,2,3,6,7,0,0,0),(13,1,3,1,9,10,0,0,0),(14,1,3,1,11,12,0,0,0),(15,1,3,2,9,11,0,0,0),(16,1,3,2,10,12,0,0,0),(17,1,3,3,9,12,0,0,0),(18,1,3,3,10,11,0,0,0),(19,1,4,1,13,14,0,0,0),(20,1,4,1,15,16,0,0,0),(21,1,4,2,13,15,0,0,0),(22,1,4,2,14,16,0,0,0),(23,1,4,3,13,16,0,0,0),(24,1,4,3,14,15,0,0,0);

/*Data for the table `competition_season` */

insert  into `competition_season`(`cs_comp`,`cs_season`) values (1,1),(2,1),(3,1),(4,1),(5,1);

/*Data for the table `competition_table` */

insert  into `competition_table`(`ctb_id`,`ctb_competition`,`ctb_season`,`ctb_team`,`ctb_played`,`ctb_won`,`ctb_drawn`,`ctb_lost`,`ctb_points`,`ctb_score_for`,`ctb_score_against`,`ctb_score_diff`) values (1,1,1,1,0,0,0,0,0,0,0,0),(2,1,1,2,0,0,0,0,0,0,0,0),(3,1,1,3,0,0,0,0,0,0,0,0),(4,1,1,4,0,0,0,0,0,0,0,0),(5,2,1,5,0,0,0,0,0,0,0,0),(6,2,1,6,0,0,0,0,0,0,0,0),(7,2,1,7,0,0,0,0,0,0,0,0),(8,2,1,8,0,0,0,0,0,0,0,0),(9,3,1,9,0,0,0,0,0,0,0,0),(10,3,1,10,0,0,0,0,0,0,0,0),(11,3,1,11,0,0,0,0,0,0,0,0),(12,3,1,12,0,0,0,0,0,0,0,0),(13,4,1,13,0,0,0,0,0,0,0,0),(14,4,1,14,0,0,0,0,0,0,0,0),(15,4,1,15,0,0,0,0,0,0,0,0),(16,4,1,16,0,0,0,0,0,0,0,0);

/*Data for the table `competition_team` */

insert  into `competition_team`(`ctm_id`,`ctm_comp`,`ctm_team`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,2,5),(6,2,6),(7,2,7),(8,2,8),(9,3,9),(10,3,10),(11,3,11),(12,3,12),(13,4,13),(14,4,14),(15,4,15),(16,4,16),(17,5,1),(18,5,2),(19,5,3),(20,5,4),(21,5,5),(22,5,6),(23,5,7),(24,5,8),(25,5,9),(26,5,10),(27,5,11),(28,5,12),(29,5,13),(30,5,14),(31,5,15),(32,5,16);

/*Data for the table `season` */

insert  into `season`(`seas_id`,`seas_owner`,`seas_name`) values (1,1,'March 2014');

/*Data for the table `team` */

insert  into `team`(`team_id`,`team_owner`,`team_name`,`team_created`,`team_updated`) values (1,1,'Prem Team 1','2014-07-03 18:30:00','2014-07-03 18:30:00'),(2,1,'Prem Team 2','2014-07-03 18:30:00','2014-07-03 18:30:00'),(3,1,'Prem Team 3','2014-07-03 18:30:00','2014-07-03 18:30:00'),(4,1,'Prem Team 4','2014-07-03 18:30:00','2014-07-03 18:30:00'),(5,1,'Chmp Team 1','2014-07-03 18:30:00','2014-07-03 18:30:00'),(6,1,'Chmp Team 2','2014-07-03 18:30:00','2014-07-03 18:30:00'),(7,1,'Chmp Team 3','2014-07-03 18:30:00','2014-07-03 18:30:00'),(8,1,'Chmp Team 4','2014-07-03 18:30:00','2014-07-03 18:30:00'),(9,1,'L1 Team 1','2014-07-03 18:30:00','2014-07-03 18:30:00'),(10,1,'L1 Team 2','2014-07-03 18:30:00','2014-07-03 18:30:00'),(11,1,'L1 Team 3','2014-07-03 18:30:00','2014-07-03 18:30:00'),(12,1,'L1 Team 4','2014-07-03 18:30:00','2014-07-03 18:30:00'),(13,1,'L2 Team 1','2014-07-03 18:30:00','2014-07-03 18:30:00'),(14,1,'L2 Team 2','2014-07-03 18:30:00','2014-07-03 18:30:00'),(15,1,'L2 Team 3','2014-07-03 18:30:00','2014-07-03 18:30:00'),(16,1,'L2 Team 4','2014-07-03 18:30:00','2014-07-03 18:30:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
