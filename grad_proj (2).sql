-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2025 at 07:28 AM
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
-- Database: `grad_proj`
--

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenitiy_id` int(11) NOT NULL,
  `amenity` text NOT NULL,
  `room_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenitiy_id`, `amenity`, `room_id`) VALUES
(4, 'Lockers', NULL),
(5, 'Prayer Room', NULL),
(6, 'Air Conditioning', NULL),
(7, 'Outdoor View', NULL),
(8, 'Projector', NULL),
(9, 'Cafetria', NULL),
(11, 'On-weekends', NULL),
(12, 'Free Coffee', NULL),
(13, 'Covered Parking', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `automated_replies`
--

CREATE TABLE `automated_replies` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `automated_replies`
--

INSERT INTO `automated_replies` (`id`, `question`, `answer`, `created_at`) VALUES
(4, 'How can I reset my password?', 'To reset your password, go to settings and click on \"Forgot Password\".', '2025-04-03 20:39:04'),
(5, 'How do I contact support?', 'You can contact support via email at support@example.com or call +123456789.', '2025-04-03 20:39:04'),
(6, 'What are your working hours?', 'Our working hours are from 9 AM to 6 PM, Monday to Friday.', '2025-04-03 20:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `num_people` int(11) NOT NULL,
  `total_price` float DEFAULT NULL,
  `status` varchar(255) DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `review-email` tinyint(4) NOT NULL DEFAULT 0,
  `pay_method` varchar(255) DEFAULT NULL,
  `is_monthly` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `date`, `start_time`, `end_time`, `num_people`, `total_price`, `status`, `created_at`, `review-email`, `pay_method`, `is_monthly`) VALUES
(347, 9, 12, '2025-04-01', '20:24:33', '00:41:33', 8, 800, 'upcoming', '2025-04-18 22:25:33', 1, NULL, 0),
(348, 9, 12, '2025-03-12', '20:24:33', '00:41:33', 8, 500, 'ongoing', '2025-04-18 22:25:33', 0, NULL, 0),
(349, 9, 12, '2025-04-09', '20:24:33', '00:41:33', 8, 1000, 'canceled', '2025-04-18 22:25:33', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `message` text NOT NULL,
  `opened` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `star` tinyint(4) NOT NULL DEFAULT 0,
  `edited` tinyint(4) NOT NULL DEFAULT 0,
  `file` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`chat_id`, `from_user`, `to_user`, `message`, `opened`, `created_at`, `star`, `edited`, `file`) VALUES
(7, 20, 4, 'hi', 1, '2025-04-19 14:58:27', 0, 0, NULL),
(8, 9, 21, 'How do I contact support?', 0, '2025-04-25 23:37:43', 0, 0, NULL),
(9, 21, 9, 'You can contact support via email at support@example.com or call +123456789.', 1, '2025-04-25 23:37:43', 0, 0, NULL),
(10, 9, 21, 'What are your working hours?', 0, '2025-04-25 23:37:46', 0, 0, NULL),
(11, 21, 9, 'Our working hours are from 9 AM to 6 PM, Monday to Friday.', 1, '2025-04-25 23:37:46', 0, 0, NULL),
(12, 4, 21, 'How can I reset my password?', 1, '2025-05-29 13:24:52', 0, 0, NULL),
(13, 21, 4, 'To reset your password, go to settings and click on \"Forgot Password\".', 1, '2025-05-29 13:24:52', 0, 0, NULL),
(14, 4, 21, 'hii', 1, '2025-05-29 13:24:58', 0, 0, NULL),
(15, 9, 4, 'hi', 1, '2025-05-29 13:27:51', 0, 0, NULL),
(16, 4, 9, 'hhhh', 1, '2025-05-29 13:35:01', 0, 0, NULL),
(17, 4, 9, '', 1, '2025-05-29 13:35:03', 0, 0, NULL),
(18, 4, 9, 'jj', 1, '2025-05-29 13:35:10', 0, 0, NULL),
(19, 4, 9, 'What are your working hours?', 1, '2025-05-29 13:35:21', 0, 0, NULL),
(20, 9, 4, 'Our working hours are from 9 AM to 6 PM, Monday to Friday.', 1, '2025-05-29 13:35:21', 0, 0, NULL),
(21, 9, 4, 'hh', 1, '2025-05-29 13:38:53', 0, 0, NULL),
(22, 4, 9, 'hhh', 1, '2025-05-29 13:39:05', 0, 0, NULL),
(23, 9, 4, 'lllll', 1, '2025-05-29 13:42:14', 0, 0, NULL),
(24, 4, 9, 'hhhhhh', 1, '2025-05-29 13:42:26', 0, 0, NULL),
(25, 9, 4, 'jjjjjjjjjjj', 1, '2025-05-29 13:42:51', 0, 0, NULL),
(26, 4, 9, 'ffffffff', 1, '2025-05-29 13:43:00', 0, 0, NULL),
(29, 9, 4, 'edhgg', 1, '2025-05-29 13:57:33', 0, 1, NULL),
(30, 4, 21, 'hiiiiiiikk', 0, '2025-06-02 04:36:27', 0, 1, NULL),
(32, 4, 21, 'ff', 0, '2025-06-02 04:36:49', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `text` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community`
--

