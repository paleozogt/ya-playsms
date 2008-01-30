## mysqldiff 0.30
## 
## run on Wed Jan 30 10:11:09 2008
##
## ---   db: playsms_0_8_1 (user=root)
## +++   db: playsms_0_8_2 (user=root)

ALTER TABLE `playsms_tblConfig_main` ADD COLUMN `cfg_system_from` varchar(100) default NULL COMMENT 'comma-delimited';
CREATE TABLE `playsms_featAutoSend` (
  `id` int(11) NOT NULL auto_increment,
  `frequency` enum('hourly','daily','weekly','monthly') NOT NULL default 'daily',
  `number` varchar(100) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


