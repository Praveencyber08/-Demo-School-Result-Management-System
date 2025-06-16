-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 05:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_results`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `date`, `status`, `created_at`) VALUES
(1, '20241235', '2025-06-07', 'present', '2025-06-07 04:48:39'),
(3, '20241234', '2025-06-07', 'absent', '2025-06-07 04:49:35'),
(5, '20241269', '2025-06-07', 'present', '2025-06-07 04:58:10'),
(6, '1101', '2025-06-07', 'present', '2025-06-07 08:40:10'),
(7, '24btbt071', '2025-06-08', 'absent', '2025-06-08 10:37:00'),
(8, '20241235', '2025-06-09', 'present', '2025-06-09 17:23:01'),
(9, '20241234', '2025-06-09', 'present', '2025-06-09 17:23:04'),
(11, '1101', '2025-06-09', 'present', '2025-06-09 17:23:12'),
(12, '20241269', '2025-06-09', 'present', '2025-06-09 17:23:15');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `event_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Login', '2025-06-07 04:23:48'),
(2, 1, 'Added student: 20241234', '2025-06-07 04:24:40'),
(3, 1, 'Added result for student: 20241234', '2025-06-07 04:24:55'),
(4, 1, 'Added result for student: 20241234', '2025-06-07 04:27:22'),
(5, 1, 'Added result for student: 20241234', '2025-06-07 04:27:39'),
(6, 1, 'Added result for student: 20241234', '2025-06-07 04:28:22'),
(7, 1, 'Added result for student: 20241234', '2025-06-07 04:29:00'),
(8, 1, 'Added result for student: 20241234', '2025-06-07 04:29:27'),
(9, 1, 'Login', '2025-06-07 04:36:37'),
(10, 1, 'Added student: 20241235', '2025-06-07 04:42:15'),
(11, 1, 'Added result for student: 20241235', '2025-06-07 04:42:40'),
(12, 1, 'Marked attendance for student: 20241235 on 2025-06-07', '2025-06-07 04:48:39'),
(13, 1, 'Marked attendance for student: 20241234 on 2025-06-07', '2025-06-07 04:49:35'),
(14, 1, 'Logout', '2025-06-07 04:53:10'),
(15, 2, 'Login', '2025-06-07 04:53:48'),
(16, 2, 'Logout', '2025-06-07 04:54:02'),
(17, 1, 'Login', '2025-06-07 04:54:07'),
(18, 1, 'Logout', '2025-06-07 04:54:13'),
(19, 2, 'Login', '2025-06-07 04:55:07'),
(20, 2, 'Added student: 20241269', '2025-06-07 04:55:44'),
(21, 2, 'Added result for student: 20241269', '2025-06-07 04:55:58'),
(22, 2, 'Added result for student: 20241234', '2025-06-07 04:56:05'),
(23, 2, 'Added result for student: 20241234', '2025-06-07 04:56:11'),
(24, 2, 'Added result for student: 20241234', '2025-06-07 04:56:18'),
(25, 2, 'Added result for student: 20241234', '2025-06-07 04:56:24'),
(26, 2, 'Added result for student: 20241269', '2025-06-07 04:56:37'),
(27, 2, 'Added result for student: 20241269', '2025-06-07 04:56:47'),
(28, 2, 'Added result for student: 20241269', '2025-06-07 04:56:59'),
(29, 2, 'Added result for student: 20241269', '2025-06-07 04:57:06'),
(30, 2, 'Added result for student: 20241269', '2025-06-07 04:57:14'),
(31, 2, 'Marked attendance for student: 20241269 on 2025-06-07', '2025-06-07 04:58:10'),
(32, 2, 'Login', '2025-06-07 08:24:38'),
(33, 2, 'Logout', '2025-06-07 08:36:13'),
(34, 2, 'Login', '2025-06-07 08:36:45'),
(35, 2, 'Added student: 1101', '2025-06-07 08:37:41'),
(36, 2, 'Added result for student: 20241234', '2025-06-07 08:38:01'),
(37, 2, 'Added result for student: 1101', '2025-06-07 08:38:09'),
(38, 2, 'Added result for student: 1101', '2025-06-07 08:38:16'),
(39, 2, 'Added result for student: 1101', '2025-06-07 08:38:22'),
(40, 2, 'Added result for student: 1101', '2025-06-07 08:38:30'),
(41, 2, 'Marked attendance for student: 1101 on 2025-06-07', '2025-06-07 08:40:10'),
(42, 1, 'Student Login', '2025-06-07 09:00:23'),
(43, 2, 'Logout', '2025-06-07 09:02:02'),
(44, 1, 'Student Login', '2025-06-07 09:02:35'),
(45, 1, 'Student recorded payment of 1000 on 2025-06-07', '2025-06-07 09:03:02'),
(46, 2, 'Login', '2025-06-07 09:15:03'),
(47, 2, 'Accessed Reports', '2025-06-07 09:18:49'),
(48, 2, 'Accessed Reports', '2025-06-07 09:19:06'),
(49, 2, 'Accessed Reports', '2025-06-07 09:19:45'),
(50, 2, 'Logout', '2025-06-08 10:33:13'),
(51, 3, 'Login', '2025-06-08 10:34:12'),
(52, 3, 'Added student: 24btbt071', '2025-06-08 10:35:04'),
(53, 3, 'Added result for student: 20241234', '2025-06-08 10:35:24'),
(54, 3, 'Added result for student: 24btbt071', '2025-06-08 10:35:53'),
(55, 3, 'Added result for student: 24btbt071', '2025-06-08 10:36:19'),
(56, 3, 'Accessed Reports', '2025-06-08 10:36:24'),
(57, 3, 'Marked attendance for student: 24btbt071 on 2025-06-08', '2025-06-08 10:37:00'),
(58, 3, 'Logout', '2025-06-08 10:37:12'),
(59, 2, 'Student Login', '2025-06-08 10:38:18'),
(60, 2, 'Login', '2025-06-09 17:22:53'),
(61, 2, 'Marked attendance for student: 20241235 on 2025-06-09', '2025-06-09 17:23:01'),
(62, 2, 'Marked attendance for student: 20241234 on 2025-06-09', '2025-06-09 17:23:04'),
(63, 2, 'Marked attendance for student: 1101 on 2025-06-09', '2025-06-09 17:23:12'),
(64, 2, 'Marked attendance for student: 20241269 on 2025-06-09', '2025-06-09 17:23:15'),
(65, 2, 'Accessed Reports', '2025-06-09 17:23:33'),
(66, 2, 'Accessed Reports', '2025-06-09 17:23:38'),
(67, 2, 'Logout', '2025-06-09 17:24:18');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fee_type` enum('school_fees','bus_fees','library_fees','exam_fees','other') NOT NULL DEFAULT 'school_fees',
  `section` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `amount`, `payment_date`, `status`, `created_at`, `fee_type`, `section`) VALUES
(1, '1101', 1000.00, '2025-06-07', 'completed', '2025-06-07 09:03:02', 'school_fees', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `marks` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `subject`, `marks`, `semester`, `created_at`) VALUES
(1, '20241234', 'Tamil', 99, 1, '2025-06-07 04:24:55'),
(2, '20241234', 'English', 92, 1, '2025-06-07 04:27:22'),
(3, '20241234', 'Maths', 95, 1, '2025-06-07 04:27:39'),
(4, '20241234', 'computer science', 99, 1, '2025-06-07 04:28:22'),
(5, '20241234', 'chemistry', 95, 1, '2025-06-07 04:29:00'),
(6, '20241234', 'physics', 90, 1, '2025-06-07 04:29:27'),
(7, '20241235', 'English', 99, 1, '2025-06-07 04:42:40'),
(8, '20241269', 'Tamil', 99, 1, '2025-06-07 04:55:58'),
(9, '20241234', 'English', 55, 1, '2025-06-07 04:56:05'),
(10, '20241234', 'Maths', 99, 1, '2025-06-07 04:56:11'),
(11, '20241234', 'computer science', 100, 1, '2025-06-07 04:56:18'),
(12, '20241234', 'chemistry', 99, 1, '2025-06-07 04:56:24'),
(13, '20241269', 'physics', 99, 1, '2025-06-07 04:56:37'),
(14, '20241269', 'English', 99, 1, '2025-06-07 04:56:47'),
(15, '20241269', 'Maths', 99, 1, '2025-06-07 04:56:59'),
(16, '20241269', 'computer science', 99, 1, '2025-06-07 04:57:06'),
(17, '20241269', 'chemistry', 99, 1, '2025-06-07 04:57:14'),
(18, '20241234', 'Tamil', 99, 1, '2025-06-07 08:38:01'),
(19, '1101', 'Tamil', 99, 1, '2025-06-07 08:38:09'),
(20, '1101', 'English', 99, 1, '2025-06-07 08:38:16'),
(21, '1101', 'Maths', 85, 1, '2025-06-07 08:38:22'),
(22, '1101', 'computer science', 100, 1, '2025-06-07 08:38:30'),
(23, '20241234', 'Tamil', 0, 2, '2025-06-08 10:35:24'),
(24, '24btbt071', 'English', 10, 2, '2025-06-08 10:35:53'),
(25, '24btbt071', 'physics', 50, 2, '2025-06-08 10:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `class` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `first_name`, `last_name`, `class`, `created_at`) VALUES
(1, '20241234', 'praveen', 'G', '12', '2025-06-07 04:24:40'),
(2, '20241235', 'naveen', 'G', '12', '2025-06-07 04:42:15'),
(3, '20241269', 'Prema', 'G', '12', '2025-06-07 04:55:44'),
(4, '1101', 'praveen kumar', 'G', '11', '2025-06-07 08:37:41'),
(5, '24btbt071', 'naveen', 'l', 'b tech', '2025-06-08 10:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `student_users`
--

CREATE TABLE `student_users` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_users`
--