CREATE TABLE `community` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `images` longtext DEFAULT NULL,
  `files` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community`
--

INSERT INTO `community` (`post_id`, `user_id`, `description`, `images`, `files`) VALUES
(17, 4, 'testtt', './img/images.jpg', './files/BIS graduation project format-1 (1).pdf');

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `conversation_id` int(11) NOT NULL,
  `user_1` int(11) NOT NULL,
  `user_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversation`
--

INSERT INTO `conversation` (`conversation_id`, `user_1`, `user_2`) VALUES
(4, 20, 4),
(5, 9, 21),
(6, 4, 21),
(7, 9, 4);

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `user_id` int(11) NOT NULL,
  `workspace_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `like`
--

CREATE TABLE `like` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `like`
--

INSERT INTO `like` (`like_id`, `user_id`, `post_id`) VALUES
(48, 4, 17);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `pay_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `amount` float NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `workspace_id` int(11) NOT NULL,
  `renewal_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`pay_id`, `booking_id`, `amount`, `payment_method`, `transaction_id`, `created_at`, `workspace_id`, `renewal_date`) VALUES
(13, NULL, 1000, 'visa', 32706, '2025-04-26 22:09:02', 13, '0000-00-00'),
(14, NULL, 1000, 'visa', 17668, '2025-04-26 22:10:55', 13, '0000-00-00'),
(15, NULL, 1000, 'visa', 27692, '2025-04-26 22:11:01', 13, '0000-00-00'),
(16, NULL, 1000, 'visa', 38564, '2025-04-26 22:11:52', 13, '0000-00-00'),
(17, NULL, 1000, 'visa', 65844, '2025-04-26 22:14:02', 13, '0000-00-00'),
(18, NULL, 1000, 'visa', 27211, '2025-04-26 22:14:05', 13, '0000-00-00'),
(19, NULL, 1000, 'visa', 92104, '2025-04-26 22:14:17', 13, '0000-00-00'),
(20, NULL, 1000, 'visa', 41045, '2025-04-26 22:18:53', 13, '2025-05-27');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_text` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `booking_id`, `rating`, `review_text`, `created_at`) VALUES
(7, 347, 5, NULL, '2025-04-18 22:26:03');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'Employee'),
(2, 'Company'),
(3, 'Workspace-owner'),
(4, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `workspace_id` int(11) NOT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `seats` int(11) DEFAULT NULL,
  `type_id` int(255) DEFAULT NULL,
  `room_status` varchar(255) DEFAULT NULL,
  `images` longtext NOT NULL,
  `p/hr` int(11) NOT NULL,
  `p/m` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `workspace_id`, `room_name`, `seats`, `type_id`, `room_status`, `images`, `p/hr`, `p/m`) VALUES
(12, 7, 'sharing area', 30, 1, 'active', '', 40, 1000),
(14, 13, 'meeting', 10, 2, '...', 'img/680c0174d54eb.jpeg', 300, 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `type_id` int(11) NOT NULL,
  `type_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`type_id`, `type_name`) VALUES
