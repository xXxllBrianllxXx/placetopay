-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 13, 2018 at 11:30 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/***************************************************************************/
/* Base de datos: "placetopay" */
/***************************************************************************/

CREATE DATABASE IF NOT EXISTS `placetopay` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `placetopay`;

/***************************************************************************/
/* Estructura tabla "bancos" */
/***************************************************************************/

DROP TABLE IF EXISTS `bancos`;
CREATE TABLE IF NOT EXISTS `bancos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/***************************************************************************/
/* Estructura tabla "transacciones" */
/***************************************************************************/

DROP TABLE IF EXISTS `transacciones`;
CREATE TABLE IF NOT EXISTS `transacciones` (
  `idt` int(11) NOT NULL,
  `state` int(11) DEFAULT '0',
  `transactionID` int(11) DEFAULT NULL,
  `sessionID` varchar(32) DEFAULT NULL,
  `returnCode` varchar(30) DEFAULT NULL,
  `trazabilityCode` varchar(40) DEFAULT NULL,
  `transactionCycle` int(11) DEFAULT NULL,
  `transactionState` varchar(20) DEFAULT NULL,
  `bankCurrency` varchar(3) DEFAULT NULL,
  `bankFactor` float DEFAULT NULL,
  `bankURL` varchar(255) DEFAULT NULL,
  `responseCode` int(11) DEFAULT NULL,
  `responseReasonCode` varchar(3) DEFAULT NULL,
  `responseReasonText` varchar(255) DEFAULT NULL,
  `bankCode` varchar(4) DEFAULT NULL,
  `bankInterface` varchar(1) DEFAULT NULL,
  `reference` varchar(32) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `requestDate` datetime DEFAULT NULL,
  `bankProcessDate` datetime DEFAULT NULL,
  `uid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/***************************************************************************/
/* Estructura tabla "variables" */
/***************************************************************************/

DROP TABLE IF EXISTS `variables`;
CREATE TABLE IF NOT EXISTS `variables` (
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/***************************************************************************/
/* Se declaran las primary key */
/***************************************************************************/

ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id`);
/***************************************************************************/
ALTER TABLE `transacciones`
  ADD PRIMARY KEY (`idt`);
/***************************************************************************/
ALTER TABLE `transacciones`
  MODIFY `idt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;
