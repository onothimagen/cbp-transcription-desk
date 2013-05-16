-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2013 at 06:08 AM
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
-- Table structure for table `cbp_boxes`
--

CREATE TABLE IF NOT EXISTS `cbp_boxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_queue_id` int(11) NOT NULL,
  `box_number` varchar(4) NOT NULL,
  `process` enum('import','slice','export','import_mw','verify','archive') NOT NULL DEFAULT 'import',
  `process_status` enum('error','started','completed','stopped') NOT NULL DEFAULT 'started',
  `process_start_time` timestamp NULL DEFAULT NULL,
  `process_end_time` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `box_number` (`box_number`),
  KEY `process` (`process`),
  KEY `process_status` (`process_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_boxes`
--


-- --------------------------------------------------------

--
-- Table structure for table `cbp_error_log`
--

CREATE TABLE IF NOT EXISTS `cbp_error_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_queue_id` int(11) unsigned DEFAULT NULL,
  `box_id` int(15) DEFAULT NULL,
  `folio_id` int(15) DEFAULT NULL,
  `item_id` int(15) DEFAULT NULL,
  `process` enum('import','slice','export','import_mw','verify','archive') DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `box_id` (`box_id`),
  UNIQUE KEY `folio_id` (`folio_id`),
  UNIQUE KEY `item_id` (`item_id`),
  UNIQUE KEY `job_queue_id_2` (`job_queue_id`),
  UNIQUE KEY `box_id_2` (`box_id`),
  UNIQUE KEY `folio_id_2` (`folio_id`),
  UNIQUE KEY `item_id_2` (`item_id`),
  KEY `process` (`process`),
  KEY `job_queue_id` (`job_queue_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_error_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `cbp_folios`
--

CREATE TABLE IF NOT EXISTS `cbp_folios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_number` int(11) NOT NULL,
  `box_id` int(11) NOT NULL,
  `folio_number` varchar(25) NOT NULL,
  `second_folio_number` varchar(25) DEFAULT NULL,
  `category` varchar(40) DEFAULT NULL,
  `recto_verso` varchar(5) DEFAULT NULL,
  `creator` varchar(255) DEFAULT NULL,
  `recipient` varchar(255) DEFAULT NULL,
  `penner` varchar(255) DEFAULT NULL,
  `marginals` varchar(255) DEFAULT NULL,
  `corrections` varchar(255) DEFAULT NULL,
  `date_1` varchar(10) DEFAULT NULL,
  `date_2` varchar(10) DEFAULT NULL,
  `date_3` varchar(10) DEFAULT NULL,
  `date_4` varchar(10) DEFAULT NULL,
  `date_5` varchar(10) DEFAULT NULL,
  `date_6` varchar(10) DEFAULT NULL,
  `estimated_date` varchar(10) DEFAULT NULL,
  `info_in_main_heading_field` varchar(255) DEFAULT NULL,
  `main_heading` varchar(255) DEFAULT NULL,
  `sub_headings` varchar(255) DEFAULT NULL,
  `marginal_summary_numbering` varchar(50) DEFAULT NULL,
  `number_of_pages` tinyint(4) DEFAULT NULL,
  `page_numbering` varchar(50) DEFAULT NULL,
  `titles` varchar(255) DEFAULT NULL,
  `watermarks` varchar(60) DEFAULT NULL,
  `paper_producer` varchar(255) DEFAULT NULL,
  `paper_producer_in_year` varchar(255) DEFAULT NULL,
  `notes_public` varchar(50) DEFAULT NULL,
  `process` enum('import','slice','export','import_mw','verify','archive') NOT NULL DEFAULT 'import',
  `process_status` enum('started','completed','stopped','error') NOT NULL DEFAULT 'started',
  `process_start_time` timestamp NULL DEFAULT NULL,
  `process_end_time` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_box_folio` (`box_id`,`folio_number`),
  KEY `process` (`process`),
  KEY `process_status` (`process_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_folios`
--


-- --------------------------------------------------------

--
-- Table structure for table `cbp_items`
--

CREATE TABLE IF NOT EXISTS `cbp_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio_id` int(11) NOT NULL,
  `item_number` varchar(4) NOT NULL,
  `process` enum('import','slice','export','import_mw','verify','archive') NOT NULL DEFAULT 'slice',
  `process_status` enum('error','started','completed','stopped') NOT NULL DEFAULT 'started',
  `process_start_time` timestamp NULL DEFAULT NULL,
  `process_end_time` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_folio_item` (`folio_id`,`item_number`),
  KEY `item_number` (`item_number`),
  KEY `process` (`process`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `cbp_job_queue`
--

CREATE TABLE IF NOT EXISTS `cbp_job_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `job_status` enum('started','completed','error','stopped') NOT NULL DEFAULT 'started',
  `job_start_time` timestamp NULL DEFAULT NULL,
  `job_end_time` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_job_queue`
--


-- --------------------------------------------------------

--
-- Table structure for table `cbp_process_log`
--

CREATE TABLE IF NOT EXISTS `cbp_process_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `job_queue_id` int(11) NOT NULL,
  `process` enum('import','slice','export','import_mw','verify','archive') NOT NULL,
  `status` varchar(10) NOT NULL,
  `started` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_job_process` (`job_queue_id`,`process`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `cbp_process_log`
--