(1, 'shared area'),
(2, 'private office'),
(3, 'meeting room'),
(4, 'conference room'),
(5, 'event space'),
(6, 'training room');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `image` longtext DEFAULT 'default.png',
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_id` int(11) DEFAULT NULL,
  `last_seen` datetime NOT NULL DEFAULT current_timestamp(),
  `company_name` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `company_type` varchar(255) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `portfolio` longtext DEFAULT NULL,
  `action` varchar(255) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `age`, `job_title`, `image`, `location`, `created_at`, `role_id`, `last_seen`, `company_name`, `contact_info`, `company_type`, `zone_id`, `portfolio`, `action`) VALUES
(4, 'sarahshendy', 'sarahhs707@gmail.com', '01096774388', '$2y$10$oXyh4VndwW3Q34b71jXqDeupMwO5Z9L9hE.GiuHiRuAPrgWXqPy6W', 22, 'web', 'default.png', 'October Gardens', '2025-03-13 23:26:39', 1, '2025-06-02 05:47:24', 'microsoft', 'micrsoftttt@gmail.com, sarah@gmail.com, shendy@gmail.com', 'technological', 4, 'Sarah_Shendy.pdf', 'active'),
(9, 'sarah', 'sarahshendy23@gmail.com', '01096774388', '$2y$10$8fnG73V5n/zlAblDeT0xVORkH/WeQ.DR3zZcHq1XW803cKoXLdbl6', NULL, NULL, 'default.png', NULL, '2025-03-24 20:54:27', 1, '2025-05-29 15:47:05', 'microsoft', 'microsoft@gmail.com', 'technological', NULL, '', 'active'),
(20, 'Omar', 'omarhazemmm87@gmail.com', '01028970103', '$2y$10$FAxUGTDfeEky64g8eHoi2eVpEO7CpnqwhEVXu2meTM5Vvm.ysqQni', NULL, NULL, 'default.png', NULL, '2025-04-18 21:24:42', 4, '2025-04-19 14:58:48', NULL, NULL, NULL, NULL, NULL, 'active'),
(21, 'salma', 'salmaa.mohamedd56@gmail.com', '01028970103', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-04-18 21:39:08', 4, '2025-04-24 22:46:34', NULL, NULL, NULL, NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `workspaces`
--

CREATE TABLE `workspaces` (
  `workspace_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `price/hr` float DEFAULT NULL,
  `zone_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Availability` tinyint(4) NOT NULL DEFAULT 1,
  `latitude` float(10,6) NOT NULL,
  `longitude` float(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workspaces`
--

INSERT INTO `workspaces` (`workspace_id`, `user_id`, `name`, `location`, `description`, `price/hr`, `zone_id`, `created_at`, `Availability`, `latitude`, `longitude`) VALUES
(7, 9, 'creativo', 'dokki-mesaha square', 'co-working space', 200, 5, '2025-03-24 21:33:44', 2, 30.037949, 31.208260),
(10, 21, 'creativo 2', 'dokki-mesaha square', 'co-working space', 200, 5, '2025-03-24 21:33:44', 2, 0.000000, 0.000000),
(11, 9, 'creativo 3', 'dokki-mesaha square', 'co-working space', 200, 5, '2025-03-24 21:33:44', 2, 0.000000, 0.000000),
(13, 9, 'test workspaces', 'giza', 'jhkjjljkjfytdgfdghfhj', 70, 9, '2025-04-25 21:41:08', 1, 0.000000, 0.000000);

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `zone_id` int(11) NOT NULL,
  `zone_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`zone_id`, `zone_name`) VALUES
(4, 'Cairo'),
(5, 'Giza'),
(6, 'Alexandria'),
(7, 'Port Said'),
(8, 'Suez'),
(9, 'Luxor'),
(10, 'Aswan'),
(11, 'Mansoura'),
(12, 'Tanta'),
(13, 'Ismailia'),
(14, 'Fayoum'),
(15, 'Zagazig'),
(16, 'Damietta'),
(17, 'Sohag'),
(18, 'Beni Suef'),
(19, 'Minya'),
(20, 'Qena'),
(21, 'Asyut'),
(22, 'Sharm El Sheikh'),
(23, 'Hurghada');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenitiy_id`),
  ADD UNIQUE KEY `unique_room_amenity` (`room_id`,`amenity`) USING HASH,
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `automated_replies`
--
ALTER TABLE `automated_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `workspace_id` (`room_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `from_user` (`from_user`),
  ADD KEY `to_user` (`to_user`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `community`
--
ALTER TABLE `community`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`conversation_id`),
  ADD KEY `user_1` (`user_1`),
  ADD KEY `user_2` (`user_2`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`user_id`,`workspace_id`),
  ADD KEY `user_id` (`user_id`,`workspace_id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- Indexes for table `like`
--
ALTER TABLE `like`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `workspace_id` (`workspace_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `workspace_id` (`workspace_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD PRIMARY KEY (`workspace_id`),
  ADD KEY `owner_id` (`user_id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`zone_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenitiy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=350;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `community`
--
ALTER TABLE `community`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `like`
--
ALTER TABLE `like`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `workspace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `amenities`
--
ALTER TABLE `amenities`
  ADD CONSTRAINT `amenities_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`from_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`to_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `community`
--
ALTER TABLE `community`
  ADD CONSTRAINT `community_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`user_1`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`user_2`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favourite`
--
ALTER TABLE `favourite`
  ADD CONSTRAINT `favourite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `favourite_ibfk_2` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`workspace_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `like`
--
ALTER TABLE `like`
  ADD CONSTRAINT `like_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `like_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`workspace_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`workspace_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `room_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD CONSTRAINT `workspaces_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workspaces_ibfk_2` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
