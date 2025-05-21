-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 11:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thesis_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `action` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `username`, `action`, `status`, `created_at`) VALUES
(1, 1, 'admin', 'User Login', 'success', '2025-05-15 08:15:07'),
(2, 3, 'butz', 'User Login', 'success', '2025-05-15 08:19:07'),
(3, 1, 'admin', 'User Login', 'success', '2025-05-15 08:42:16'),
(4, 3, 'butz', 'User Login', 'success', '2025-05-15 08:55:18'),
(5, 1, 'admin', 'User Login', 'success', '2025-05-15 08:56:16'),
(6, 3, 'butz', 'User Login', 'success', '2025-05-15 12:06:57'),
(7, 1, 'admin', 'User Login', 'success', '2025-05-15 12:09:13'),
(8, 1, 'admin', 'User Login', 'success', '2025-05-16 00:42:39'),
(9, 3, 'butz', 'User Login', 'success', '2025-05-16 00:47:15'),
(10, 1, 'admin', 'User Login', 'success', '2025-05-16 00:54:36'),
(11, 1, 'admin', 'User Login', 'success', '2025-05-16 01:00:33'),
(12, 1, 'admin', 'User Login', 'success', '2025-05-16 01:15:54'),
(13, 1, 'admin', 'User Login', 'success', '2025-05-16 01:44:16'),
(14, 1, 'admin', 'User Login', 'success', '2025-05-16 02:56:50'),
(17, 1, 'admin', 'User Login', 'success', '2025-05-16 03:06:22'),
(19, 1, 'admin', 'User Login', 'success', '2025-05-16 03:35:19'),
(21, 1, 'admin', 'User Login', 'success', '2025-05-16 07:23:21'),
(22, 1, 'admin', 'User Login', 'success', '2025-05-16 07:55:59'),
(23, 1, 'admin', 'User Login', 'success', '2025-05-16 08:37:28'),
(24, 1, 'admin', 'User Login', 'success', '2025-05-18 05:55:53'),
(25, 1, 'admin', 'User Login', 'success', '2025-05-18 06:29:08'),
(26, 1, 'admin', 'User Login', 'success', '2025-05-18 06:29:55'),
(27, 1, 'admin', 'User Login', 'success', '2025-05-18 07:09:23'),
(28, 1, 'admin', 'User Login', 'success', '2025-05-18 08:20:31'),
(29, 1, 'admin', 'User Login', 'success', '2025-05-18 08:25:30'),
(30, 1, 'admin', 'User Login', 'success', '2025-05-18 08:28:55'),
(31, 1, 'admin', 'User Login', 'success', '2025-05-18 08:38:45'),
(32, 1, 'admin', 'User Login', 'success', '2025-05-18 10:03:54'),
(33, 1, 'admin', 'User Login', 'success', '2025-05-18 10:07:58'),
(34, 1, 'admin', 'User Login', 'success', '2025-05-18 10:14:29'),
(35, 1, 'admin', 'User Login', 'success', '2025-05-18 10:19:57'),
(36, 1, 'admin', 'User Login', 'success', '2025-05-18 10:47:21'),
(37, 1, 'admin', 'User Login', 'success', '2025-05-18 10:50:23'),
(38, 1, 'admin', 'User Login', 'success', '2025-05-18 11:08:49'),
(39, 1, 'admin', 'User Login', 'success', '2025-05-18 12:05:59'),
(40, 1, 'admin', 'User Login', 'success', '2025-05-18 13:04:36'),
(41, 1, 'admin', 'User Login', 'success', '2025-05-19 00:32:40'),
(42, 1, 'admin', 'User Login', 'success', '2025-05-19 01:51:02'),
(43, 1, 'admin', 'User Login', 'success', '2025-05-19 02:34:08'),
(44, 1, 'admin', 'User Login', 'success', '2025-05-19 05:24:58'),
(45, 1, 'admin', 'User Login', 'success', '2025-05-19 05:39:41'),
(46, 1, 'admin', 'Failed Login Attempt', 'failed', '2025-05-19 06:12:39'),
(47, 1, 'admin', 'User Login', 'success', '2025-05-19 06:12:48'),
(49, 1, 'admin', 'User Login', 'success', '2025-05-19 06:25:16'),
(50, 1, 'admin', 'User Login', 'success', '2025-05-19 07:51:46'),
(51, 1, 'admin', 'User Login', 'success', '2025-05-19 08:15:33'),
(52, 1, 'admin', 'User Login', 'success', '2025-05-19 08:31:13'),
(53, 1, 'admin', 'User Login', 'success', '2025-05-19 09:54:45'),
(54, 1, 'admin', 'User Login', 'success', '2025-05-19 10:03:28'),
(55, 1, 'admin', 'User Login', 'success', '2025-05-19 10:35:04'),
(56, 1, 'admin', 'User Login', 'success', '2025-05-19 12:12:56'),
(57, 1, 'admin', 'Viewed Admin Dashboard', '', '2025-05-19 12:16:31'),
(58, 1, 'admin', 'Viewed Admin Dashboard', '', '2025-05-19 12:16:33'),
(59, 1, 'admin', 'Viewed Admin Dashboard', '', '2025-05-19 12:16:50'),
(60, 1, 'admin', 'User Login', 'success', '2025-05-19 12:20:18'),
(61, 1, 'admin', 'User Login', 'success', '2025-05-19 12:25:05'),
(62, 1, 'admin', 'User Login', 'success', '2025-05-19 12:47:53'),
(63, 1, 'admin', 'User Login', 'success', '2025-05-19 13:13:16'),
(64, 1, 'admin', 'User Login', 'success', '2025-05-19 14:28:50'),
(65, 1, 'admin', 'User Login', 'success', '2025-05-20 09:20:11'),
(66, 1, 'admin', 'User Login', 'success', '2025-05-20 09:28:04'),
(67, 1, 'admin', 'User Login', 'success', '2025-05-21 01:40:18'),
(68, 1, 'admin', 'User Login', 'success', '2025-05-21 02:13:42'),
(69, 1, 'admin', 'User Login', 'success', '2025-05-21 02:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploader` varchar(255) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved` tinyint(1) DEFAULT 0,
  `thesis_name` varchar(255) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `uploader_email` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `filename`, `uploader`, `upload_date`, `status`, `approved`, `thesis_name`, `group_name`, `department_name`, `uploaded_by`, `uploader_email`, `user_id`) VALUES
