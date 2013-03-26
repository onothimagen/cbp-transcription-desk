-- phpMyAdmin SQL Dump
-- version 3.3.4-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2013 at 04:34 PM
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
-- Table structure for table `cbp_metadata`
--

CREATE TABLE IF NOT EXISTS `cbp_metadata` (
  `id` int(11) NOT NULL,
  `box_number` varchar(3) NOT NULL,
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
  `job_queue_id` int(11) NOT NULL,
  `process` enum('import','slice','export','import_mw','verify') NOT NULL DEFAULT 'import',
  `status` enum('queued','started','completed') NOT NULL DEFAULT 'queued',
  `updated` timestamp NULL DEFAULT NULL,
  `completed` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_box_folio` (`box_number`,`folio_number`),
  KEY `job_queue_id` (`job_queue_id`),
  KEY `process` (`process`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cbp_metadata`
--

