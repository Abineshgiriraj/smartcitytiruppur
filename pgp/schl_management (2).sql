-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2024 at 06:15 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schl_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `acc_years`
--

CREATE TABLE `acc_years` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_year` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_year` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acc_year` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `acc_years`
--

INSERT INTO `acc_years` (`id`, `unique_id`, `from_year`, `to_year`, `acc_year`, `remarks`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '66acae2317711', '2020-06', '2021-04', '2020 - 2021', 'Academic year', 1, 1, '2024-08-02 04:30:03', '2024-08-07 00:25:33'),
(2, '66acae471cbd1', '2021-06', '2022-04', '2021 - 2022', 'Testing', 1, 0, '2024-08-02 04:30:39', '2024-08-02 04:30:39'),
(3, '66acae8734d5a', '2022-06', '2023-04', '2022 - 2023', 'acc year', 1, 0, '2024-08-02 04:31:43', '2024-08-07 00:25:23'),
(4, '66b068fd5c920', '2019-08', '2020-08', '2019 - 2020', NULL, 1, 1, '2024-08-05 00:24:05', '2024-08-05 00:24:27'),
(5, '66b30c35cbc07', '2024-06', '2025-04', '2024 - 2025', NULL, 1, 0, '2024-08-07 00:25:01', '2024-08-07 00:25:01');

-- --------------------------------------------------------

--
-- Table structure for table `auth_key`
--

CREATE TABLE `auth_key` (
  `id` int(11) NOT NULL,
  `key_name` varchar(50) NOT NULL,
  `api_key` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `auth_key`
--

INSERT INTO `auth_key` (`id`, `key_name`, `api_key`) VALUES
(1, 'Auth', '8A9A8E2548D07801C');

-- --------------------------------------------------------

--
-- Table structure for table `biometric_attendance`
--

CREATE TABLE `biometric_attendance` (
  `id` int(3) NOT NULL,
  `machine_no` varchar(50) NOT NULL,
  `enroll_no` varchar(50) NOT NULL,
  `enroll_name` varchar(80) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `punching_datetime` varchar(80) NOT NULL,
  `verify_mode` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `device_id` varchar(50) NOT NULL,
  `ip_address` varchar(100) NOT NULL,
  `port_no` int(10) NOT NULL,
  `password` varchar(80) NOT NULL,
  `location_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `biometric_attendance`
--

