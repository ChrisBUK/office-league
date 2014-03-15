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
/*Data for the table `competition_fixture` */

/*Data for the table `competition_team` */

insert  into `competition_team`(`ctm_id`,`ctm_season_instance`,`ctm_competition`,`ctm_team`,`ctm_played`,`ctm_won`,`ctm_drawn`,`ctm_lost`,`ctm_points`,`ctm_score_for`,`ctm_score_against`,`ctm_score_diff`,`ctm_current_pos`,`ctm_previous_pos`,`ctm_promoted`,`ctm_relegated`,`ctm_winners`,`ctm_runners_up`,`ctm_knocked_out_round`) values (1,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(2,1,1,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(3,1,1,3,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(4,1,1,4,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(5,1,1,5,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(6,1,2,6,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(7,1,2,7,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(8,1,2,8,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(9,1,2,9,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(10,1,3,10,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(11,1,3,11,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(12,1,3,12,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(13,1,3,13,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(14,1,4,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(15,1,4,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(16,1,4,3,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(17,1,4,4,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(18,1,4,5,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(19,1,4,6,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(20,1,4,7,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(21,1,4,8,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(22,1,4,9,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(23,1,4,10,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(24,1,4,11,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(25,1,4,12,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL),(26,1,4,13,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL);

/*Data for the table `competition_type` */

insert  into `competition_type`(`comp_type_id`,`comp_name`,`comp_rank`,`comp_owner`,`comp_format`,`comp_total_places`,`comp_promo_places`,`comp_releg_places`,`comp_promo_into`,`comp_releg_into`,`comp_points_win`,`comp_points_draw`,`comp_points_lose`,`comp_rounds`,`comp_rules`) values (1,'SSDM Premier',1,1,'LEAGUE',5,0,1,NULL,2,3,1,0,5,'5 Min/Half, Game speed Normal, Results after 90 mins'),(2,'SSDM Championship',2,1,'LEAGUE',4,1,1,1,3,3,1,0,4,'5 Min/Half, Game speed Normal, Results after 90 mins'),(3,'SSDM Conference',3,1,'LEAGUE',4,1,0,2,NULL,3,1,0,4,'5 Min/Half, Game speed Normal, Results after 90 mins'),(4,'SSDM Cup',4,1,'KO',13,0,0,NULL,NULL,0,0,0,5,'5 Min/Half, Game speed Normal, Extra time if a draw after 90 min, Penalties if a draw AET.');

/*Data for the table `season_instance` */

insert  into `season_instance`(`sin_id`,`sin_season_type`,`sin_began`,`sin_ended`) values (1,1,'2014-03-15 00:00:00',NULL);

/*Data for the table `season_type` */

insert  into `season_type`(`seas_type_id`,`seas_owner`,`seas_name`,`seas_current_id`) values (1,1,'SSDM Development',2);

/*Data for the table `season_type_competition` */

insert  into `season_type_competition`(`stc_id`,`stc_season_type`,`stc_competition`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4);

/*Data for the table `team` */

insert  into `team`(`team_id`,`team_owner`,`team_name`,`team_created`,`team_updated`) values (1,1,'Dave Keywood','2014-07-03 18:30:00','2014-07-03 18:30:00'),(2,1,'Mark Kitching','2014-07-03 18:30:00','2014-07-03 18:30:00'),(3,1,'Paul Grave','2014-07-03 18:30:00','2014-07-03 18:30:00'),(4,1,'Dave Gordon','2014-07-03 18:30:00','2014-07-03 18:30:00'),(5,1,'Nick Philpott','2014-07-03 18:30:00','2014-07-03 18:30:00'),(6,1,'Gary Ward','2014-07-03 18:30:00','2014-07-03 18:30:00'),(7,1,'Michael Cooper','2014-07-03 18:30:00','2014-07-03 18:30:00'),(8,1,'Chris Booker','2014-07-03 18:30:00','2014-07-03 18:30:00'),(9,1,'Chris Bell','2014-07-03 18:30:00','2014-07-03 18:30:00'),(10,1,'Amy Bamber','2014-07-03 18:30:00','2014-07-03 18:30:00'),(11,1,'Guy Taylor','2014-07-03 18:30:00','2014-07-03 18:30:00'),(12,1,'Ryan Winfield','2014-07-03 18:30:00','2014-07-03 18:30:00'),(13,1,'Jamie Hobbs','2014-07-03 18:30:00','2014-07-03 18:30:00');

/*Data for the table `user_account` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
