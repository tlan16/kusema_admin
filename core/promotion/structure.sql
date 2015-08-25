-- Setting Up Database
DROP TABLE IF EXISTS `asset`;
CREATE TABLE `asset` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`assetId` varchar(32) NOT NULL DEFAULT '',
	`type` varchar(20) NOT NULL DEFAULT '',
	`filename` varchar(100) NOT NULL DEFAULT '',
	`mimeType` varchar(50) NOT NULL DEFAULT '',
	`path` varchar(200) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`type`)
	,UNIQUE INDEX (`assetId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`content` LONGTEXT NOT NULL ,
	`authorName` varchar(50) NULL ,
	`authorId` int(10) unsigned NULL DEFAULT NULL,
	`refId` varchar(50) NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(50) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	,INDEX (`authorId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`title`)
	,INDEX (`authorName`)
	,INDEX (`refId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `answerinfo`;
CREATE TABLE `answerinfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`value` varchar(255) NULL DEFAULT '',
	`entityId` int(10) unsigned NULL DEFAULT 0,
	`entityName` varchar(50) NULL DEFAULT '',
	`answerId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`answerId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`value`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `answerinfotype`;
CREATE TABLE `answerinfotype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`content` LONGTEXT NOT NULL ,
	`authorName` varchar(50) NULL ,
	`authorId` int(10) unsigned NULL DEFAULT NULL,
	`refId` varchar(50) NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
	,INDEX (`authorId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`title`)
	,INDEX (`authorName`)
	,INDEX (`refId`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `commentsinfo`;
CREATE TABLE `commentsinfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`value` varchar(255) NULL DEFAULT '',
	`entityId` int(10) unsigned NULL DEFAULT 0,
	`entityName` varchar(50) NULL DEFAULT '',
	`commentsId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`commentsId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`value`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `commentsinfotype`;
CREATE TABLE `commentsinfotype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`content` LONGTEXT NOT NULL ,
	`authorName` varchar(50) NULL ,
	`authorId` int(10) unsigned NULL DEFAULT NULL,
	`refId` varchar(50) NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`authorId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`title`)
	,INDEX (`authorName`)
	,INDEX (`refId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `questioninfo`;
CREATE TABLE `questioninfo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`value` varchar(255) NULL DEFAULT '',
	`entityId` int(10) unsigned NULL DEFAULT 0,
	`entityName` varchar(50) NULL DEFAULT '',
	`questionId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`questionId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`value`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `questioninfotype`;
CREATE TABLE `questioninfotype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`code` varchar(25) NOT NULL DEFAULT '',
	`year` int(2) unsigned NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
	,INDEX (`code`)
	,INDEX (`year`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `unit_topic`;
CREATE TABLE `unit_topic` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`unitId` int(10) unsigned NOT NULL DEFAULT 0,
	`topicId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`unitId`)
	,INDEX (`topicId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`firstName` varchar(50) NOT NULL DEFAULT '',
	`lastName` varchar(50) NOT NULL DEFAULT '',
	`email` varchar(100) NULL DEFAULT '',
	`monashId` varchar(25) NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`firstName`)
	,INDEX (`lastName`)
	,INDEX (`email`)
	,INDEX (`monashId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(32) NOT NULL DEFAULT '',
	`data` longtext NOT NULL ,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`key`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `systemsettings`;
CREATE TABLE `systemsettings` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL DEFAULT '',
	`value` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,UNIQUE INDEX (`type`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccount`;
CREATE TABLE `useraccount` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(100) NOT NULL DEFAULT '',
	`password` varchar(40) NOT NULL DEFAULT '',
	`personId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`personId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`password`)
	,UNIQUE INDEX (`username`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role_useraccount`;
CREATE TABLE `role_useraccount` (
	`roleId` int(10) unsigned NOT NULL,
	`useraccountId` int(10) unsigned NOT NULL,
	`created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	`createdById` int(10) unsigned NOT NULL,
	UNIQUE KEY `uniq_role_useraccount` (`roleId`,`useraccountId`),
	KEY `idx_role_useraccount_roleId` (`roleId`),
	KEY `idx_role_useraccount_useraccountId` (`useraccountId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE `userprofile` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`personId` int(10) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`personId`)
	,INDEX (`typeId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `userprofiletype`;
CREATE TABLE `userprofiletype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

-- Completed CRUD Setup.