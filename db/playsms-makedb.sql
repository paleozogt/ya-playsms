-- MySQL dump 9.09
--
-- Host: localhost    Database: playsms
-- -------------------------------------------------------
-- Server version	4.0.15-log

-- create and use the database
-- 
CREATE DATABASE IF NOT EXISTS `playsms`;
USE `playsms`;

--
-- Table structure for table `playsms_featAutoreply`
--

DROP TABLE IF EXISTS playsms_featAutoreply;
CREATE TABLE playsms_featAutoreply (
  autoreply_id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  autoreply_code varchar(10) NOT NULL default '',
  PRIMARY KEY  (autoreply_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featAutoreply`
--

INSERT INTO playsms_featAutoreply VALUES (1,1,'HELP');

--
-- Table structure for table `playsms_featAutoreply_log`
--

DROP TABLE IF EXISTS playsms_featAutoreply_log;
CREATE TABLE playsms_featAutoreply_log (
  autoreply_log_id int(11) NOT NULL auto_increment,
  sms_sender varchar(20) NOT NULL default '',
  autoreply_log_datetime varchar(20) NOT NULL default '',
  autoreply_log_code varchar(10) NOT NULL default '',
  autoreply_log_request varchar(160) NOT NULL default '',
  PRIMARY KEY  (autoreply_log_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featAutoreply_log`
--


--
-- Table structure for table `playsms_featAutoreply_scenario`
--

DROP TABLE IF EXISTS playsms_featAutoreply_scenario;
CREATE TABLE playsms_featAutoreply_scenario (
  autoreply_scenario_id int(11) NOT NULL auto_increment,
  autoreply_id int(11) NOT NULL default '0',
  autoreply_scenario_param1 varchar(20) NOT NULL default '',
  autoreply_scenario_param2 varchar(20) NOT NULL default '',
  autoreply_scenario_param3 varchar(20) NOT NULL default '',
  autoreply_scenario_param4 varchar(20) NOT NULL default '',
  autoreply_scenario_param5 varchar(20) NOT NULL default '',
  autoreply_scenario_param6 varchar(20) NOT NULL default '',
  autoreply_scenario_param7 varchar(20) NOT NULL default '',
  autoreply_scenario_result text NOT NULL default '',
  PRIMARY KEY  (autoreply_scenario_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featAutoreply_scenario`
--

INSERT INTO playsms_featAutoreply_scenario VALUES (1,1,'INTERNET','DOWN','','','','','','Please contact sysadmin via phone: +62 21 8613027');
INSERT INTO playsms_featAutoreply_scenario VALUES (2,1,'WEBMAIL','PASSWORD','ERROR','','','','','Please use forgot password link, and follow given instructions');

--
-- Table structure for table `playsms_featAutoSend`
-- 

CREATE TABLE IF NOT EXISTS `playsms_featAutoSend` (
  `id` int(11) NOT NULL auto_increment,
  `frequency` enum('hourly','daily','weekly','monthly') NOT NULL default 'daily',
  `number` varchar(100) NOT NULL,
  `msg` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

--
-- Table structure for table `playsms_featBoard`
--

DROP TABLE IF EXISTS playsms_featBoard;
CREATE TABLE playsms_featBoard (
  board_id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  board_code varchar(100) NOT NULL default '',
  board_forward_email varchar(250) NOT NULL default '',
  board_pref_template text NOT NULL,
  PRIMARY KEY  (board_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featBoard`
--

INSERT INTO playsms_featBoard VALUES (1,1,'PHP','anton@ngoprek.org','<font color=black size=-1><b>##SENDER##</b></font><br><font color=black size=-2><i>##DATETIME##</i></font><br><font color=black size=-1>##MESSAGE##</font>');

--
-- Table structure for table `playsms_featCommand`
--

DROP TABLE IF EXISTS playsms_featCommand;
CREATE TABLE playsms_featCommand (
  command_id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  command_code varchar(10) NOT NULL default '',
  command_exec text NOT NULL,
  PRIMARY KEY  (command_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featCommand`
--

INSERT INTO playsms_featCommand VALUES (1,1,'UPTIME','/home/playsms/public_html/beta/bin/uptime.sh ##SMSSENDER##');

--
-- Table structure for table `playsms_featCommand_log`
--

DROP TABLE IF EXISTS playsms_featCommand_log;
CREATE TABLE playsms_featCommand_log (
  command_log_id int(11) NOT NULL auto_increment,
  sms_sender varchar(20) NOT NULL default '',
  command_log_datetime varchar(20) NOT NULL default '',
  command_log_code varchar(10) NOT NULL default '',
  command_log_exec text NOT NULL,
  PRIMARY KEY  (command_log_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featCommand_log`
--


--
-- Table structure for table `playsms_featCustom`
--

DROP TABLE IF EXISTS playsms_featCustom;
CREATE TABLE playsms_featCustom (
  custom_id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  custom_code varchar(10) NOT NULL default '',
  custom_url text NOT NULL,
  PRIMARY KEY  (custom_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featCustom`
--

INSERT INTO playsms_featCustom VALUES (1,1,'CURR','http://www.ngoprek.org/currency.php?toeuro=##CUSTOMPARAM##&sender=##SMSSENDER##');

--
-- Table structure for table `playsms_featCustom_log`
--

DROP TABLE IF EXISTS playsms_featCustom_log;
CREATE TABLE playsms_featCustom_log (
  custom_log_id int(11) NOT NULL auto_increment,
  sms_sender varchar(20) NOT NULL default '',
  custom_log_datetime varchar(20) NOT NULL default '',
  custom_log_code varchar(10) NOT NULL default '',
  custom_log_url text NOT NULL,
  PRIMARY KEY  (custom_log_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featCustom_log`
--


--
-- Table structure for table `playsms_featPoll`
--

DROP TABLE IF EXISTS playsms_featPoll;
CREATE TABLE playsms_featPoll (
  poll_id int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  poll_title varchar(250) NOT NULL default '',
  poll_code varchar(10) NOT NULL default '',
  poll_enable int(11) NOT NULL default '0',
  PRIMARY KEY  (poll_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featPoll`
--

INSERT INTO playsms_featPoll VALUES (1,1,'Which do you prefer ?','PREFER',1);

--
-- Table structure for table `playsms_featPoll_choice`
--

DROP TABLE IF EXISTS playsms_featPoll_choice;
CREATE TABLE playsms_featPoll_choice (
  choice_id int(11) NOT NULL auto_increment,
  poll_id int(11) NOT NULL default '0',
  choice_title varchar(250) NOT NULL default '',
  choice_code varchar(10) NOT NULL default '',
  PRIMARY KEY  (choice_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featPoll_choice`
--

INSERT INTO playsms_featPoll_choice VALUES (1,1,'Love Without Sex','LWS');
INSERT INTO playsms_featPoll_choice VALUES (2,1,'Sex Without Love','SWL');

--
-- Table structure for table `playsms_featPoll_result`
--

DROP TABLE IF EXISTS playsms_featPoll_result;
CREATE TABLE playsms_featPoll_result (
  result_id int(11) NOT NULL auto_increment,
  poll_id int(11) NOT NULL default '0',
  choice_id int(11) NOT NULL default '0',
  poll_sender varchar(20) NOT NULL default '',
  PRIMARY KEY  (result_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_featPoll_result`
--


--
-- Table structure for table `playsms_gwmodClickatell_apidata`
--

DROP TABLE IF EXISTS playsms_gwmodClickatell_apidata;
CREATE TABLE playsms_gwmodClickatell_apidata (
  apidata_id int(11) NOT NULL auto_increment,
  smslog_id int(11) NOT NULL default '0',
  apimsgid varchar(100) NOT NULL default '',
  PRIMARY KEY  (apidata_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodClickatell_apidata`
--


--
-- Table structure for table `playsms_gwmodClickatell_config`
--

DROP TABLE IF EXISTS playsms_gwmodClickatell_config;
CREATE TABLE playsms_gwmodClickatell_config (
  cfg_name varchar(20) default 'gnokii',
  cfg_api_id varchar(20) default NULL,
  cfg_username varchar(100) default NULL,
  cfg_password varchar(100) default NULL,
  cfg_sender varchar(20) default NULL,
  cfg_send_url varchar(250) default NULL,
  cfg_incoming_path varchar(250) default NULL,
  cfg_credit int(11) NOT NULL default '0'
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodClickatell_config`
--

INSERT INTO playsms_gwmodClickatell_config VALUES ('clickatell','123456','playsms','pwd','PlaySMS','http://api.clickatell.com/http','/usr/local',10);

--
-- Table structure for table `playsms_gwmodGnokii_config`
--

DROP TABLE IF EXISTS playsms_gwmodGnokii_config;
CREATE TABLE playsms_gwmodGnokii_config (
  cfg_name varchar(20) NOT NULL default '',
  cfg_path varchar(250) NOT NULL default ''
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodGnokii_config`
--

INSERT INTO playsms_gwmodGnokii_config VALUES ('gnokii','/usr/local');

--
-- Table structure for table `playsms_gwmodKannel_config`
--

DROP TABLE IF EXISTS playsms_gwmodKannel_config;
CREATE TABLE playsms_gwmodKannel_config (
  cfg_name varchar(20) default NULL,
  cfg_incoming_path varchar(250) default NULL,
  cfg_username varchar(100) default NULL,
  cfg_password varchar(100) default NULL,
  cfg_global_sender varchar(20) default NULL,
  cfg_bearerbox_host varchar(250) default NULL,
  cfg_sendsms_port varchar(10) default NULL,
  cfg_playsms_web varchar(250) default NULL
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodKannel_config`
--

INSERT INTO playsms_gwmodKannel_config VALUES ('kannel','/usr/local','playsms','playsms','123','127.0.0.1','13031','http://localhost/playsms');

--
-- Table structure for table `playsms_gwmodKannel_dlr`
--

DROP TABLE IF EXISTS playsms_gwmodKannel_dlr;
CREATE TABLE playsms_gwmodKannel_dlr (
  kannel_dlr_id int(11) NOT NULL auto_increment,
  smslog_id int(11) NOT NULL default '0',
  kannel_dlr_type tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (kannel_dlr_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodKannel_dlr`
--


--
-- Table structure for table `playsms_gwmodTemplate_config`
--

DROP TABLE IF EXISTS playsms_gwmodTemplate_config;
CREATE TABLE playsms_gwmodTemplate_config (
  cfg_name varchar(20) NOT NULL default '',
  cfg_path varchar(250) NOT NULL default ''
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodTemplate_config`
--

INSERT INTO playsms_gwmodTemplate_config VALUES ('template','/usr/local');

--
-- Table structure for table `playsms_gwmodUplink`
--

DROP TABLE IF EXISTS playsms_gwmodUplink;
CREATE TABLE playsms_gwmodUplink (
  up_id int(11) NOT NULL auto_increment,
  up_local_slid int(11) NOT NULL default '0',
  up_remote_slid int(11) NOT NULL default '0',
  up_status tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (up_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodUplink`
--

INSERT INTO playsms_gwmodUplink VALUES (1,3,259,1);

--
-- Table structure for table `playsms_gwmodUplink_config`
--

DROP TABLE IF EXISTS playsms_gwmodUplink_config;
CREATE TABLE playsms_gwmodUplink_config (
  cfg_name varchar(20) default NULL,
  cfg_master varchar(250) default NULL,
  cfg_username varchar(100) default NULL,
  cfg_password varchar(100) default NULL,
  cfg_global_sender varchar(20) default NULL,
  cfg_incoming_path varchar(250) default NULL
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_gwmodUplink_config`
--

INSERT INTO playsms_gwmodUplink_config VALUES ('uplink','http://cpanel.smsrakyat.net','playsms','pwd','','/usr/local');

--
-- Table structure for table `playsms_tblConfig_main`
--

DROP TABLE IF EXISTS playsms_tblConfig_main;
CREATE TABLE playsms_tblConfig_main (
  id int(11) NOT NULL auto_increment,
  cfg_web_title varchar(250) default NULL,
  cfg_web_url varchar(250) NOT NULL, 
  cfg_email_service varchar(250) default NULL,
  cfg_email_footer varchar(250) default NULL,
  cfg_gateway_module varchar(20) default NULL,
  cfg_gateway_number varchar(100) default NULL,
  cfg_system_from varchar(100) default NULL COMMENT 'comma-delimited',
  PRIMARY KEY (id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblConfig_main`
--

INSERT INTO playsms_tblConfig_main VALUES ('1', 'PlaySMS MPS (Mobile Portal System)', 'http://localhost/playsms', '','PlaySMS MPS (Mobile Portal System)','kannel','', '');

--
-- Table structure for table `playsms_tblSMSIncoming`
--

DROP TABLE IF EXISTS playsms_tblSMSIncoming;
CREATE TABLE playsms_tblSMSIncoming (
  in_id int(11) NOT NULL auto_increment,
  in_gateway varchar(100) NOT NULL default '',
  in_sender varchar(20) NOT NULL default '',
  in_masked varchar(20) NOT NULL default '',
  in_code varchar(20) NOT NULL default '',
  in_msg text NOT NULL default '',
  in_datetime varchar(20) NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (in_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblSMSIncoming`
--


--
-- Table structure for table `playsms_tblSMSOutgoing`
--

DROP TABLE IF EXISTS playsms_tblSMSOutgoing;
CREATE TABLE playsms_tblSMSOutgoing (
  smslog_id int(11) NOT NULL auto_increment,
  flag_deleted tinyint(4) NOT NULL default '0',
  uid int(11) NOT NULL default '0',
  p_gateway varchar(100) NOT NULL default '',
  p_src varchar(100) NOT NULL default '',
  p_dst varchar(100) NOT NULL default '',
  p_footer varchar(11) NOT NULL default '',
  p_msg text NOT NULL default '',
  p_datetime varchar(20) NOT NULL default '0000-00-00 00:00:00',
  p_update varchar(20) NOT NULL default '0000-00-00 00:00:00',
  p_status tinyint(4) NOT NULL default '0',
  p_gpid tinyint(4) NOT NULL default '0',
  p_credit tinyint(4) NOT NULL default '0',
  p_sms_type varchar(100) NOT NULL default '',
  unicode tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (smslog_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblSMSOutgoing`
--

--
-- Table structure for table `playsms_tblSMSTemplate`
--

DROP TABLE IF EXISTS playsms_tblSMSTemplate;
CREATE TABLE playsms_tblSMSTemplate (
  tid int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  t_title varchar(100) NOT NULL default '',
  t_text text NOT NULL default '',
  PRIMARY KEY  (tid)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblSMSTemplate`
--

--
-- Table structure for table `playsms_tblUser`
--

DROP TABLE IF EXISTS playsms_tblUser;
CREATE TABLE playsms_tblUser (
  uid int(11) NOT NULL auto_increment,
  status tinyint(4) NOT NULL default '0',
  ticket varchar(100) NOT NULL default '',
  username varchar(100) NOT NULL default '',
  password varchar(100) binary NOT NULL default '',
  name varchar(100) NOT NULL default '',
  mobile varchar(100) NOT NULL default '',
  email varchar(250) NOT NULL default '',
  sender varchar(30) NOT NULL default '',
  dailysms int(11) NOT NULL default '0',
  gender tinyint(4) NOT NULL default '0',
  age tinyint(4) NOT NULL default '0',
  address varchar(250) NOT NULL default '',
  city varchar(100) NOT NULL default '',
  state varchar(100) NOT NULL default '',
  country int(11) NOT NULL default '0',
  birthday varchar(10) NOT NULL default '0000-00-00',
  marital tinyint(4) NOT NULL default '0',
  education tinyint(4) NOT NULL default '0',
  zipcode varchar(10) NOT NULL default '',
  junktimestamp varchar(30) NOT NULL default '',
  PRIMARY KEY  (uid)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblUser`
--

INSERT INTO playsms_tblUser VALUES (1,2,'d0b3239a5da9504a1a38f7745790c3c4','admin','admin','Administrator','+628568809027','','',0,1,0,'','','',334,'',1,1,'0','');

--
-- Table structure for table `playsms_tblUserGroupPhonebook`
--

DROP TABLE IF EXISTS playsms_tblUserGroupPhonebook;
CREATE TABLE playsms_tblUserGroupPhonebook (
  gpid int(11) NOT NULL auto_increment,
  uid int(11) NOT NULL default '0',
  gp_name varchar(100) NOT NULL default '',
  gp_code varchar(10) NOT NULL default '',
  PRIMARY KEY  (gpid)
) TYPE=MyISAM PACK_KEYS=0;

--
-- Dumping data for table `playsms_tblUserGroupPhonebook`
--

INSERT INTO playsms_tblUserGroupPhonebook VALUES (1,1,'Friends','FR');

--
-- Table structure for table `playsms_tblUserGroupPhonebook_public`
--

DROP TABLE IF EXISTS playsms_tblUserGroupPhonebook_public;
CREATE TABLE playsms_tblUserGroupPhonebook_public (
  gpidpublic int(11) NOT NULL auto_increment,
  gpid int(11) NOT NULL default '0',
  uid varchar(100) NOT NULL default '',
  PRIMARY KEY  (gpidpublic)
) TYPE=MyISAM PACK_KEYS=0;

--
-- Dumping data for table `playsms_tblUserGroupPhonebook_public`
--


--
-- Table structure for table `playsms_tblUserInbox`
--

DROP TABLE IF EXISTS playsms_tblUserInbox;
CREATE TABLE playsms_tblUserInbox (
  in_id int(11) NOT NULL auto_increment,
  in_sender varchar(20) NOT NULL default '',
  in_uid int(11) NOT NULL default '0',
  in_msg text NOT NULL default '',
  in_datetime varchar(20) NOT NULL default '0000-00-00 00:00:00',
  in_hidden tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (in_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblUserInbox`
--


--
-- Table structure for table `playsms_tblUserPhonebook`
--

DROP TABLE IF EXISTS playsms_tblUserPhonebook;
CREATE TABLE playsms_tblUserPhonebook (
  pid int(11) NOT NULL auto_increment,
  gpid int(11) NOT NULL default '0',
  uid int(11) NOT NULL default '0',
  p_num varchar(100) NOT NULL default '',
  p_desc varchar(250) NOT NULL default '',
  p_email varchar(250) NOT NULL default '',
  PRIMARY KEY  (pid)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblUserPhonebook`
--

--
-- Table structure for table `playsms_tblUser_country`
--

DROP TABLE IF EXISTS playsms_tblUser_country;
CREATE TABLE playsms_tblUser_country (
  country_id int(11) NOT NULL auto_increment,
  country_name varchar(200) NOT NULL default '',
  PRIMARY KEY  (country_id)
) TYPE=MyISAM;

--
-- Dumping data for table `playsms_tblUser_country`
--

INSERT INTO playsms_tblUser_country VALUES (1,'Afghanistan');
INSERT INTO playsms_tblUser_country VALUES (2,'Albania');
INSERT INTO playsms_tblUser_country VALUES (3,'Algeria');
INSERT INTO playsms_tblUser_country VALUES (5,'Andorra');
INSERT INTO playsms_tblUser_country VALUES (10,'Argentina');
INSERT INTO playsms_tblUser_country VALUES (11,'Armenia');
INSERT INTO playsms_tblUser_country VALUES (14,'Australia');
INSERT INTO playsms_tblUser_country VALUES (16,'Austria');
INSERT INTO playsms_tblUser_country VALUES (18,'Azerbaijan');
INSERT INTO playsms_tblUser_country VALUES (19,'Bahamas');
INSERT INTO playsms_tblUser_country VALUES (20,'Bahrain');
INSERT INTO playsms_tblUser_country VALUES (21,'Bangladesh');
INSERT INTO playsms_tblUser_country VALUES (24,'Belarus');
INSERT INTO playsms_tblUser_country VALUES (25,'Belgium');
INSERT INTO playsms_tblUser_country VALUES (29,'Bermuda');
INSERT INTO playsms_tblUser_country VALUES (30,'Bhutan');
INSERT INTO playsms_tblUser_country VALUES (31,'Bolivia');
INSERT INTO playsms_tblUser_country VALUES (32,'Bosnia-Herzegovina');
INSERT INTO playsms_tblUser_country VALUES (33,'Botswana');
INSERT INTO playsms_tblUser_country VALUES (35,'Brazil');
INSERT INTO playsms_tblUser_country VALUES (38,'Brunei');
INSERT INTO playsms_tblUser_country VALUES (39,'Bulgaria');
INSERT INTO playsms_tblUser_country VALUES (41,'Burundi');
INSERT INTO playsms_tblUser_country VALUES (42,'Cambodia');
INSERT INTO playsms_tblUser_country VALUES (44,'Cameroon');
INSERT INTO playsms_tblUser_country VALUES (45,'Canada');
INSERT INTO playsms_tblUser_country VALUES (51,'Chile');
INSERT INTO playsms_tblUser_country VALUES (52,'China');
INSERT INTO playsms_tblUser_country VALUES (55,'Columbia');
INSERT INTO playsms_tblUser_country VALUES (58,'Congo');
INSERT INTO playsms_tblUser_country VALUES (60,'Costa Rica');
INSERT INTO playsms_tblUser_country VALUES (61,'Croatia');
INSERT INTO playsms_tblUser_country VALUES (62,'Cuba');
INSERT INTO playsms_tblUser_country VALUES (66,'Czech Republic');
INSERT INTO playsms_tblUser_country VALUES (67,'Denmark');
INSERT INTO playsms_tblUser_country VALUES (74,'East Timor');
INSERT INTO playsms_tblUser_country VALUES (76,'Ecuador');
INSERT INTO playsms_tblUser_country VALUES (77,'Egypt');
INSERT INTO playsms_tblUser_country VALUES (78,'El Salvador');
INSERT INTO playsms_tblUser_country VALUES (81,'Estonia');
INSERT INTO playsms_tblUser_country VALUES (84,'Fiji Islands');
INSERT INTO playsms_tblUser_country VALUES (85,'Finland');
INSERT INTO playsms_tblUser_country VALUES (86,'France');
INSERT INTO playsms_tblUser_country VALUES (93,'Gabon');
INSERT INTO playsms_tblUser_country VALUES (94,'Gambia');
INSERT INTO playsms_tblUser_country VALUES (96,'Germany');
INSERT INTO playsms_tblUser_country VALUES (98,'Ghana');
INSERT INTO playsms_tblUser_country VALUES (100,'Greece');
INSERT INTO playsms_tblUser_country VALUES (105,'Guam');
INSERT INTO playsms_tblUser_country VALUES (107,'Guatemala');
INSERT INTO playsms_tblUser_country VALUES (108,'UK');
INSERT INTO playsms_tblUser_country VALUES (111,'Guyana');
INSERT INTO playsms_tblUser_country VALUES (112,'Haiti');
INSERT INTO playsms_tblUser_country VALUES (113,'Honduras');
INSERT INTO playsms_tblUser_country VALUES (114,'HongKong');
INSERT INTO playsms_tblUser_country VALUES (118,'Hungary');
INSERT INTO playsms_tblUser_country VALUES (120,'Iceland');
INSERT INTO playsms_tblUser_country VALUES (121,'India');
INSERT INTO playsms_tblUser_country VALUES (132,'Indonesia');
INSERT INTO playsms_tblUser_country VALUES (139,'Iran');
INSERT INTO playsms_tblUser_country VALUES (140,'Iraq');
INSERT INTO playsms_tblUser_country VALUES (141,'Ireland');
INSERT INTO playsms_tblUser_country VALUES (143,'Israel');
INSERT INTO playsms_tblUser_country VALUES (144,'Italy');
INSERT INTO playsms_tblUser_country VALUES (146,'Ivory Coast');
INSERT INTO playsms_tblUser_country VALUES (147,'Jamaica');
INSERT INTO playsms_tblUser_country VALUES (148,'Japan');
INSERT INTO playsms_tblUser_country VALUES (150,'Jordan');
INSERT INTO playsms_tblUser_country VALUES (151,'Kazakhstan');
INSERT INTO playsms_tblUser_country VALUES (153,'Kenya');
INSERT INTO playsms_tblUser_country VALUES (155,'Korea (South)');
INSERT INTO playsms_tblUser_country VALUES (156,'Korea (North)');
INSERT INTO playsms_tblUser_country VALUES (157,'Kuwait');
INSERT INTO playsms_tblUser_country VALUES (158,'Kyrgyzstan');
INSERT INTO playsms_tblUser_country VALUES (160,'Latvia');
INSERT INTO playsms_tblUser_country VALUES (161,'Lebanon');
INSERT INTO playsms_tblUser_country VALUES (163,'Liberia');
INSERT INTO playsms_tblUser_country VALUES (164,'Libya');
INSERT INTO playsms_tblUser_country VALUES (166,'Lithuania');
INSERT INTO playsms_tblUser_country VALUES (167,'Luxembourg');
INSERT INTO playsms_tblUser_country VALUES (170,'Macedonia');
INSERT INTO playsms_tblUser_country VALUES (171,'Malawi');
INSERT INTO playsms_tblUser_country VALUES (173,'Malaysia');
INSERT INTO playsms_tblUser_country VALUES (175,'Maldives');
INSERT INTO playsms_tblUser_country VALUES (177,'Mali Republic');
INSERT INTO playsms_tblUser_country VALUES (178,'Malta');
INSERT INTO playsms_tblUser_country VALUES (181,'Mauritania');
INSERT INTO playsms_tblUser_country VALUES (184,'Mexico');
INSERT INTO playsms_tblUser_country VALUES (186,'Moldova');
INSERT INTO playsms_tblUser_country VALUES (188,'Mongolia');
INSERT INTO playsms_tblUser_country VALUES (189,'Montserrat');
INSERT INTO playsms_tblUser_country VALUES (190,'Morocco');
INSERT INTO playsms_tblUser_country VALUES (192,'Mozambique');
INSERT INTO playsms_tblUser_country VALUES (193,'Myanmar');
INSERT INTO playsms_tblUser_country VALUES (194,'Namibia');
INSERT INTO playsms_tblUser_country VALUES (196,'Nepal');
INSERT INTO playsms_tblUser_country VALUES (197,'Netherlands');
INSERT INTO playsms_tblUser_country VALUES (201,'New Zealand');
INSERT INTO playsms_tblUser_country VALUES (202,'Nicaragua');
INSERT INTO playsms_tblUser_country VALUES (203,'Niger');
INSERT INTO playsms_tblUser_country VALUES (204,'Nigeria');
INSERT INTO playsms_tblUser_country VALUES (208,'Norway');
INSERT INTO playsms_tblUser_country VALUES (209,'Oman');
INSERT INTO playsms_tblUser_country VALUES (210,'Pakistan');
INSERT INTO playsms_tblUser_country VALUES (211,'Palau');
INSERT INTO playsms_tblUser_country VALUES (212,'Palestine');
INSERT INTO playsms_tblUser_country VALUES (213,'Panama');
INSERT INTO playsms_tblUser_country VALUES (214,'Papua New Guinea');
INSERT INTO playsms_tblUser_country VALUES (215,'Paraguay');
INSERT INTO playsms_tblUser_country VALUES (216,'Peru');
INSERT INTO playsms_tblUser_country VALUES (217,'Philippines');
INSERT INTO playsms_tblUser_country VALUES (220,'Poland');
INSERT INTO playsms_tblUser_country VALUES (223,'Portugal');
INSERT INTO playsms_tblUser_country VALUES (225,'Puerto Rico');
INSERT INTO playsms_tblUser_country VALUES (226,'Qatar');
INSERT INTO playsms_tblUser_country VALUES (228,'Romania');
INSERT INTO playsms_tblUser_country VALUES (229,'Russia');
INSERT INTO playsms_tblUser_country VALUES (232,'Rwanda');
INSERT INTO playsms_tblUser_country VALUES (238,'Samoa');
INSERT INTO playsms_tblUser_country VALUES (241,'Saudi Arabia');
INSERT INTO playsms_tblUser_country VALUES (242,'Senegal');
INSERT INTO playsms_tblUser_country VALUES (244,'Sierra Leone');
INSERT INTO playsms_tblUser_country VALUES (245,'Singapore');
INSERT INTO playsms_tblUser_country VALUES (248,'Slovakia');
INSERT INTO playsms_tblUser_country VALUES (249,'Slovenia');
INSERT INTO playsms_tblUser_country VALUES (251,'Somalia');
INSERT INTO playsms_tblUser_country VALUES (252,'South Africa');
INSERT INTO playsms_tblUser_country VALUES (253,'Spain');
INSERT INTO playsms_tblUser_country VALUES (256,'Sri Lanka');
INSERT INTO playsms_tblUser_country VALUES (257,'Sudan');
INSERT INTO playsms_tblUser_country VALUES (258,'Suriname');
INSERT INTO playsms_tblUser_country VALUES (259,'Swaziland');
INSERT INTO playsms_tblUser_country VALUES (260,'Sweden');
INSERT INTO playsms_tblUser_country VALUES (262,'Switzerland');
INSERT INTO playsms_tblUser_country VALUES (263,'Syria');
INSERT INTO playsms_tblUser_country VALUES (264,'Taiwan');
INSERT INTO playsms_tblUser_country VALUES (267,'Tajikistan');
INSERT INTO playsms_tblUser_country VALUES (268,'Tanzania');
INSERT INTO playsms_tblUser_country VALUES (269,'Thailand');
INSERT INTO playsms_tblUser_country VALUES (274,'Trinidad and Tobago');
INSERT INTO playsms_tblUser_country VALUES (275,'Tunisia');
INSERT INTO playsms_tblUser_country VALUES (276,'Turkey');
INSERT INTO playsms_tblUser_country VALUES (277,'Turkmenistan');
INSERT INTO playsms_tblUser_country VALUES (279,'Tuvalu');
INSERT INTO playsms_tblUser_country VALUES (280,'Uganda');
INSERT INTO playsms_tblUser_country VALUES (281,'Ukraine');
INSERT INTO playsms_tblUser_country VALUES (284,'USA');
INSERT INTO playsms_tblUser_country VALUES (289,'United Arab Emirates');
INSERT INTO playsms_tblUser_country VALUES (290,'Uruguay');
INSERT INTO playsms_tblUser_country VALUES (291,'Uzbekistan');
INSERT INTO playsms_tblUser_country VALUES (293,'Vatican City State');
INSERT INTO playsms_tblUser_country VALUES (294,'Venezuela');
INSERT INTO playsms_tblUser_country VALUES (295,'Vietnam');
INSERT INTO playsms_tblUser_country VALUES (299,'Yemen');
INSERT INTO playsms_tblUser_country VALUES (300,'Yugoslavia');
INSERT INTO playsms_tblUser_country VALUES (303,'Zambia');
INSERT INTO playsms_tblUser_country VALUES (305,'Zimbabwe');
INSERT INTO playsms_tblUser_country VALUES (312,'Ethiopia');
INSERT INTO playsms_tblUser_country VALUES (314,'South Korea');
INSERT INTO playsms_tblUser_country VALUES (318,'Angola');
INSERT INTO playsms_tblUser_country VALUES (319,'Aruba');
INSERT INTO playsms_tblUser_country VALUES (320,'Laos');
INSERT INTO playsms_tblUser_country VALUES (325,'Serbia & Montenegro (Yugoslavia)');
INSERT INTO playsms_tblUser_country VALUES (332,'Jersey');
INSERT INTO playsms_tblUser_country VALUES (334,'OTHER (unlisted)');

