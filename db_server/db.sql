-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 29, 2018 at 07:58 PM
-- Server version: 5.6.38
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `safedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `browsertab`
--

CREATE TABLE `browsertab` (
  `fingerprint` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `browsertab`
--
ALTER TABLE `browsertab`
  ADD PRIMARY KEY (`fingerprint`);
