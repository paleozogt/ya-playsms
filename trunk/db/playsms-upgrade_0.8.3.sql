## mysqldiff 0.30
## 
## run on Tue Jan 29 09:38:32 2008
##
## ---   db: playsms_0_8_2 (user=root)
## +++   db: playsms_0_8_3 (user=root)

ALTER TABLE `playsms_featAutoSend` CHANGE COLUMN `frequency` `frequency` enum('hourly','daily','weekly','monthly','startup') NOT NULL default 'daily'; # was enum('hourly','daily','weekly','monthly') NOT NULL default 'daily'
ALTER TABLE `playsms_featAutoreply_scenario` CHANGE COLUMN `autoreply_scenario_result` `autoreply_scenario_result` text NOT NULL; # was varchar(130) NOT NULL default ''
ALTER TABLE `playsms_tblConfig_main` ADD COLUMN `id` int(11) NOT NULL auto_increment primary key first;
ALTER TABLE `playsms_tblConfig_main` ADD COLUMN `cfg_web_url` varchar(250) NOT NULL after `id`;
ALTER TABLE `playsms_tblConfig_main` ADD COLUMN `version` varchar(25) default NULL COMMENT 'DATABASE VERSION - DO NOT EDIT BY HAND';
ALTER TABLE `playsms_tblSMSIncoming` CHANGE COLUMN `in_msg` `in_msg` text NOT NULL; # was varchar(200) NOT NULL default ''
ALTER TABLE `playsms_tblSMSOutgoing` CHANGE COLUMN `p_msg` `p_msg` text NOT NULL; # was varchar(250) NOT NULL default ''
ALTER TABLE `playsms_tblSMSOutgoing` ADD COLUMN `send_tries` tinyint(4) NOT NULL default '1';
ALTER TABLE `playsms_tblSMSTemplate` CHANGE COLUMN `t_text` `t_text` text NOT NULL; # was varchar(130) NOT NULL default ''
ALTER TABLE `playsms_tblUserInbox` CHANGE COLUMN `in_msg` `in_msg` text NOT NULL; # was varchar(200) NOT NULL default ''
CREATE TABLE `db_info` (
  `id` tinyint(4) NOT NULL,
  `version` varchar(25) NOT NULL,
  `misc` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

update playsms_tblConfig_main set version='0.8.3';

