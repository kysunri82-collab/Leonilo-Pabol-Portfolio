-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2026 at 07:34 PM
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
-- Database: `leonilo_portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(10) UNSIGNED NOT NULL,
  `identifier_hash` char(64) NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_attempt` datetime DEFAULT NULL,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `tagline` varchar(150) NOT NULL,
  `introduction` text NOT NULL,
  `email` varchar(254) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` tinyint unsigned NOT NULL DEFAULT 1,
  `website_title` varchar(150) NOT NULL DEFAULT 'Leonilo Pabol',
  `logo_path` varchar(255) DEFAULT NULL,
  `appearance_color` char(7) NOT NULL DEFAULT '#e8c61f',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'school',
  `technologies` varchar(255) DEFAULT NULL,
  `project_image` varchar(255) DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'General',
  `description` text NOT NULL,
  `skill_level` varchar(50) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school` varchar(200) NOT NULL,
  `degree` varchar(200) NOT NULL,
  `start_year` varchar(20) DEFAULT NULL,
  `end_year` varchar(20) DEFAULT NULL,
  `description` text,
  `display_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_title` varchar(200) NOT NULL,
  `organization` varchar(200) NOT NULL,
  `start_date` varchar(50) DEFAULT NULL,
  `end_date` varchar(50) DEFAULT NULL,
  `description` text,
  `display_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `experience`
--

INSERT INTO `experience` (`id`, `job_title`, `organization`, `start_date`, `end_date`, `description`, `display_order`, `created_at`) VALUES
(2, 'Student Worker', 'Student Movement for Christ International (SMCI)', '2024', '2026', 'Served as a student worker, supporting campus ministry activities, student fellowships, evangelism, and Christian leadership initiatives. Helped organize activities that encouraged students to grow in faith, build relationships, and become godly, competent leaders.', 0, '2026-11-16 16:20:36'),
(3, 'Organization President', 'SMCI NORSU', NULL, NULL, 'Served as the student organization''s president, helping lead campus ministry initiatives, coordinate fellowships, encourage student participation, and support the mission of bringing Christ to the campus.', 1, '2026-11-16 16:20:36'),
(4, 'Customer Service Representative (CSR)', 'Inspiro Dumaguete', 'July 2025', 'September 2025', 'Worked as a Customer Service Representative, assisting customers with their concerns, communicating professionally, providing customer support, and developing problem-solving and customer service skills.', 2, '2026-11-16 16:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `organization` varchar(200) DEFAULT NULL,
  `achievement_date` varchar(50) DEFAULT NULL,
  `description` text,
  `display_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`id`, `title`, `organization`, `achievement_date`, `description`, `display_order`, `created_at`) VALUES
(1, 'Regional Schools Press Conference (RSPC)', NULL, '2024', 'Qualified for the Regional Schools Press Conference in Copyreading and Headline Writing, and participated as an Assistant Layout Artist.', 1, '2026-11-17 11:51:49'),
(2, 'Debate Contest Winner', NULL, NULL, NULL, 2, '2026-11-17 11:51:49'),
(3, 'Senior High School Graduate With Honors', NULL, NULL, 'Graduated from senior high school with honors in recognition of academic performance.', 3, '2026-11-17 11:51:49'),
(4, 'Animation Course – Video Advertisement Project', NULL, NULL, NULL, 4, '2026-11-17 11:51:49'),
(5, 'Leading Through Change: Adapt. Inspire. Transform.', NULL, NULL, NULL, 5, '2026-11-17 11:51:49'),
(6, 'Data Analytics for Decision-Making: Turn Data into Insights. Turn Insights into Action.', NULL, NULL, NULL, 6, '2026-11-17 11:51:49'),
(7, 'Smart Finance for Business: Control Costs. Improve Cash Flow. Protect Profit.', NULL, NULL, NULL, 7, '2026-11-17 11:51:49'),
(8, 'Cloud Computing & Digital Workplace: Work Smarter. Collaborate Anywhere.', NULL, NULL, NULL, 8, '2026-11-17 11:51:49'),
(9, 'Customer Experience Excellence: Turn Customers into Loyal Customers.', NULL, NULL, NULL, 9, '2026-11-17 11:51:49');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(254) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`username`,`email`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_login_identifier_hash` (`identifier_hash`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile`
--
ALTER TABLE `profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
