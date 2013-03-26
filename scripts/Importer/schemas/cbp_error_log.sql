-- phpMyAdmin SQL Dump
-- version 3.3.4-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 26, 2013 at 05:24 PM
-- Server version: 5.5.15
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `transcription`
--

-- --------------------------------------------------------

--
-- Table structure for table `cbp_error_log`
--

CREATE TABLE IF NOT EXISTS `cbp_error_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_queue_id` int(11) unsigned NOT NULL,
  `box` varchar(15) DEFAULT NULL,
  `folio` varchar(15) DEFAULT NULL,
  `item` varchar(15) DEFAULT NULL,
  `process` enum('import','slice','export','import_mw','verify') DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `process` (`process`),
  KEY `job_queue_id` (`job_queue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
