-- phpMyAdmin SQL Dump
-- version 3.3.4-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 26, 2013 at 12:56 PM
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
-- Table structure for table `cbp_items`
--

CREATE TABLE IF NOT EXISTS `cbp_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metadata_id` int(11) NOT NULL,
  `item_number` varchar(4) NOT NULL,
  `process` enum('slice','export','import_mw','verify') NOT NULL DEFAULT 'slice',
  `status` enum('queued','error','started','completed') NOT NULL DEFAULT 'queued',
  `updated` timestamp NULL DEFAULT NULL,
  `completed` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_meta_item` (`metadata_id`,`item_number`),
  KEY `metadata_id` (`metadata_id`),
  KEY `process` (`process`),
  KEY `status` (`status`),
  KEY `completed` (`completed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