(19, 'GROUP-5 (1).pptx', '', '2025-05-16 11:05:44', 'rejected', 0, 'GAgsas', 'asgasg', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(20, 'ThesisRealm-Survey.pdf', '', '2025-05-16 11:05:58', 'approved', 1, 'LKGAKG', 'GKGKJAKJ', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(21, 'Index-JQuery.pdf', '', '2025-05-16 15:54:02', 'approved', 1, 'kjdhksjhj', 'lskafjlksf', 'BSFT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(22, 'Database-IM.pptx', '', '2025-05-16 15:54:20', 'approved', 1, 'JGHakjghakjh', 'ahgalkjhgakjhga', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(23, 'MidterxmExam.pdf', '', '2025-05-16 15:54:38', 'approved', 1, 'afsczxc', 'zxczxvhgku', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(24, 'draftIM.xlsx', '', '2025-05-16 15:54:58', 'approved', 1, 'l;gaoiguaiougi', 'ajkhkjlaahjkajshkga', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(25, 'System-Proposal.pptx', '', '2025-05-16 15:55:23', 'approved', 1, 'jalkghakh', 'KJNVZKJZNVKJ', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(26, 'Blank diagram.pdf', '', '2025-05-16 15:55:41', 'approved', 1, ';KJKJLFGKJLKGL', 'KJLGKJGGKJGK', 'BSEMC', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(27, 'MidterxmExam.pdf', '', '2025-05-18 18:14:11', 'approved', 1, 'HWajsj', 'shfasjfj', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(28, 'Venture-Initiation (1).pdf', '', '2025-05-18 18:47:07', 'rejected', 0, 'Hallelujah', 'GAHAHAHAH', 'BSAT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(29, 'SgycPurComInfographic.pdf', '', '2025-05-18 18:50:07', 'approved', 1, 'WTF', 'AHAHAHAHAHS', 'BSEMC', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(30, 'ThesisRealm-Survey (1).pdf', '', '2025-05-18 19:08:17', 'approved', 1, 'tryialll', 'bakalboys', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(31, 'IT126-IT129-Proposal-Documentation.pdf', '', '2025-05-18 20:05:38', 'approved', 1, 'oihoihohoi', '5yeytytyut', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(32, 'SgycResume.pdf', '', '2025-05-18 21:04:18', 'approved', 1, 'Hesoyam', 'Htsaws', 'BSAT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(33, 'Sagayoc-Output 8 Data Presentation and Interpretation.pdf', '', '2025-05-19 14:11:50', 'rejected', 0, 'lkhnvamv', 'manmnvnm', 'BSAT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(34, 'Sagyoc-Output 3-Chapter 2.2.pdf', '', '2025-05-19 14:12:08', 'rejected', 0, 'lkfnalksfsf', 'aksjfajsasf', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(35, 'SagayocApplicationLetter.pdf', '', '2025-05-19 14:12:26', 'approved', 1, 'asfasfvlkmkl', 'nksvnkvn', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(36, 'Sagayoc-Output 2-Chapter 2.1.pdf', '', '2025-05-19 16:29:39', 'approved', 1, 'Hatsawss', 'Secret', 'BSEMC', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(37, 'Sagayoc-Output 7 Table 1 - 3 Data Analysis.pdf', '', '2025-05-19 16:30:44', 'approved', 1, 'KJKJJFA', 'kjahsjkfhakfj', 'BSET', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(38, 'Sagayoc_CaseStudy.pdf', '', '2025-05-19 20:19:26', 'approved', 1, 'One Piece', 'Nika', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(39, 'letters.pdf', '', '2025-05-19 20:19:47', 'rejected', 0, 'Buko no pico', 'HEHE', 'BSAT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL),
(40, 'ProjectPropss.pdf', '', '2025-05-21 10:13:15', 'approved', 1, 'Capstone repo', 'Triple Threats', 'BSIT', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `verification_token` varchar(64) DEFAULT NULL,
  `role` enum('Admin','Teacher','Student') DEFAULT 'Teacher',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `login_count` int(11) DEFAULT 0,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `login_method` varchar(50) DEFAULT 'custom',
  `reset_code` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `name`, `email`, `password`, `status`, `verification_token`, `role`, `created_at`, `profile_picture`, `last_login`, `is_online`, `login_count`, `firstname`, `lastname`, `login_method`, `reset_code`, `is_verified`) VALUES
(1, 'admin', 'Armando B. Sagayoc Jr.', 'cooliit13@Gmail.com', '$2y$10$G01vtiql4JIfAcUWOCfzVO61v3Q6IrMfsFEhY82gICH34X2tPVXeC', 'active', NULL, 'Admin', '2025-05-15 08:14:20', 'https://lh3.googleusercontent.com/a/ACg8ocLmLmOYevFst9CtNST_2tytIm_gKgFjvVSseTLFaVYmuY8QEy0o=s96-c', '2025-05-21 10:15:34', 1, 57, 'Armando', 'B. Sagayoc Jr.', 'custom', '813004', 0),
(2, '', 'Armando Sagayoc', '2301104471@student.buksu.edu.ph', NULL, 'active', NULL, 'Teacher', '2025-05-16 00:53:51', 'https://lh3.googleusercontent.com/a/ACg8ocLZG7AGYFtmpIjoYj_lQLREeXL8rVJsRUNP60D4MDMb5Vm8EnK1=s96-c', '2025-05-21 10:12:39', 0, 31, 'Armando', 'Sagayoc', 'google', NULL, 0),
(3, 'butz', NULL, 'agha13@gmail.com', '$2y$10$GTRo/JhKp5aM9/kaPmJSSuuWih6/sdLPRhJnPPkunQd8CKsrbYdPS', 'inactive', NULL, 'Teacher', '2025-05-15 08:18:53', NULL, '2025-05-16 08:47:15', 1, 4, 'butch', 'Sagayoc', 'custom', NULL, 0),
(45, 'Marv', NULL, 'MarkV@buksu.edu.ph', '$2y$10$OcfQ.Hs2oaceBUT8uEA4gu3it3LmpIWflwUcHD8jGBz8vcHWUwkju', 'inactive', 'd93116dd153ec473230fa19c9e1f0fd9', 'Teacher', '2025-05-21 02:11:50', NULL, NULL, 0, 0, 'Mark', 'Villar', 'custom', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
