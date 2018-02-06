-- --------------------------------------------------------
-- 호스트:                          10.0.0.22
-- 서버 버전:                        10.0.23-MariaDB-log - Source distribution
-- 서버 OS:                        Linux
-- HeidiSQL 버전:                  9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- toast 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `toast` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `toast`;

-- 테이블 toast.nt_certificate 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_certificate` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `memo` text,
  `certificate` text,
  `certificate_key` text,
  `client_certificate` text,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `name` (`o_no`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_config 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_config` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `key` varchar(45) NOT NULL,
  `val` text,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `key` (`o_no`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_deploy 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_deploy` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_no` int(11) unsigned NOT NULL,
  `t_no` int(11) unsigned NOT NULL,
  `deployed` varchar(45) NOT NULL DEFAULT '0.0.0',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `t_no` (`s_no`,`t_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_fleet 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_fleet` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `phase` varchar(45) NOT NULL,
  `fleet` varchar(45) NOT NULL,
  `apps` varchar(256) DEFAULT NULL,
  `health` int(11) unsigned DEFAULT '80',
  `lb_f_no` int(11) unsigned DEFAULT '0',
  `lb_port` varchar(256) DEFAULT '',
  `profile` text,
  `hosts` text,
  `script` text,
  `instance` text,
  `lb` text,
  `eip` varchar(1) DEFAULT NULL,
  `locked` varchar(1) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `phase` (`o_no`,`phase`,`fleet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_fleet_star 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_fleet_star` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f_no` int(11) unsigned NOT NULL,
  `u_no` int(11) unsigned NOT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_ip 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_ip` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `f_no` int(11) DEFAULT NULL,
  `s_no` int(11) DEFAULT NULL,
  `id` varchar(45) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `nt_ip_id_uindex` (`id`),
  KEY `nt_ip_f_no_s_no_index` (`f_no`,`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_log 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_log` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_no` int(11) unsigned NOT NULL,
  `u_no` int(11) unsigned NOT NULL,
  `data` text,
  `success` varchar(1) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`),
  KEY `sno` (`s_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_org 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_org` (
  `no` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_phase 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_phase` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `phase` varchar(45) DEFAULT NULL,
  `profile` text,
  `hosts` text,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `phase` (`o_no`,`phase`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_project 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_project` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `groupId` varchar(50) NOT NULL DEFAULT 'com.yanolja',
  `artifactId` varchar(100) NOT NULL,
  `packaging` varchar(10) NOT NULL DEFAULT 'war',
  `major` int(11) NOT NULL DEFAULT '1',
  `minor` int(11) NOT NULL DEFAULT '0',
  `build` int(11) NOT NULL DEFAULT '0',
  `git_url` varchar(100) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `artifactId` (`artifactId`),
  KEY `groupId` (`groupId`,`artifactId`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_project_star 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_project_star` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_no` int(11) unsigned NOT NULL,
  `u_no` int(11) unsigned NOT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_server 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_server` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `f_no` int(11) unsigned NOT NULL,
  `u_no` int(11) DEFAULT NULL,
  `a_no` int(11) DEFAULT NULL,
  `id` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `host` varchar(45) DEFAULT NULL,
  `port` varchar(5) DEFAULT NULL,
  `user` varchar(45) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `plugYN` varchar(1) DEFAULT NULL COMMENT 'lb',
  `locked` varchar(1) DEFAULT 'N',
  `power` varchar(1) DEFAULT 'Y',
  `toast` varchar(16) DEFAULT NULL,
  `os` varchar(256) DEFAULT NULL,
  `uptime` varchar(256) DEFAULT NULL,
  `cpu` float DEFAULT '0',
  `hdd` float DEFAULT '0',
  `instance` text,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NULL DEFAULT NULL,
  `ping_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pong_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`),
  KEY `f_no` (`o_no`,`f_no`),
  KEY `name` (`o_no`,`name`),
  KEY `id` (`o_no`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_server_mon 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_server_mon` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_no` int(11) unsigned NOT NULL,
  `uptime` varchar(256) DEFAULT NULL,
  `cpu` float DEFAULT '0',
  `hdd` float DEFAULT '0',
  `load_1` float DEFAULT '0',
  `load_5` float DEFAULT '0',
  `load_15` float DEFAULT '0',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`),
  KEY `s_no` (`s_no`,`reg_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_server_star 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_server_star` (
  `no` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `s_no` int(10) unsigned NOT NULL,
  `u_no` int(10) unsigned NOT NULL,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_status 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_status` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_target 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_target` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `f_no` int(11) unsigned NOT NULL,
  `p_no` int(11) unsigned NOT NULL,
  `version` varchar(45) NOT NULL DEFAULT '0.0.0',
  `domain` varchar(64) DEFAULT NULL,
  `port` int(11) DEFAULT '80',
  `deploy` varchar(10) DEFAULT 'web',
  `deployYN` varchar(1) DEFAULT 'Y',
  `le` varchar(1) DEFAULT 'N',
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`no`),
  KEY `f_no` (`f_no`,`p_no`,`deployYN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_user 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_user` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `o_no` int(11) unsigned NOT NULL,
  `provider` varchar(32) DEFAULT 'yanolja',
  `username` varchar(128) DEFAULT NULL,
  `nickname` varchar(128) DEFAULT NULL,
  `phoneNum` varchar(45) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `auth` varchar(64) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `con_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `memberNo` int(11) unsigned NOT NULL,
  `picture` varchar(256) DEFAULT NULL,
  `token` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `mno` (`o_no`,`provider`,`memberNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
-- 테이블 toast.nt_version 구조 내보내기
CREATE TABLE IF NOT EXISTS `nt_version` (
  `no` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_no` int(11) unsigned NOT NULL,
  `b_no` int(11) DEFAULT NULL,
  `version` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `branch` varchar(64) DEFAULT NULL,
  `git_id` varchar(32) DEFAULT NULL,
  `note` text,
  `lock` varchar(1) DEFAULT 'N',
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mod_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`no`),
  KEY `p_no` (`p_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 내보낼 데이터가 선택되어 있지 않습니다.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
