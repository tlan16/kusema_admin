-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 17, 2015 at 03:04 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kusemaadmin`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contactName` varchar(100) NOT NULL DEFAULT '',
  `contactNo` varchar(50) NOT NULL DEFAULT '',
  `street` varchar(200) NOT NULL DEFAULT '',
  `city` varchar(20) NOT NULL DEFAULT '',
  `region` varchar(20) NOT NULL DEFAULT '',
  `country` varchar(20) NOT NULL DEFAULT '',
  `postCode` varchar(10) NOT NULL DEFAULT '',
  `sKey` varchar(32) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `sKey` (`sKey`),
  KEY `contactName` (`contactName`),
  KEY `contactNo` (`contactNo`),
  KEY `city` (`city`),
  KEY `region` (`region`),
  KEY `country` (`country`),
  KEY `postCode` (`postCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE IF NOT EXISTS `asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assetId` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `mimeType` varchar(50) NOT NULL DEFAULT '',
  `path` varchar(200) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `assetId` (`assetId`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entityId` int(10) unsigned NOT NULL DEFAULT '0',
  `entityName` varchar(100) NOT NULL DEFAULT '',
  `comments` varchar(255) NOT NULL DEFAULT '',
  `groupId` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `entityId` (`entityId`),
  KEY `entityName` (`entityName`),
  KEY `groupId` (`groupId`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transId` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `entityId` int(10) unsigned NOT NULL DEFAULT '0',
  `entityName` varchar(100) NOT NULL DEFAULT '',
  `funcName` varchar(100) NOT NULL DEFAULT '',
  `msg` longtext NOT NULL,
  `comments` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `transId` (`transId`),
  KEY `entityId` (`entityId`),
  KEY `entityName` (`entityName`),
  KEY `type` (`type`),
  KEY `funcName` (`funcName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transId` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `from` varchar(200) NOT NULL DEFAULT '',
  `to` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(200) NOT NULL DEFAULT '',
  `body` longtext NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT '',
  `attachAssetIds` longtext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `transId` (`transId`),
  KEY `type` (`type`),
  KEY `from` (`from`),
  KEY `to` (`to`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL DEFAULT '',
  `lastName` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `firstName` (`firstName`),
  KEY `lastName` (`lastName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Peter', 'Wu', 1, '2014-03-06 19:47:35', 10, '2015-02-24 07:33:29', 10),
(2, 'James', 'van Veen', 1, '2014-03-06 19:47:35', 10, '2015-03-23 01:23:59', 10),
(3, 'Xixi', 'Tan', 1, '2014-03-06 19:47:35', 10, '2015-07-30 00:49:43', 10),
(4, 'Store Manager', 'user', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(5, 'Brian', 'Wu', 1, '2014-03-06 19:47:35', 10, '2014-10-28 07:54:21', 10),
(10, 'System', 'User', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(20, 'lin', 'he', 1, '2014-03-24 12:03:57', 4, '2014-03-24 12:04:23', 4),
(21, 'Bob', 'Zou', 1, '2014-03-25 06:44:37', 5, '2014-10-05 22:57:53', 5),
(22, 'test', 'test', 1, '2014-11-29 03:35:48', 5, '2014-11-29 03:35:48', 5),
(23, 'Michael', 'Yu', 1, '2014-12-13 02:11:20', 5, '2015-01-24 10:45:05', 5),
(24, 'Frank', 'Lan', 1, '2014-12-20 13:23:44', 20, '2014-12-20 13:23:44', 20),
(25, 'Sam', 'Zhou', 1, '2015-02-21 11:38:00', 5, '2015-05-25 04:21:33', 5),
(26, 'Peter', 'Wang', 1, '2015-02-21 11:40:12', 5, '2015-03-15 23:30:11', 5),
(27, 'Laurie', 'Zuccarini', 1, '2015-02-27 23:31:34', 5, '2015-02-27 23:31:34', 5),
(28, 'Erica', 'Fang', 1, '2015-03-15 23:28:41', 5, '2015-03-15 23:28:41', 5),
(29, 'Jon', 'Wong', 1, '2015-03-19 07:27:47', 5, '2015-05-28 00:48:58', 5),
(30, 'warehousetest', 'warehousetest', 1, '2015-05-02 01:24:44', 24, '2015-05-02 01:24:44', 24),
(31, 'Ben', 'Hamono', 1, '2015-06-30 02:17:47', 24, '2015-07-12 16:07:57', 24),
(32, 'Felix', 'Chen', 1, '2015-07-09 08:35:29', 5, '2015-07-08 22:35:29', 5);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Warehouse', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10),
(2, 'Purchasing', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10),
(3, 'Accounting', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10),
(4, 'Store Manager', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10),
(5, 'Administrator', 1, '2014-03-06 19:47:34', 10, '2014-03-06 08:47:34', 10),
(6, 'Sales', 1, '2014-12-20 13:11:22', 10, '2014-12-20 13:11:22', 10),
(7, 'Workshop', 1, '2015-05-22 12:32:20', 10, '2015-05-22 12:32:20', 10);

-- --------------------------------------------------------

--
-- Table structure for table `role_useraccount`
--

CREATE TABLE IF NOT EXISTS `role_useraccount` (
  `roleId` int(10) unsigned NOT NULL,
  `useraccountId` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createdById` int(10) unsigned NOT NULL,
  UNIQUE KEY `uniq_role_useraccount` (`roleId`,`useraccountId`),
  KEY `idx_role_useraccount_roleId` (`roleId`),
  KEY `idx_role_useraccount_useraccountId` (`useraccountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_useraccount`
--

INSERT INTO `role_useraccount` (`roleId`, `useraccountId`, `created`, `createdById`) VALUES
(1, 1, '2015-02-24 07:33:29', 1),
(1, 22, '2014-11-29 03:35:48', 5),
(1, 30, '2015-05-02 01:24:44', 24),
(3, 21, '2014-10-05 22:57:53', 5),
(3, 28, '2015-03-15 23:28:41', 5),
(4, 2, '2015-03-23 01:23:59', 5),
(4, 3, '2015-07-30 10:49:43', 3),
(4, 4, '2014-03-06 08:47:35', 10),
(4, 23, '2015-01-24 10:45:05', 20),
(4, 29, '2015-05-28 00:48:58', 5),
(4, 31, '2015-07-10 00:31:22', 5),
(5, 5, '2014-10-28 07:54:21', 5),
(5, 20, '2014-03-24 12:04:23', 4),
(5, 24, '2014-12-20 13:23:44', 20),
(6, 26, '2015-03-15 23:30:11', 5),
(6, 27, '2015-02-27 23:31:34', 5),
(6, 32, '2015-07-09 08:35:29', 5),
(7, 25, '2015-05-25 04:21:33', 5);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `key`, `data`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(2, 'rh51k43re9ef594vb8i2v08ko2', 'AuthExpireTime|i:1439474879;BPCinternal-System:ReturnUrl|s:12:"/favicon.ico";77c112581237a174588d8cc2dbc6b6a7|s:4080:"a:2:{i:0;s:3083:"a:2:{s:4:"user";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";s:5:"frank";s:21:"\0UserAccount\0password";s:40:"b85b2c37a170c7fa6100dcca17ba66d370207744";s:9:"\0*\0person";O:6:"Person":11:{s:17:"\0Person\0firstName";N;s:16:"\0Person\0lastName";N;s:15:"\0*\0userAccounts";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"24";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:8:"\0*\0roles";a:1:{i:0;O:4:"Role":10:{s:10:"\0Role\0name";s:13:"Administrator";s:15:"\0*\0userAccounts";a:0:{}s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:1:"5";s:9:"\0*\0active";i:1;s:10:"\0*\0created";s:19:"2014-03-06 19:47:34";s:12:"\0*\0createdBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:10:"\0*\0updated";s:19:"2014-03-06 19:47:34";s:12:"\0*\0updatedBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:12:"\0*\0proxyMode";b:0;}}s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"24";s:9:"\0*\0active";i:1;s:10:"\0*\0created";s:19:"2014-12-20 13:23:44";s:12:"\0*\0createdBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"20";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:10:"\0*\0updated";s:19:"2015-02-05 23:48:00";s:12:"\0*\0updatedBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"20";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:12:"\0*\0proxyMode";b:0;}s:4:"role";O:4:"Role":10:{s:10:"\0Role\0name";s:13:"Administrator";s:15:"\0*\0userAccounts";a:0:{}s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:1:"5";s:9:"\0*\0active";i:1;s:10:"\0*\0created";s:19:"2014-03-06 19:47:34";s:12:"\0*\0createdBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:10:"\0*\0updated";s:19:"2014-03-06 19:47:34";s:12:"\0*\0updatedBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:12:"\0*\0proxyMode";b:0;}}";i:1;s:964:"a:3:{s:4:"Name";s:5:"frank";s:7:"IsGuest";b:0;s:5:"Roles";a:1:{i:0;O:4:"Role":10:{s:10:"\0Role\0name";s:13:"Administrator";s:15:"\0*\0userAccounts";a:0:{}s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:1:"5";s:9:"\0*\0active";i:1;s:10:"\0*\0created";s:19:"2014-03-06 19:47:34";s:12:"\0*\0createdBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:10:"\0*\0updated";s:19:"2014-03-06 19:47:34";s:12:"\0*\0updatedBy";O:11:"UserAccount":12:{s:21:"\0UserAccount\0username";N;s:21:"\0UserAccount\0password";N;s:9:"\0*\0person";N;s:8:"\0*\0roles";N;s:13:"\0*\0_jsonArray";a:0:{}s:5:"\0*\0id";s:2:"10";s:9:"\0*\0active";N;s:10:"\0*\0created";N;s:12:"\0*\0createdBy";N;s:10:"\0*\0updated";N;s:12:"\0*\0updatedBy";N;s:12:"\0*\0proxyMode";b:1;}s:12:"\0*\0proxyMode";b:0;}}}";}";', 1, '2015-08-13 13:01:13', 10, '2015-08-13 03:07:59', 10);

-- --------------------------------------------------------

--
-- Table structure for table `systemsettings`
--

CREATE TABLE IF NOT EXISTS `systemsettings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `systemsettings`
--

INSERT INTO `systemsettings` (`id`, `type`, `value`, `description`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'b2b_soap_wsdl', 'http://localhost:8080/?soap=product.wsdl', 'Where the magento wsdl v2 is?', 1, '2014-03-06 19:47:35', 10, '2015-08-09 11:32:54', 10),
(2, 'b2b_soap_user', 'B2BUser', 'The user for the magento B2B', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(3, 'b2b_soap_key', 'B2BUser', 'The user for the magento API key', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(4, 'b2b_soap_timezone', 'Australia/Melbourne', 'The timezone the magento is operating on', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(5, 'b2b_soap_last_import_time', '2015-08-08 12:39:21', 'When did we do the imports from Magento last time', 1, '2014-03-06 19:47:35', 10, '2015-08-08 02:40:03', 10),
(6, 'system_timezone', 'Australia/Melbourne', 'The timezone this CURRENT SYSTEM is operating on', 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(7, 'asset_root_dir', '/var/www/appAssets/', 'The root directory of the assets', 1, '2014-10-14 11:21:42', 1, '2014-10-16 05:47:36', 1),
(8, 'sending_server_conf', '{"host":"server.budgetpc.com.au","port":"587","SMTPAuth":true,"username":"noreply@budgetpc.com.au","password":"budget123pc","SMTPSecure":"ssl","SMTPDebug":2,"debugOutput":"html"}', 'SMTP sending server conf', 1, '2014-12-20 13:11:57', 10, '2015-08-01 02:41:53', 10),
(9, 'sys_email_addr', 'noreply@budgetpc.com.au', 'system default email address', 1, '2014-12-21 14:16:01', 10, '2014-12-21 14:16:01', 10),
(10, 'allow_neg_stock', '0', 'allow negtive stock', 1, '2015-06-20 19:28:26', 10, '2015-06-19 23:30:30', 10),
(11, 'last_new_product_pull', '0001-01-01 00:00:00', 'last timestamp pull NEW product from magento to system', 1, '2015-07-18 03:17:50', 10, '2015-08-03 02:56:05', 10),
(12, 'last_new_price_push', '0001-01-01 00:00:00', 'last timestamp push NEW product price from system to magento', 1, '2015-07-18 03:17:50', 10, '2015-07-18 03:09:25', 10),
(13, 'last_product_pull_id', '1', 'last id of pull product from magento to system', 1, '2015-08-07 07:27:01', 10, '2015-08-06 21:27:01', 10);

-- --------------------------------------------------------

--
-- Table structure for table `useraccount`
--

CREATE TABLE IF NOT EXISTS `useraccount` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `personId` int(10) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `personId` (`personId`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`),
  KEY `password` (`password`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `useraccount`
--

INSERT INTO `useraccount` (`id`, `username`, `password`, `personId`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
(1, 'Peter', '328854132bf61a37c6b4a64be7b23d03b74f8f83', 1, 1, '2014-03-06 19:47:35', 10, '2015-02-24 07:33:29', 10),
(2, 'James', '917ffaf0b1101ef1c2621fc42f591f47ad41dccc', 2, 1, '2014-03-06 19:47:35', 10, '2015-03-23 01:23:59', 10),
(3, 'xixi', 'a72271ccb44cb01e70b837aba3f61e2f5152d21d', 3, 1, '2014-03-06 19:47:35', 10, '2015-07-30 00:49:43', 10),
(4, 'smuser', '12dea96fec20593566ab75692c9949596833adc9', 4, 0, '2014-03-06 19:47:35', 10, '2014-03-25 06:45:38', 10),
(5, 'brian', '5c967c02b9d4795ab9425777dbc24d58ec115269', 5, 1, '2014-03-06 19:47:35', 10, '2014-10-28 07:54:21', 10),
(10, '075ae3d2fc31640504f814f60e5ef713', 'disabled', 10, 1, '2014-03-06 19:47:35', 10, '2014-03-06 08:47:35', 10),
(20, 'helin16', '262bab1f48755709edd4c9c8774ec2d0d97857e7', 20, 1, '2014-03-24 12:03:57', 4, '2014-03-24 12:04:23', 4),
(21, 'bob', '562020a104cdeaaa4b6f37fc471c30444937d43e', 21, 1, '2014-03-25 06:44:37', 5, '2014-10-05 22:57:53', 5),
(22, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 22, 0, '2014-11-29 03:35:48', 5, '2015-02-22 11:20:48', 5),
(23, 'michael', '723d39e1930d1b151fdb1fc16bc995bc5d1314b5', 23, 1, '2014-12-13 02:11:20', 5, '2015-01-24 10:45:05', 5),
(24, 'frank', 'b85b2c37a170c7fa6100dcca17ba66d370207744', 24, 1, '2014-12-20 13:23:44', 20, '2015-02-05 12:48:00', 20),
(25, 'sam', '050989490f1fb728fd7e7866c9af0974d3d32470', 25, 1, '2015-02-21 11:38:00', 5, '2015-05-25 04:21:33', 5),
(26, 'peterpeter', '4b8373d016f277527198385ba72fda0feb5da015', 26, 1, '2015-02-21 11:40:12', 5, '2015-03-15 23:30:11', 5),
(27, 'Laurie', '8468275f0d120527324004754c3d088324306866', 27, 1, '2015-02-27 23:31:34', 5, '2015-02-27 23:31:34', 5),
(28, 'erica', '477a7e6d3493bb29cb1c599a8a543fff2fb9b85e', 28, 1, '2015-03-15 23:28:41', 5, '2015-03-15 23:28:41', 5),
(29, 'Jon', '44f878afe53efc66b76772bd845eb65944ed8232', 29, 1, '2015-03-19 07:27:47', 5, '2015-05-28 00:48:58', 5),
(30, 'warehousetest', '8d11039e72f4dcb3da90eec9be414cdd3bd9b112', 30, 0, '2015-05-02 01:24:44', 24, '2015-05-02 01:36:33', 24),
(31, 'ben', 'd4bb78ae9ec5bdf77dee2b06128337cab4919536', 31, 1, '2015-06-30 02:17:47', 24, '2015-07-12 16:07:22', 24),
(32, 'felix', '9c69098d379350e157eff4ad93150662007b8fb2', 32, 1, '2015-07-09 08:35:29', 5, '2015-07-08 22:35:29', 5);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
