-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2015 at 03:19 PM
-- Server version: 5.6.21
-- PHP Version: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yourlist`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tugas_karyawan`
--

CREATE TABLE IF NOT EXISTS `tbl_tugas_karyawan` (
`id_tugas_karyawan` int(100) NOT NULL,
  `id_user` int(100) NOT NULL,
  `id_tugas` int(100) NOT NULL,
  `time_start` datetime NOT NULL,
  `time_finish` datetime NOT NULL,
  `status` int(1) NOT NULL,
  `creator` int(100) NOT NULL,
  `created` datetime NOT NULL,
  `changer` int(100) NOT NULL,
  `changed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_tugas_karyawan`
--
ALTER TABLE `tbl_tugas_karyawan`
 ADD PRIMARY KEY (`id_tugas_karyawan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_tugas_karyawan`
--
ALTER TABLE `tbl_tugas_karyawan`
MODIFY `id_tugas_karyawan` int(100) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