INSERT INTO `biometric_attendance` (`id`, `machine_no`, `enroll_no`, `enroll_name`, `department_name`, `punching_datetime`, `verify_mode`, `created_at`, `device_id`, `ip_address`, `port_no`, `password`, `location_name`) VALUES
(1, '1', '5', 'ranjith\r\n', '1st', '05-08-2024 08:30\r\n', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(2, '1', '5', 'ranjith', '1st', '05-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(3, '1', '5', 'ranjith', '1st', '05-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(4, '1', '5', 'ranjith', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(5, '1', '5', 'ranjith', '1st', '06-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(6, '1', '5', 'ranjith', '1st', '06-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(7, '1', '5', 'ranjith', '1st', '06-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(8, '1', '5', 'ranjith', '1st', '06-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(9, '1', '5', 'ranjith', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(10, '1', '5', 'ranjith', '1st', '01-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(11, '1', '5', 'ranjith', '1st', '01-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(12, '1', '5', 'ranjith', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(13, '1', '5', 'ranjith', '1st', '02-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(14, '1', '5', 'ranjith', '1st', '02-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(15, '1', '5', 'ranjith', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(16, '1', '5', 'ranjith', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(17, '1', '5', 'ranjith', '1st', '07-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(18, '1', '5', 'ranjith', '1st', '07-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(19, '1', '5', 'ranjith', '1st', '07-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(20, '1', '5', 'ranjith', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', ''),
(21, '1', '6', 'kumar', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(22, '1', '6', 'kumar', '1st', '01-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(23, '1', '6', 'kumar', '1st', '01-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(24, '1', '6', 'kumar', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(25, '1', '6', 'kumar', '1st', '02-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(26, '1', '6', 'kumar', '1st', '02-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(27, '1', '6', 'kumar', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(28, '1', '6', 'kumar', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(29, '1', '6', 'kumar', '1st', '05-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(30, '1', '6', 'kumar', '1st', '05-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(31, '1', '6', 'kumar', '1st', '05-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(32, '1', '6', 'kumar', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(33, '1', '6', 'kumar', '1st', '06-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(34, '1', '6', 'kumar', '1st', '06-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(35, '1', '6', 'kumar', '1st', '06-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(36, '1', '6', 'kumar', '1st', '06-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(37, '1', '6', 'kumar', '1st', '07-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(38, '1', '6', 'kumar', '1st', '07-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(39, '1', '6', 'kumar', '1st', '07-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(40, '1', '6', 'kumar', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', ''),
(41, '1', '7', 'prabu', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(42, '1', '7', 'prabu', '1st', '01-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:35:22', '1', '192.168.0.201', 5005, '', ''),
(43, '1', '7', 'prabu', '1st', '01-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(44, '1', '7', 'prabu', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(45, '1', '7', 'prabu', '1st', '02-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:35:51', '1', '192.168.0.201', 5005, '', ''),
(46, '1', '7', 'prabu', '1st', '02-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:36:07', '1', '192.168.0.201', 5005, '', ''),
(47, '1', '7', 'prabu', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(48, '1', '7', 'prabu', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(49, '1', '7', 'prabu', '1st', '05-08-2024 08:30', 'MorningClassOn', '2024-08-07 09:40:49', '1', '192.168.0.201', 5005, '', ''),
(50, '1', '7', 'prabu', '1st', '05-08-2024 09:10', 'BreakfastOn', '2024-08-07 09:40:53', '1', '192.168.0.201', 5005, '', ''),
(51, '1', '7', 'prabu', '1st', '05-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(52, '1', '7', 'prabu', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(53, '1', '7', 'prabu', '1st', '06-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:41:16', '1', '192.168.0.201', 5005, '', ''),
(54, '1', '7', 'prabu', '1st', '06-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:41:21', '1', '192.168.0.201', 5005, '', ''),
(55, '1', '7', 'prabu', '1st', '06-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(56, '1', '7', 'prabu', '1st', '06-08-2024 14:04', 'AfternoonClassOn\n', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(57, '1', '7', 'prabu', '1st', '07-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:41:39', '1', '192.168.0.201', 5005, '', ''),
(58, '1', '7', 'prabu', '1st', '07-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:41:53', '1', '192.168.0.201', 5005, '', ''),
(59, '1', '7', 'prabu', '1st', '07-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(60, '1', '7', 'prabu', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', ''),
(61, '969567', '987767', 'Prabu', '1st', '05-08-2024 08:20:01', 'BreakFastOn', '2024-08-07 11:23:55', '87687', '192.168.0.120', 5005, '121', 'GGHSS, PPM'),
(62, '86785', '43452', 'Sivaraj', '1st', '05-08-2024 08:40:20', 'BreakFastOn', '2024-08-07 11:23:55', '87687', '192.168.0.120', 5005, '121', 'GGHSS, PPM');

-- --------------------------------------------------------

--
-- Table structure for table `biometric_creation`
--

CREATE TABLE `biometric_creation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taluk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_details` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_date` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(2) NOT NULL DEFAULT 1,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `biometric_creation`
--

INSERT INTO `biometric_creation` (`id`, `unique_id`, `zone`, `district`, `taluk`, `school_location`, `device_code`, `device_name`, `serial_no`, `ip_details`, `user_name`, `password`, `created_date`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '66aa25b3b729e910', 'Salem', 'Chennai', 'Erode', 'test', 'test', 'test', '1233', '1222', 'admin', '123', '2024-07-06', 1, 0, NULL, '2024-07-31 06:23:32'),
(2, '66ab218587b57226', 'North', 'Chennai', 'Perundurai', 'tet', 'tets', 'etst', 'test', 'ets', 'test', 'test', '2024-08-01', 1, 0, NULL, '2024-08-01 04:28:04'),
(3, '66ab234f72dd4806', 'Namakkal', 'Chennai', 'Perundurai', 'Erode', 'test', 'test', 'test', 'test', 'test', 'test', '2024-08-09', 1, 1, '2024-08-01 00:25:27', '2024-08-01 00:32:00'),
(4, '66ab24fc92c59663', 'Namakkal', 'Chennai', 'Perundurai', 'test', 'test', 'test', 'test', 'test', 'test', 'tets', '2024-07-31', 1, 0, '2024-08-01 00:32:36', '2024-08-01 00:32:36'),
(5, '66ab25c9baef2725', 'North', 'Erode', 'Perundurai', 'testrtr', 'test', 'test', '12335', 'testtedt', 'test', 'tes', '2024-08-08', 1, 0, NULL, '2024-08-01 11:16:43'),
(6, '66ab2bb5ec641633', 'North', 'Erode', 'Perundurai', 'test', 'test', 'test', '123', 'test', 'test', 'test', '2024-08-08', 1, 0, NULL, '2024-08-01 11:06:57'),
(7, '66ab2bfdb0ad1469', 'North', 'Erode', 'Erode', 'test', 'test', 'test', 'test', '198.890.89.90', 'test', 'test', '2024-08-08', 1, 0, NULL, '2024-08-01 09:07:49'),
(8, '66ab3353352cf134', 'South', 'Namakkal', 'Sivagiri', 'Erode', 'test', 'tet', '1233', 'test', 'admin', '123', '2024-07-31', 1, 0, NULL, '2024-08-01 10:49:48'),
(9, '66ab5c1e89e37683', 'West', 'Erode', 'Anthiyur', 'test12', 'test', 'test', '1234', 'test', 'admin', '1233', '2024-08-08', 1, 0, NULL, '2024-08-02 00:12:06'),
(10, '66ab76b44ec93718', 'West', 'Chennai', 'Anthiyur', 'tet', 'tet', 'etst', 'tes', 'tes', 'etst', 'est', '2024-08-02', 1, 1, NULL, '2024-08-01 06:47:38'),
(11, '66ac7487a24ef145', 'South', 'Erode', 'Erode', 'test', 'test', 'test', 'test', 'test', 'test', 'test', '2024-08-01', 1, 0, '2024-08-02 00:24:15', '2024-08-02 00:24:15'),
(12, '66ac868fc8835324', 'South', 'Erode', 'Perundurai', 'test', 'test', 'test', 'test', '192.168.111.1', 'test', 'test@1223W', '2024-07-30', 1, 1, '2024-08-02 01:41:11', '2024-08-07 00:50:52'),
(13, '66b3121e8c1e316095', 'North', 'Erode', 'Erode', 'ahsyg', 'sdgvsg', 'hsgdv', 'gvsdy', '122.121.1.1', 'Prabu', 'Pra12311@', '2024-08-07', 1, 0, '2024-08-07 00:50:14', '2024-08-07 00:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `biometric_detail`
--

CREATE TABLE `biometric_detail` (
  `id` int(3) NOT NULL,
  `machine_no` varchar(50) NOT NULL,
  `enroll_no` varchar(50) NOT NULL,
  `enroll_name` varchar(80) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `punching_datetime` varchar(80) NOT NULL,
  `verify_mode` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `device_id` varchar(50) NOT NULL,
  `ip_address` varchar(100) NOT NULL,
  `port_no` int(10) NOT NULL,
  `password` varchar(80) NOT NULL,
  `location_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `biometric_detail`
--

INSERT INTO `biometric_detail` (`id`, `machine_no`, `enroll_no`, `enroll_name`, `department_name`, `punching_datetime`, `verify_mode`, `created_at`, `device_id`, `ip_address`, `port_no`, `password`, `location_name`) VALUES
(1, '1', '5', 'ranjith\r\n', '1st', '05-08-2024 08:30\r\n', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(2, '1', '5', 'ranjith', '1st', '05-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(3, '1', '5', 'ranjith', '1st', '05-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(4, '1', '5', 'ranjith', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(5, '1', '5', 'ranjith', '1st', '06-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(6, '1', '5', 'ranjith', '1st', '06-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(7, '1', '5', 'ranjith', '1st', '06-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(8, '1', '5', 'ranjith', '1st', '06-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(9, '1', '5', 'ranjith', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(10, '1', '5', 'ranjith', '1st', '01-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(11, '1', '5', 'ranjith', '1st', '01-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(12, '1', '5', 'ranjith', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(13, '1', '5', 'ranjith', '1st', '02-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(14, '1', '5', 'ranjith', '1st', '02-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(15, '1', '5', 'ranjith', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(16, '1', '5', 'ranjith', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(17, '1', '5', 'ranjith', '1st', '07-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(18, '1', '5', 'ranjith', '1st', '07-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(19, '1', '5', 'ranjith', '1st', '07-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(20, '1', '5', 'ranjith', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', ''),
(21, '1', '6', 'kumar', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(22, '1', '6', 'kumar', '1st', '01-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(23, '1', '6', 'kumar', '1st', '01-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(24, '1', '6', 'kumar', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(25, '1', '6', 'kumar', '1st', '02-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(26, '1', '6', 'kumar', '1st', '02-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(27, '1', '6', 'kumar', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(28, '1', '6', 'kumar', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(29, '1', '6', 'kumar', '1st', '05-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(30, '1', '6', 'kumar', '1st', '05-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(31, '1', '6', 'kumar', '1st', '05-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(32, '1', '6', 'kumar', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(33, '1', '6', 'kumar', '1st', '06-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(34, '1', '6', 'kumar', '1st', '06-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(35, '1', '6', 'kumar', '1st', '06-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(36, '1', '6', 'kumar', '1st', '06-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(37, '1', '6', 'kumar', '1st', '07-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(38, '1', '6', 'kumar', '1st', '07-08-2024 09:10', 'MorningClassOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(39, '1', '6', 'kumar', '1st', '07-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(40, '1', '6', 'kumar', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', ''),
(41, '1', '7', 'prabu', '1st', '01-08-2024 08:30', 'BreakfastOn', '0000-00-00 00:00:00', '1', '192.168.0.201', 5005, '', ''),
(42, '1', '7', 'prabu', '1st', '01-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:35:22', '1', '192.168.0.201', 5005, '', ''),
(43, '1', '7', 'prabu', '1st', '01-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:53', '1', '192.168.0.201', 5005, '', ''),
(44, '1', '7', 'prabu', '1st', '01-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:44', '1', '192.168.0.201', 5005, '', ''),
(45, '1', '7', 'prabu', '1st', '02-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:35:51', '1', '192.168.0.201', 5005, '', ''),
(46, '1', '7', 'prabu', '1st', '02-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:36:07', '1', '192.168.0.201', 5005, '', ''),
(47, '1', '7', 'prabu', '1st', '02-08-2024 13:05', 'NoonMealOn', '2024-08-07 10:20:00', '1', '192.168.0.201', 5005, '', ''),
(48, '1', '7', 'prabu', '1st', '02-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:51', '1', '192.168.0.201', 5005, '', ''),
(49, '1', '7', 'prabu', '1st', '05-08-2024 08:30', 'MorningClassOn', '2024-08-07 09:40:49', '1', '192.168.0.201', 5005, '', ''),
(50, '1', '7', 'prabu', '1st', '05-08-2024 09:10', 'BreakfastOn', '2024-08-07 09:40:53', '1', '192.168.0.201', 5005, '', ''),
(51, '1', '7', 'prabu', '1st', '05-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:17', '1', '192.168.0.201', 5005, '', ''),
(52, '1', '7', 'prabu', '1st', '05-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:20:57', '1', '192.168.0.201', 5005, '', ''),
(53, '1', '7', 'prabu', '1st', '06-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:41:16', '1', '192.168.0.201', 5005, '', ''),
(54, '1', '7', 'prabu', '1st', '06-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:41:21', '1', '192.168.0.201', 5005, '', ''),
(55, '1', '7', 'prabu', '1st', '06-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:30', '1', '192.168.0.201', 5005, '', ''),
(56, '1', '7', 'prabu', '1st', '06-08-2024 14:04', 'AfternoonClassOn\n', '2024-08-07 10:21:05', '1', '192.168.0.201', 5005, '', ''),
(57, '1', '7', 'prabu', '1st', '07-08-2024 08:30', 'BreakfastOn', '2024-08-07 09:41:39', '1', '192.168.0.201', 5005, '', ''),
(58, '1', '7', 'prabu', '1st', '07-08-2024 09:10', 'MorningClassOn', '2024-08-07 09:41:53', '1', '192.168.0.201', 5005, '', ''),
(59, '1', '7', 'prabu', '1st', '07-08-2024 13:05', 'NoonMealOn\n', '2024-08-07 10:19:44', '1', '192.168.0.201', 5005, '', ''),
(60, '1', '7', 'prabu', '1st', '07-08-2024 14:04', 'AfternoonClassOn', '2024-08-07 10:21:11', '1', '192.168.0.201', 5005, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `category_creation`
--

CREATE TABLE `category_creation` (
  `id` int(20) NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(2) NOT NULL DEFAULT 1,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_creation`
--

INSERT INTO `category_creation` (`id`, `unique_id`, `category_type`, `description`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '66a1141b9071e', 'Break Fast', 'Idly& Idiyapam', 1, 0, '2024-07-24 09:17:55', '2024-08-01 23:19:59'),
(2, '66a1ddc2d7bcf', 'Lunch', 'Veg Meals', 1, 0, '2024-07-24 23:38:18', '2024-07-24 23:38:18'),
(3, '66a1de08bdab3', 'School In', '09:20 AM', 1, 0, '2024-07-24 23:39:28', '2024-07-26 00:29:24'),
(15, '66a33e5695450', 'School Out', '04:30 PM', 1, 1, '2024-07-26 00:42:38', '2024-07-27 02:48:47'),
(16, '66aa10f87bc8c652', 'test', 'test', 1, 1, '2024-07-31 04:54:56', '2024-08-01 23:47:44'),
(17, '66ab397952a5a111', 'test22', 'test22', 1, 1, '2024-08-01 02:00:01', '2024-08-01 02:04:22'),
(18, '66ab3990df29b896', 'test22', 'test22', 1, 1, '2024-08-01 02:00:24', '2024-08-01 02:04:04'),
(19, '66ab39a8a96ab134', 'breakfast', 'test', 1, 1, '2024-08-01 02:00:48', '2024-08-01 02:03:58'),
(20, '66ab3f660b34e606', 'test', 'test', 1, 1, '2024-08-01 02:25:17', '2024-08-01 02:26:54'),
(21, '66ab3f7ebb1ad665', 'test', 'tets', 1, 1, '2024-08-01 02:25:42', '2024-08-01 02:26:50'),
(22, '66ab3fb7eefab110', 'new test', 'new test', 1, 1, '2024-08-01 02:26:39', '2024-08-01 02:26:45'),
(23, '66ab4347ae3a1940', 'school out', '09:00 AM', 1, 0, '2024-08-01 02:41:51', '2024-08-01 02:41:51'),
(24, '66ac649a47986391', 'test11', 'tets', 1, 1, '2024-08-01 23:16:18', '2024-08-01 23:16:26'),
(25, '66ac6c0ccd44f891', 'Test', 'test', 1, 1, '2024-08-01 23:48:04', '2024-08-01 23:51:53'),
(26, '66ac7094cebe8157', 'dthvsvgcshgfjxds', 'bdvdbfds', 1, 1, '2024-08-02 00:07:24', '2024-08-02 00:09:08'),
(27, '66b311bb50c4c90641', 'teste', 'hvfdtf', 1, 0, '2024-08-07 00:48:35', '2024-08-07 01:46:42'),
(28, '66b31f7b7a54848285', 'vjgh', NULL, 1, 0, '2024-08-07 01:47:15', '2024-08-07 01:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `unique_id`, `designation`, `remarks`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '66a8da4eeb13b', 'Principal', 'Remarks', 1, 0, '2024-07-30 06:49:26', '2024-07-30 06:49:26'),
(2, '66a9de4f4de40', 'Biology Teacher', 'Remarkste', 1, 0, '2024-07-31 01:18:47', '2024-08-04 23:46:43'),
(3, '66a9de6da12ad', 'PET Teacher', 'update ddd', 1, 0, '2024-07-31 01:19:17', '2024-07-31 04:17:17'),
(4, '66a9df6043775', 'Biology Teacher', 'Remark update', 1, 1, '2024-07-31 01:23:20', '2024-07-31 02:09:17'),
(6, '66b05c5e8062e', 'good', 'test', 1, 1, '2024-08-04 23:30:14', '2024-08-04 23:41:06'),
(7, '66b05d29bb55c', 'designation', 'remarks', 1, 1, '2024-08-04 23:33:37', '2024-08-04 23:41:15'),
(8, '66b05f02721b4', 'Testing ggg', 'Testing', 1, 1, '2024-08-04 23:41:30', '2024-08-04 23:46:52'),
(9, '66b0600578cf2', 'name', 'rename', 1, 0, '2024-08-04 23:45:49', '2024-08-04 23:45:49'),
(10, '66b060665a6cc', 'test', 'goose', 1, 1, '2024-08-04 23:47:26', '2024-08-05 00:40:36'),
(11, '66b3165b2ccd6', 'tested data', NULL, 1, 1, '2024-08-07 01:08:19', '2024-08-07 01:08:33');

-- --------------------------------------------------------

--
-- Table structure for table `device_detail_api`
--

CREATE TABLE `device_detail_api` (
  `id` int(11) NOT NULL,
  `entry_date` varchar(100) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `biometric_id` varchar(100) NOT NULL,
  `department` varchar(150) NOT NULL,
  `att_date` varchar(50) NOT NULL,
  `att_time` varchar(80) NOT NULL,
  `status` int(3) NOT NULL DEFAULT 0,
  `is_active` int(2) NOT NULL DEFAULT 1 COMMENT '0-not is active,1-is active',
  `is_delete` int(2) NOT NULL DEFAULT 0 COMMENT '0-no delete,1-deleted',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `device_detail_api`
--

INSERT INTO `device_detail_api` (`id`, `entry_date`, `device_id`, `biometric_id`, `department`, `att_date`, `att_time`, `status`, `is_active`, `is_delete`, `created_date`) VALUES
(8, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 06:42:28'),
(9, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 06:43:39'),
(10, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:00:46'),
(11, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:06:49'),
(12, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:06:53'),
(13, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:08:16'),
(14, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:09:31'),
(15, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:09:58'),
(16, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:10:03'),
(17, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:10:29'),
(18, '', '', '', '', '', '', 0, 1, 0, '2024-08-05 07:10:34'),
(19, '05-08-2024', '1234', '467574t', 'break_fast', '05-08-2024', '12:25:00', 1, 1, 0, '2024-08-05 07:10:43'),
(20, '05-08-2024', '1234', '467574t', 'break_fast', '05-08-2024', '12:25:00', 0, 1, 0, '2024-08-05 07:11:42'),
(21, '05-08-2024', '1234', '467574t', 'break_fast', '05-08-2024', '12:25:00', 1, 1, 0, '2024-08-05 07:12:07'),
(22, '05-08-2024', '987767', 'ADw35EDFd4', 'noon_meal', '05-08-2024', '12:25:00', 1, 1, 0, '2024-08-07 10:55:55'),
(23, '06-08-2024', '987768', 'BDw36EDFe5', 'morning_meal', '06-08-2024', '8:00:00', 1, 1, 0, '2024-08-07 10:55:55'),
(24, '05-08-2024', '987767', 'ADw35EDFd4', 'noon_meal', '05-08-2024', '12:25:00', 1, 1, 0, '2024-08-07 10:56:13'),
(25, '06-08-2024', '987768', 'BDw36EDFe5', 'morning_meal', '06-08-2024', '8:00:00', 1, 1, 0, '2024-08-07 10:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(2) DEFAULT 1,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`id`, `unique_id`, `zone`, `district`, `is_active`, `is_delete`, `description`, `created_at`, `updated_at`) VALUES
(1, '66b1b0dc01ed9742', '66b1a52c1d2d6869', 'Erode', 1, 0, 'test', '2024-08-05 23:42:59', '2024-08-05 23:42:59'),
(2, '66b1b0f00d523700', '66b1a53722bb6200', 'Madurai', 1, 0, 'test', '2024-08-05 23:43:19', '2024-08-05 23:43:19'),
(3, '66b1b1156123f539', '66b1a54290019153', 'Coimbatore', 1, 0, 'test', '2024-08-05 23:43:57', '2024-08-05 23:43:57'),
(4, '66b1b1265d1a2581', '66b1a5584a77f739', 'Nagapatinam', 1, 0, 'test', '2024-08-05 23:44:14', '2024-08-05 23:44:14'),
(5, '66b1b5bc2ea54134', '66b1a52c1d2d6869', 'Theni', 1, 0, 'test', NULL, NULL),
(6, '66b1c22e3d41a649', '66b1a5584a77f739', 'Nilgiris', 1, 0, 'test', NULL, NULL),
(7, '66b3111b6fcda47424', '66b1a52c1d2d6869', 'Chennai', 1, 1, NULL, NULL, '2024-08-07 00:46:00'),
(8, '66b31ef11a23295886', '66b1a53722bb6200', 'Covai', 1, 1, NULL, NULL, '2024-08-07 01:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(17, '2014_10_12_000000_create_users_table', 1),
(18, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(19, '2014_10_12_100000_create_password_resets_table', 1),
(20, '2019_08_19_000000_create_failed_jobs_table', 1),
(21, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(22, '2024_07_24_073712_create_zones_table', 2),
(23, '2024_07_26_054157_create_students_table', 3),
(24, '2024_07_24_091350_create_category_creation', 4),
(25, '2024_07_25_060028_create_district', 4),
(26, '2024_07_25_094252_create_academic_year_creation', 4),
(27, '2024_07_29_094705_create_biometric_creation', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taluk_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_delete` int(2) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `unique_id`, `school_name`, `taluk_name`, `district_name`, `zone_name`, `remark`, `is_delete`, `created_at`, `updated_at`) VALUES
(4, 'df9806e739da20240806051405', 'Manoj metriculation school', '1b6f9a4d3c2e7058', '6b1c3a9f5e7d4028', '8b7e1c3f5d9a6042', NULL, 0, '2024-08-05 23:44:05', '2024-08-07 00:32:54'),
(5, 'd76696c5867d20240806060432', 'GVSS', '1b6f9a4d3c2e7058', '0a5c9d4f6b7e8013', '2a8c4b6d9e1f3057', 'tets', 0, '2024-08-06 00:34:32', '2024-08-06 00:34:32'),
(6, 'a883045ed7c920240807080936', 'GGHSS, VALAPPADI', '9e3d1b5f7a4c6028', '5c9a2f7d3b4e1068', '4e2d6f9b7a1c3058', 't4', 0, '2024-08-07 02:39:36', '2024-08-07 02:47:59'),
(8, '4040e35739d320240807084347', 'GGHSS, VALAPPADI', '9e3d1b5f7a4c6028', '6b1c3a9f5e7d4028', '8b7e1c3f5d9a6042', 'test', 0, '2024-08-07 03:13:47', '2024-08-07 03:13:56');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acc_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `emp_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taluk_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `doj` date NOT NULL,
  `emp_contact` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `unique_id`, `acc_year`, `emp_name`, `emp_id`, `designation`, `district`, `taluk_name`, `zone`, `school`, `doj`, `emp_contact`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '731f93ce857c20240806122311', '1', 'test', 'test', '8f0d5b3c0f4a321a', '4c7f8b2d6a3e9051', '9e3d1b5f7a4c6028', '3c5f7a1e6d9b8042', '8b7e1c3f5d9a6042', '2024-08-05', '1234567890', 1, 0, '2024-08-06 06:53:11', '2024-08-06 06:53:11'),
(2, 'b720e7ab869b20240807070458', '2', 'Prabu', 'hukg', '8f0d5b3c0f4a321a', '4c7f8b2d6a3e9051', '9e3d1b5f7a4c6028', '9c6b3f8d1a5e7024', '8b7e1c3f5d9a6042', '2024-08-07', '3442365478', 1, 0, '2024-08-07 01:34:58', '2024-08-07 01:34:58'),
(3, 'ba091253ec0020240807070915', '1', 'Prabu', '476446', '8f0d5b3c0f4a321a', '2e5f9d7a4b6c1038', '1b6f9a4d3c2e7058', '3c5f7a1e6d9b8042', '2a8c4b6d9e1f3057', '2024-08-07', '9898989898', 1, 0, '2024-08-07 01:39:15', '2024-08-07 01:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `standard_creation`
--

CREATE TABLE `standard_creation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_taluk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medium` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `standard` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `standard_creation`
--

INSERT INTO `standard_creation` (`id`, `unique_id`, `school_id`, `school_name`, `school_address`, `school_taluk`, `school_district`, `school_zone`, `medium`, `standard`, `remarks`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, '66b3114f9ac1615584', '141414', 'Govt Boys Higher Secondary School', '7/8,Govt Boys school,Karungalpalayam,Erode', 'Karungalapalayam', 'Erode', 'North', 'Tamil', '1', NULL, 1, 0, NULL, '2024-08-07 00:47:29'),
(2, '66b3117272b4433192', '2323322', 'Govt Boys Higher Secondary School', '7/8,Govt Boys school,Karungalpalayam,Erode', 'Karungalapalayam', 'Erode', 'North', 'English', '2', NULL, 1, 1, '2024-08-07 00:47:22', '2024-08-07 00:47:33');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emis_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biometric_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `udise_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stu_dob` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cur_std` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_sec` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medium` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schl_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schl_block` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schl_district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_delete` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `unique_id`, `emis_no`, `biometric_id`, `device_id`, `school_id`, `udise_code`, `student_name`, `stu_dob`, `cur_std`, `group_name`, `class_sec`, `medium`, `schl_name`, `schl_block`, `schl_district`, `zone`, `father_name`, `mother_name`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'c7696d22-0e52-462f-b094-47cd662e6dc0', '1015050972', 'Chennai', 'Chennai', '64765143651', '33081901402', 'BIRUNTHA R', '2010-05-02', '10', NULL, 'B', 'Tamil', 'GGHSS, VALAPPADI', 'Valappadi', 'SALEM', 'North', 'RAMACHANDRAN', 'GOMATHI', '0', '2024-07-26 04:50:46', '2024-07-26 04:50:46'),
(2, 'f5be9c2f-2f1b-46b0-b18f-d47ca2f59870', '1015050972', 'Salem', 'Salem', '68646547', '33081901402', 'BIRUNTHA R', '2010-05-02', '10', NULL, 'B', 'Tamil', 'GGHSS, VALAPPADI', 'Valappadi', 'SALEM', 'North', 'RAMACHANDRAN', 'GOMATHI', '0', '2024-07-29 06:45:50', '2024-07-29 06:45:50'),
(3, 'fbb5a0a4-8de9-44c1-8bc8-971ac9d145aa', '1015050972', 'Chennai', 'Chennai', '8765875', '33081901402', 'BIRUNTHA R', '2010-05-02', '10', NULL, 'B', 'Tamil', 'GGHSS, VALAPPADI', 'Valappadi', 'SALEM', 'North', 'RAMACHANDRAN', 'GOMATHI', '0', '2024-08-07 03:27:53', '2024-08-07 03:27:53');

-- --------------------------------------------------------

--
-- Table structure for table `taluk`
--

CREATE TABLE `taluk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taluk_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taluk`
--

INSERT INTO `taluk` (`id`, `unique_id`, `zone_name`, `district_name`, `taluk_name`, `remark`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(6, '66b0bcfff3761605', 'North', 'Tiruchy', 'Srirangam', 'tets', 1, 0, '2024-08-05 06:22:32', '2024-08-05 06:22:32'),
(8, '66b2377f4789d150', '66b1a5584a77f739', 'test', 'etst', 'create', 1, 0, '2024-08-06 09:17:27', '2024-08-06 09:17:27'),
(9, '66b241c26fd86603', '66b1a52c1d2d6869', '66b1b0dc01ed9742', 'test', 'test', 1, 0, '2024-08-06 10:01:14', '2024-08-06 10:01:14'),
(10, '66b316dc18ac181757', '66b1a53722bb6200', '66b1b0dc01ed9742', 'dsresrse', NULL, 1, 0, '2024-08-07 01:10:28', '2024-08-07 01:10:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Prabu', 'prabu@gmail.com', NULL, '123', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remarks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `unique_id`, `zone`, `remarks`, `is_active`, `is_delete`, `created_at`, `updated_at`) VALUES
(3, '66b1a52c1d2d6869', 'North', 'test', 1, 0, '2024-08-05 22:53:08', '2024-08-05 22:53:08'),
(4, '66b1a53722bb6200', 'South', 'test', 1, 0, '2024-08-05 22:53:19', '2024-08-05 22:53:19'),
(5, '66b1a54290019153', 'West', 'test', 1, 0, '2024-08-05 22:53:30', '2024-08-05 22:53:30'),
(6, '66b1a5584a77f739', 'East', 'test', 1, 0, '2024-08-05 22:53:52', '2024-08-05 22:53:52'),
(7, '66b3143c5234015328', 'yteyrty', '', 1, 1, '2024-08-07 00:59:16', '2024-08-07 00:59:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acc_years`
--
ALTER TABLE `acc_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academic_year_creation_unique_id_unique` (`unique_id`);

--
-- Indexes for table `auth_key`
--
ALTER TABLE `auth_key`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biometric_attendance`
--
ALTER TABLE `biometric_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biometric_creation`
--
ALTER TABLE `biometric_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `biometric_detail`
--
ALTER TABLE `biometric_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_creation`
--
ALTER TABLE `category_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `designations_unique_id_unique` (`unique_id`);

--
-- Indexes for table `device_detail_api`
--
ALTER TABLE `device_detail_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_unique_id_unique` (`unique_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_emp_id_unique` (`emp_id`);

--
-- Indexes for table `standard_creation`
--
ALTER TABLE `standard_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_unique_id_unique` (`unique_id`);

--
-- Indexes for table `taluk`
--
ALTER TABLE `taluk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zones_unique_id_unique` (`unique_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acc_years`
--
ALTER TABLE `acc_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `auth_key`
--
ALTER TABLE `auth_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `biometric_attendance`
--
ALTER TABLE `biometric_attendance`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `biometric_creation`
--
ALTER TABLE `biometric_creation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `biometric_detail`
--
ALTER TABLE `biometric_detail`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `category_creation`
--
ALTER TABLE `category_creation`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `device_detail_api`
--
ALTER TABLE `device_detail_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `standard_creation`
--
ALTER TABLE `standard_creation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `taluk`
--
ALTER TABLE `taluk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