INSERT INTO `student_users` (`id`, `student_id`, `username`, `password`, `email`, `created_at`) VALUES
(1, '1101', 'praveen kumar', '$2y$10$VjKJclipeNsSDlwclRe82eUtNMPR8tN77y7C7iANETb5CmhewCgn.', 'f@gmail.com', '2025-06-07 08:59:58'),
(2, '24btbt071', 'naveen l', '$2y$10$mVVtD7yy8LLhtI.krzwC/OoVd78QjIE783wAN1GaFvgcrBFDyMEgG', 'naveen@gmail.com', '2025-06-08 10:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('teacher','admin') DEFAULT 'teacher',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `reset_token`, `reset_expiry`, `created_at`) VALUES
(1, 'admin', '$2y$10$w2CtbWafNCMyzMndpc9grerWvb2mRgQGr8RRgUds.1iu55h7N.AuG', 'f@gmail.com', 'teacher', 'd8ea56d376bdd589d06c8c596bd18176a8dddb21e8020e43ad3b371490358403', '2025-06-07 07:37:03', '2025-06-07 04:23:41'),
(2, 'Praveen ', '$2y$10$Brdps9J7HvKy8waDnEGyf.brkhfl.3.oeIaFE/LRtwSHqseoB442W', 'hh@gmail.com', 'teacher', NULL, NULL, '2025-06-07 04:53:34'),
(3, 'tharani', '$2y$10$BdjWvLKKrCjqr/YkozAjBer/Z.k2qNBgreVkZQP4tf8l8RvDgPX2W', 'tharani@gmail.com', 'teacher', NULL, NULL, '2025-06-08 10:33:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`date`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `student_users`
--
ALTER TABLE `student_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_users`
--
ALTER TABLE `student_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_users`
--
ALTER TABLE `student_users`
  ADD CONSTRAINT `student_users_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
