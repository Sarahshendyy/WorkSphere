-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 12:05 AM
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
(13, 'Covered Parking', NULL),
(53, 'Prayer Room', 21),
(54, 'Air Conditioning', 21),
(55, 'Projector', 21),
(56, 'On-weekends', 21),
(57, 'Free Coffee', 21),
(58, 'Air Conditioning', 22),
(59, 'Free Coffee', 22),
(60, 'Lockers', 23),
(61, 'Prayer Room', 23),
(62, 'Air Conditioning', 23),
(63, 'Cafetria', 23),
(64, 'Covered Parking', 23),
(65, 'Lockers', 24),
(66, 'Prayer Room', 24),
(67, 'Air Conditioning', 24),
(68, 'Cafetria', 24),
(69, 'Free Coffee', 24),
(70, 'Air Conditioning', 25),
(71, 'Outdoor View', 25),
(72, 'Free Coffee', 25),
(73, 'Lockers', 26),
(74, 'Prayer Room', 26),
(75, 'Air Conditioning', 26),
(76, 'Outdoor View', 26),
(77, 'Projector', 26),
(78, 'Cafetria', 26),
(79, 'Free Coffee', 26),
(80, 'Lockers', 27),
(81, 'Air Conditioning', 27),
(82, 'Projector', 27),
(83, 'Cafetria', 27),
(84, 'Air Conditioning', 32),
(85, 'Projector', 32),
(86, 'Free Coffee', 32),
(87, 'Lockers', 31),
(88, 'Prayer Room', 31),
(89, 'Air Conditioning', 31),
(90, 'Cafetria', 31),
(91, 'Air Conditioning', 33),
(92, 'Outdoor View', 33),
(93, 'Projector', 33),
(94, 'Cafetria', 33),
(95, 'Prayer Room', 37),
(96, 'Air Conditioning', 37),
(97, 'Projector', 37),
(98, 'Free Coffee', 37),
(99, 'Lockers', 36),
(100, 'Air Conditioning', 36),
(101, 'Projector', 36),
(102, 'Cafetria', 36),
(103, 'Air Conditioning', 35),
(104, 'Projector', 35),
(105, 'Free Coffee', 35),
(106, 'Lockers', 34),
(107, 'Air Conditioning', 34),
(108, 'Outdoor View', 34),
(109, 'Lockers', 40),
(110, 'Prayer Room', 40),
(111, 'Air Conditioning', 40),
(112, 'Projector', 40),
(113, 'Cafetria', 40),
(114, 'Lockers', 39),
(115, 'Air Conditioning', 39),
(116, 'Outdoor View', 39),
(117, 'Free Coffee', 39),
(118, 'Lockers', 38),
(119, 'Prayer Room', 38),
(120, 'Air Conditioning', 38),
(121, 'Projector', 38),
(122, 'Cafetria', 38),
(123, 'Free Coffee', 38),
(124, 'Standing Desks', 38),
(131, 'Air Conditioning', 42),
(132, 'Projector', 42),
(133, 'On-weekends', 42),
(134, 'Free Coffee', 42),
(135, 'Covered Parking', 42),
(136, 'WiFi', 42),
(137, 'Lockers', 43),
(138, 'Prayer Room', 43),
(139, 'Outdoor View', 43),
(140, 'Cafetria', 43),
(141, 'Standing Desks', 43),
(142, 'WiFi', 43),
(143, 'Lockers', 44),
(144, 'Air Conditioning', 44),
(145, 'Outdoor View', 44),
(146, 'On-weekends', 44),
(147, 'WiFi', 44),
(148, 'Air Conditioning', 45),
(149, 'Projector', 45),
(150, 'Cafetria', 45),
(151, 'On-weekends', 45),
(152, 'Free Coffee', 45),
(153, 'WiFi', 45),
(154, 'Lockers', 46),
(155, 'Outdoor View', 46),
(156, 'Projector', 46),
(157, 'Free Coffee', 46),
(158, 'WiFi', 46),
(159, 'Lockers', 47),
(160, 'Prayer Room', 47),
(161, 'Air Conditioning', 47),
(162, 'Cafetria', 47),
(163, 'Standing Desks', 47),
(164, 'WiFi', 47),
(165, 'Lockers', 48),
(166, 'Prayer Room', 48),
(167, 'Air Conditioning', 48),
(168, 'Projector', 48),
(169, 'Cafetria', 48),
(170, 'On-weekends', 48),
(171, 'WiFi', 48),
(172, 'Lockers', 49),
(173, 'Projector', 49),
(174, 'Covered Parking', 49),
(175, 'WiFi', 49),
(176, 'Lockers', 50),
(177, 'Prayer Room', 50),
(178, 'Air Conditioning', 50),
(179, 'Cafetria', 50),
(180, 'Free Coffee', 50),
(181, 'WiFi', 50),
(182, 'Lockers', 51),
(183, 'Outdoor View', 51),
(184, 'Free Coffee', 51),
(185, 'WiFi', 51);

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
(1, 'How do I book a workspace?', 'Go to Workspaces page, select your preferred workspace, choose a room, select date/time, and complete payment.', '2025-06-12 21:00:00'),
(2, 'What payment methods do you accept?', 'We accept online payments via credit/debit cards or you can choose to pay at the workspace when you arrive.', '2025-06-12 21:00:00'),
(3, 'Can I cancel my booking?', 'Yes, you can cancel bookings from your My Bookings page. Cancellation policies vary by workspace.', '2025-06-12 21:00:00'),
(4, 'What if I encounter a technical issue?', 'Try refreshing the app or logging out/in. If the problem persists, contact support via the Chat feature.', '2025-06-12 21:00:00'),
(5, 'What are your working hours?', 'Our working hours are from 9 AM to 6 PM, Monday to Friday.', '2025-04-03 20:39:04');

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
(352, 32, 25, '2025-06-18', '09:00:00', '10:00:00', 1, 500, 'upcoming', '2025-06-12 21:02:51', 0, 'Pay at Host', 0),
(353, 32, 36, '2025-06-28', '09:00:00', '10:00:00', 4, 200, 'upcoming', '2025-06-12 21:17:50', 0, 'Online', 0),
(354, 32, 46, '2025-06-28', '09:00:00', '10:00:00', 3, 200, 'upcoming', '2025-06-12 22:03:30', 0, 'Pay at Host', 0);

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
(18, 4, '\"Hi everyone! I\'m Sarah, a freelance graphic designer with over 6 years of experience helping businesses build a strong visual identity. I specialize in logo design, branding packages, and social media graphics. I\'m currently taking on new clients. Let\'s make your brand stand out! You can see some of my recent work in the attached portfolio.\"', NULL, './files/Sarah_Shendy.pdf'),
(23, 31, '\"Hello WorkSphere community! My name is Ahmed, and I\'m a professional content writer and copywriter. If you\'re struggling to find the right words for your website, blog, or marketing materials, I can create compelling content that connects with your audience. I have experience in the tech, finance, and travel industries. Feel free to message me to discuss your needs!\"', './img/comm.png', NULL),
(24, 44, '\"Our team at \'Innovate Solutions\' is looking for a creative and data-driven Social Media Manager to handle our online presence. This is a remote, part-time position (approx. 15 hours/week). We need someone who understands the B2B tech space. If you\'re passionate about growing a brand online, please send me a message with your portfolio or links to accounts you\'ve managed.\"', './img/community.png', NULL),
(25, 50, '\"Hi all, I\'m looking to hire a freelance web developer to build a new e-commerce website for my local gift shop. The project requires experience with Shopify or WooCommerce. Please comment below or message me with a link to your portfolio and your estimated rates if you are interested. Thanks!\"', NULL, NULL),
(26, 40, '\"Hey developers and designers! I\'m a frontend developer working on a personal project—a simple productivity app. I\'m looking for a UI/UX designer who might be interested in collaborating to build a great-looking portfolio piece. This is currently an unpaid collaboration, but a great opportunity to create something cool together. Let me know if you\'re interested!\"', './img/image-6.png', NULL);

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
(50, 32, 25),
(51, 32, 26),
(52, 32, 24),
(53, 32, 23),
(54, 32, 18);

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
(23, 352, 500, 'Pay at Host', 59497, '2025-06-12 21:10:21', 18, '0000-00-00'),
(24, 353, 200, 'Online', 22665, '2025-06-12 21:19:19', 21, '0000-00-00'),
(25, 354, 200, 'Pay at Host', 95595, '2025-06-12 22:03:53', 25, '0000-00-00');

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
(18, 16, 'The Focus Room', 8, 3, NULL, 'DSC03719-min-1.jpg,380230-Coworking 1.jpg,169744-Coworking Space (1).jpg', 250, 0),
(19, 16, 'The Idea Lab', 4, 1, 'available', '684a2d1a3d84c.jpeg,684a2d1a3da75.jpg', 300, 0),
(20, 16, 'The Vision Room', 7, 1, 'available', '684a2da8f16c6.png,684a2da8f1a72.png', 250, 0),
(21, 17, 'The Quiet Zone', 3, 2, NULL, 'room_684a313cd49c65.97134686.jpg,room_684a313cd4bb96.76150215.jpg', 300, 0),
(22, 17, 'ThinkTank', 10, 4, 'available', '684a3269ef8f0.jpg,684a3269efb5f.jpg', 500, 0),
(23, 17, 'The Innovation Bay', 6, 2, 'available', '684a32e189964.webp,684a32e189be7.jpg', 600, 0),
(24, 18, 'The Innovation Bay', 15, 1, NULL, 'room_684a36bc095f60.20739998.jpeg,room_684a36bc098148.97379520.jpg', 500, 0),
(25, 18, 'Skyline Room', 3, 2, NULL, 'room_684a36bc09fc06.97936258.jpg,room_684a36bc0a27f5.77411007.jpg', 500, 0),
(26, 18, 'TeamNest', 20, 6, NULL, 'room_684a36bc0a9e28.63452989.jpg,room_684a36bc0ac3d9.55329656.jpg', 700, 0),
(27, 18, 'Connect Corner', 10, 4, NULL, 'room_684a36bc0b1d16.44659182.jpg,room_684a36bc0b3ea7.50696793.jpg', 650, 0),
(28, 19, 'TaskPod', 6, 3, NULL, 'room_684a3b743a6652.71855222.jpg,room_684a3b743a82f6.00693091.jpg', 450, 0),
(29, 19, 'Worknestia', 15, 1, NULL, 'room_684a3b743b0629.58490987.jpg,room_684a3b743b29c7.17918910.webp', 450, 0),
(30, 19, 'ProjectDen', 10, 4, NULL, 'room_684a3b743b93d9.78314982.jpg,room_684a3b743bb072.18216354.webp', 500, 0),
(31, 20, 'The Idea Lab', 6, 1, NULL, 'room_684ad630d21c17.02435137.webp', 250, 0),
(32, 20, 'Brainstorm Bay', 11, 4, NULL, 'room_684ad630d2d8f7.60645393.jpg,room_684ad630d304b1.10810361.jpg', 200, 0),
(33, 20, 'The Nest', 20, 6, NULL, 'room_684ad630d40c55.56664176.jpg', 200, 0),
(34, 21, 'The Algorithm Room', 4, 2, NULL, 'room_684ada4da5dad4.55739322.jpg,room_684ada4da603f5.76631052.jpg', 350, 0),
(35, 21, 'Nova Space', 6, 3, NULL, 'room_684ada4da6c3a5.24432342.jpg,room_684ada4da72914.40918157.jpg', 200, 0),
(36, 21, 'Sketch Room', 20, 1, NULL, 'room_684ada4da94324.59483113.jpg,room_684ada4da98eb1.70380879.jpeg', 200, 0),
(37, 21, 'The Studio', 12, 4, NULL, 'room_684ada4daa32b4.30350613.webp', 230, 0),
(38, 22, 'The Matrix', 8, 3, NULL, 'room_684aded0362f44.73539868.jpg,room_684aded0367310.66804595.jpg', 270, 0),
(39, 22, 'Quantum Hub', 4, 2, NULL, 'room_684aded0375074.22670304.jpg', 300, 0),
(40, 22, 'Techtonic Room', 10, 1, NULL, 'room_684aded038a3c0.83736945.jpg,room_684aded038ef73.43651241.jpg', 250, 0),
(42, 24, 'Pixel Room', 8, 3, NULL, 'room_684b28c34afee5.43044900.jpg,room_684b28c34b4566.78850879.jpg', 250, 0),
(43, 24, 'Data Dock', 15, 1, 'available', '684b28fed07be.jpg,684b28fed10bd.jpg', 200, 0),
(44, 24, 'Techtonic Room', 6, 2, 'available', '684b2947990e6.jpg,684b29479974a.jpg', 200, 0),
(45, 24, 'Nova Space', 30, 6, 'available', '684b29a9164c8.jpg,684b29a916d56.jpg', 300, 0),
(46, 25, 'Creative Core', 8, 4, NULL, 'room_684b2de4a62253.40014458.jpg,room_684b2de4a66f43.63547305.jpg', 200, 0),
(47, 25, 'Strategy Studio', 4, 2, 'available', '684b2e61211e3.jpg,684b2e6121921.jpg', 250, 0),
(48, 25, 'The Idea Lab', 20, 1, 'available', '684b2ebbc46cc.jpg,684b2ebbc51c2.jpg', 300, 0),
(49, 26, 'Execution Hub', 10, 4, NULL, 'room_684b310181c3e2.94740243.jpg,room_684b3101821c68.72237681.jpg', 200, 0),
(50, 26, 'Cedar Room', 10, 1, 'available', '684b3191a3cfc.jpg,684b3191a42d5.jpg', 300, 0),
(51, 26, 'Circuit Studio', 10, 2, 'available', '684b3221b79ad.jpg,684b3221b8171.jpg', 300, 0);

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
(4, 'sarahshendy', 'sarahhs707@gmail.com', '01096774388', '$2y$10$oXyh4VndwW3Q34b71jXqDeupMwO5Z9L9hE.GiuHiRuAPrgWXqPy6W', 22, 'web', 'default.png', 'October Gardens', '2025-03-13 23:26:39', 3, '2025-06-02 05:47:24', 'microsoft', 'micrsoftttt@gmail.com, sarah@gmail.com, shendy@gmail.com', 'technological', 4, 'Sarah_Shendy.pdf', 'active'),
(9, 'sarah', 'sarahshendy32@gmail.com', '01096774388', '$2y$10$8fnG73V5n/zlAblDeT0xVORkH/WeQ.DR3zZcHq1XW803cKoXLdbl6', NULL, NULL, 'default.png', NULL, '2025-03-24 20:54:27', 1, '2025-05-29 15:47:05', 'microsoft', 'microsoft@gmail.com', 'technological', NULL, '', 'active'),
(20, 'Omar', 'omarhazemmm87@gmail.com', '01028970103', '$2y$10$FAxUGTDfeEky64g8eHoi2eVpEO7CpnqwhEVXu2meTM5Vvm.ysqQni', NULL, NULL, 'default.png', NULL, '2025-04-18 21:24:42', 2, '2025-04-19 14:58:48', NULL, NULL, NULL, NULL, NULL, 'active'),
(21, 'salma', 'salmaa.mohamedd56@gmail.com', '01028970103', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-04-18 21:39:08', 3, '2025-04-24 22:46:34', NULL, NULL, NULL, NULL, NULL, 'active'),
(22, 'samsoma', 'samsoma@gmail.com', '34243', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-11 23:59:29', 3, '2025-06-12 02:59:29', NULL, NULL, NULL, NULL, NULL, 'active'),
(23, 'ko', 'ko@gmail.com', 'w223w', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 00:14:57', 3, '2025-06-12 03:14:57', NULL, NULL, NULL, NULL, NULL, 'active'),
(24, 'Sara Ahmed', 'saraahmed123@gmail.com', '01098991339', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 00:26:39', 3, '2025-06-12 23:35:32', NULL, NULL, NULL, NULL, NULL, 'active'),
(25, 'Muhamed Ahmed', 'muhameddd34@outlook.com', '01126749751', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 01:33:47', 3, '2025-06-12 04:33:47', NULL, NULL, NULL, NULL, NULL, 'active'),
(26, 'Alaa Gamil', 'Alaagamill56@gmail.com', '01275990021', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 01:55:37', 3, '2025-06-12 04:55:37', NULL, NULL, NULL, NULL, NULL, 'active'),
(27, 'Ahmed Samy', 'Ahmedsamy1970@gmail.com', '01145667831', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 02:16:47', 3, '2025-06-12 05:16:47', NULL, NULL, NULL, NULL, NULL, 'active'),
(28, 'Karim Tamer', 'Karimttamer99@gmail.com', '01087774890', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 13:09:34', 3, '2025-06-12 16:09:34', NULL, NULL, NULL, NULL, NULL, 'active'),
(29, 'Youssef Hany', 'YoussefHH1@gmail.com', '01236789560', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 13:33:50', 3, '2025-06-12 16:33:50', NULL, NULL, NULL, NULL, NULL, 'active'),
(30, 'Omar Fathy', 'OmarFathyy67@gmail.com', '01087889061', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', NULL, NULL, 'default.png', NULL, '2025-06-12 13:53:06', 3, '2025-06-12 16:53:06', NULL, NULL, NULL, NULL, NULL, 'active'),
(31, 'Ahmed Samir', 'ahmed.samir.employee@example.com', '01011223344', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 28, 'Web Developer', 'default.png', 'Cairo', '2025-06-12 14:00:00', 1, '2025-06-12 20:00:00', NULL, NULL, NULL, 4, NULL, 'active'),
(32, 'Mona Adel', 'mona.adel.employee@example.com', '01022334455', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 25, 'Graphic Designer', 'default.png', 'Alexandria', '2025-06-12 14:01:00', 1, '2025-06-12 20:01:00', NULL, NULL, NULL, 5, NULL, 'active'),
(33, 'Omar Tarek', 'omar.tarek.employee@example.com', '01033445566', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 30, 'Marketing Specialist', 'default.png', 'Giza', '2025-06-12 14:02:00', 1, '2025-06-12 20:02:00', NULL, NULL, NULL, 6, NULL, 'active'),
(34, 'Yara Hossam', 'yara.hossam.employee@example.com', '01044556677', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 27, 'Accountant', 'default.png', 'Nasr City', '2025-06-12 14:03:00', 1, '2025-06-12 20:03:00', NULL, NULL, NULL, 4, NULL, 'active'),
(35, 'Karim Wael', 'karim.wael.employee@example.com', '01055667788', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 32, 'Sales Executive', 'default.png', 'Heliopolis', '2025-06-12 14:04:00', 1, '2025-06-12 20:04:00', NULL, NULL, NULL, 5, NULL, 'active'),
(36, 'Dina Sherif', 'dina.sherif.employee@example.com', '01066778899', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 29, 'HR Coordinator', 'default.png', 'Maadi', '2025-06-12 14:05:00', 1, '2025-06-12 20:05:00', NULL, NULL, NULL, 6, NULL, 'active'),
(37, 'Amr Nader', 'amr.nader.employee@example.com', '01077889900', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'IT Support', 'default.png', '6th of October', '2025-06-12 14:06:00', 1, '2025-06-12 20:06:00', NULL, NULL, NULL, 4, NULL, 'active'),
(38, 'Nourhan Karim', 'nourhan.karim.employee@example.com', '01088990011', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 26, 'Content Writer', 'default.png', 'Zamalek', '2025-06-12 14:07:00', 1, '2025-06-12 20:07:00', NULL, NULL, NULL, 5, NULL, 'active'),
(39, 'Hossam Ali', 'hossam.ali.employee@example.com', '01099001122', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 33, 'Project Manager', 'default.png', 'Dokki', '2025-06-12 14:08:00', 1, '2025-06-12 20:08:00', NULL, NULL, NULL, 6, NULL, 'active'),
(40, 'Farah Mohamed', 'farah.mohamed.employee@example.com', '01000112233', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 24, 'UI/UX Designer', 'default.png', 'New Cairo', '2025-06-12 14:09:00', 1, '2025-06-12 20:09:00', NULL, NULL, NULL, 4, NULL, 'active'),
(41, 'Mahmoud Fawzy', 'mahmoud.fawzy.company@example.com', '01112233445', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 42, 'HR Manager', 'default.png', 'Cairo', '2025-06-12 14:10:00', 2, '2025-06-12 20:10:00', 'Vodafone Egypt', 'hr@vodafone.com, careers@vodafone.com', 'Telecommunications', 5, 'Sarah_Shendy.pdf', 'active'),
(42, 'Lina Magdy', 'lina.magdy.company@example.com', '01123344556', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 35, 'Recruitment Specialist', 'default.png', 'Alexandria', '2025-06-12 14:11:00', 2, '2025-06-12 20:11:00', 'Orange Egypt', 'recruitment@orange.eg, hr@orange.eg', 'Telecommunications', 6, 'Sarah_Shendy.pdf', 'active'),
(43, 'Waleed Ashraf', 'waleed.ashraf.company@example.com', '01134455667', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 39, 'Talent Acquisition', 'default.png', 'Giza', '2025-06-12 14:12:00', 2, '2025-06-12 20:12:00', 'Etisalat Egypt', 'careers@etisalat.com, hr@etisalat.com', 'Telecommunications', 4, 'Sarah_Shendy.pdf', 'active'),
(44, 'Nourhan Samy', 'nourhan.samy.company@example.com', '01145566778', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'HR Business Partner', 'default.png', 'Nasr City', '2025-06-12 14:13:00', 2, '2025-06-12 20:13:00', 'Amazon Egypt', 'egypt-hr@amazon.com, careers@amazon.eg', 'E-commerce', 5, 'Sarah_Shendy.pdf', 'active'),
(45, 'Adel Hatem', 'adel.hatem.company@example.com', '01156677889', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 45, 'Head of HR', 'default.png', 'Heliopolis', '2025-06-12 14:14:00', 2, '2025-06-12 20:14:00', 'IBM Egypt', 'hr-egypt@ibm.com, careers@ibm.eg', 'Technology', 6, 'Sarah_Shendy.pdf', 'active'),
(46, 'Dina Gamal', 'dina.gamal.company@example.com', '01167788990', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 37, 'Talent Manager', 'default.png', 'Maadi', '2025-06-12 14:15:00', 2, '2025-06-12 20:15:00', 'Microsoft Egypt', 'egypt-careers@microsoft.com, hr@microsoft.eg', 'Technology', 4, 'Sarah_Shendy.pdf', 'active'),
(47, 'Khaled Omar', 'khaled.omar.company@example.com', '01178899001', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 40, 'HR Director', 'default.png', '6th of October', '2025-06-12 14:16:00', 2, '2025-06-12 20:16:00', 'Siemens Egypt', 'hr.egypt@siemens.com, careers@siemens.eg', 'Engineering', 5, 'Sarah_Shendy.pdf', 'active'),
(48, 'Farida Nasser', 'farida.nasser.company@example.com', '01189900112', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 34, 'Recruitment Lead', 'default.png', 'Zamalek', '2025-06-12 14:17:00', 2, '2025-06-12 20:17:00', 'Nestle Egypt', 'careers-egypt@nestle.com, hr@nestle.eg', 'Food & Beverage', 6, 'Sarah_Shendy.pdf', 'active'),
(49, 'Sherif Adel', 'sherif.adel.company@example.com', '01190011223', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 43, 'HR Operations Manager', 'default.png', 'Dokki', '2025-06-12 14:18:00', 2, '2025-06-12 20:18:00', 'PepsiCo Egypt', 'hr.egypt@pepsico.com, careers@pepsico.eg', 'Food & Beverage', 4, 'Sarah_Shendy.pdf', 'active'),
(50, 'Mona Tarek', 'mona.tarek.company@example.com', '01100112234', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 36, 'Learning & Development', 'default.png', 'New Cairo', '2025-06-12 14:19:00', 2, '2025-06-12 20:19:00', 'Unilever Egypt', 'egypt.hr@unilever.com, careers@unilever.eg', 'Consumer Goods', 5, 'Sarah_Shendy.pdf', 'active'),
(51, 'Sarah Shendy', 'sarahshendy23@gmail.com', '01112233445', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 28, 'Website Admin', 'default.png', 'Cairo', '2025-06-12 14:10:00', 4, '2025-06-12 20:10:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(52, 'Salma Mohamed', 'salmaa.mohamedd65@gmail.com', '01123344556', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 32, 'Admin Manager', 'default.png', 'Alexandria', '2025-06-12 14:11:00', 4, '2025-06-12 20:11:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(53, 'Maryam Kamel', 'maryamkamel488@gmail.com', '01134455667', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 30, 'Business Admin', 'default.png', 'Giza', '2025-06-12 14:12:00', 4, '2025-06-12 20:12:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(54, 'Sama Shaheen', 'samashaheenn776@gmail.com', '01145566778', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 29, 'Database Admin', 'default.png', 'Nasr City', '2025-06-12 14:13:00', 4, '2025-06-12 20:13:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(55, 'Shahd Amr', 'shahhddamrr@gmail.com', '01156677889', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'Security Admin', 'default.png', 'Heliopolis', '2025-06-12 14:14:00', 4, '2025-06-12 20:14:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(56, 'Roua Ashraf', 'rouaashraff@gmail.com', '01167788990', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 27, 'System Admin', 'default.png', 'Maadi', '2025-06-12 14:15:00', 4, '2025-06-12 20:15:00', NULL, NULL, NULL, NULL, NULL, 'active'),
(57, 'Ahmed Hassan', 'ahmed.hassan.workspace@example.com', '01112233446', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 32, 'Workspace Owner', 'default.png', 'Cairo', '2025-06-12 14:16:00', 3, '2025-06-12 20:16:00', NULL, NULL, NULL, 4, NULL, 'active'),
(58, 'Mariam Adel', 'mariam.adel.workspace@example.com', '01123344557', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 29, 'Workspace Owner', 'default.png', 'Alexandria', '2025-06-12 14:17:00', 3, '2025-06-12 20:17:00', NULL, NULL, NULL, 5, NULL, 'active'),
(59, 'Omar Khaled', 'omar.khaled.workspace@example.com', '01134455668', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 35, 'Workspace Owner', 'default.png', 'Giza', '2025-06-12 14:18:00', 3, '2025-06-12 20:18:00', NULL, NULL, NULL, 4, NULL, 'active'),
(60, 'Nada Samir', 'nada.samir.workspace@example.com', '01145566779', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'Workspace Owner', 'default.png', 'Nasr City', '2025-06-12 14:19:00', 3, '2025-06-12 20:19:00', NULL, NULL, NULL, 5, NULL, 'active'),
(61, 'Karim Mahmoud', 'karim.mahmoud.workspace@example.com', '01156677880', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 28, 'Workspace Owner', 'default.png', 'Heliopolis', '2025-06-12 14:20:00', 3, '2025-06-12 20:20:00', NULL, NULL, NULL, 4, NULL, 'active'),
(62, 'Yara Wael', 'yara.wael.workspace@example.com', '01167788991', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 33, 'Workspace Owner', 'default.png', 'Maadi', '2025-06-12 14:21:00', 3, '2025-06-12 20:21:00', NULL, NULL, NULL, 5, NULL, 'active'),
(63, 'Tarek Fouad', 'tarek.fouad.workspace@example.com', '01178899002', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 37, 'Workspace Owner', 'default.png', '6th of October', '2025-06-12 14:22:00', 3, '2025-06-12 20:22:00', NULL, NULL, NULL, 4, NULL, 'active'),
(64, 'Dalia Nader', 'dalia.nader.workspace@example.com', '01189900113', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 30, 'Workspace Owner', 'default.png', 'Zamalek', '2025-06-12 14:23:00', 3, '2025-06-12 20:23:00', NULL, NULL, NULL, 5, NULL, 'active'),
(65, 'Hazem Ashraf', 'hazem.ashraf.workspace@example.com', '01190011224', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 34, 'Workspace Owner', 'default.png', 'Dokki', '2025-06-12 14:24:00', 3, '2025-06-12 20:24:00', NULL, NULL, NULL, 4, NULL, 'active'),
(66, 'Farah Tamer', 'farah.tamer.workspace@example.com', '01100112235', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 29, 'Workspace Owner', 'default.png', 'New Cairo', '2025-06-12 14:25:00', 3, '2025-06-12 20:25:00', NULL, NULL, NULL, 5, NULL, 'active'),
(67, 'Amr Samy', 'amr.samy.workspace@example.com', '01111223346', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'Workspace Owner', 'default.png', 'Sheikh Zayed', '2025-06-12 14:26:00', 3, '2025-06-12 20:26:00', NULL, NULL, NULL, 4, NULL, 'active'),
(68, 'Lina Fawzy', 'lina.fawzy.workspace@example.com', '01122334457', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 27, 'Workspace Owner', 'default.png', 'Madinaty', '2025-06-12 14:27:00', 3, '2025-06-12 20:27:00', NULL, NULL, NULL, 5, NULL, 'active'),
(69, 'Waleed Gamal', 'waleed.gamal.workspace@example.com', '01133445568', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 36, 'Workspace Owner', 'default.png', 'Rehab', '2025-06-12 14:28:00', 3, '2025-06-12 20:28:00', NULL, NULL, NULL, 4, NULL, 'active'),
(70, 'Nourhan Karim', 'nourhan.karim.workspace@example.com', '01144556679', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 33, 'Workspace Owner', 'default.png', '5th Settlement', '2025-06-12 14:29:00', 3, '2025-06-12 20:29:00', NULL, NULL, NULL, 5, NULL, 'active'),
(71, 'Adel Nasser', 'adel.nasser.workspace@example.com', '01155667780', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 38, 'Workspace Owner', 'default.png', 'Tagamoa', '2025-06-12 14:30:00', 3, '2025-06-12 20:30:00', NULL, NULL, NULL, 4, NULL, 'active'),
(72, 'Dina Hatem', 'dina.hatem.workspace@example.com', '01166778891', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 30, 'Workspace Owner', 'default.png', 'Badr City', '2025-06-12 14:31:00', 3, '2025-06-12 20:31:00', NULL, NULL, NULL, 5, NULL, 'active'),
(73, 'Khaled Samir', 'khaled.samir.workspace@example.com', '01177889902', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 35, 'Workspace Owner', 'default.png', 'Shorouk', '2025-06-12 14:32:00', 3, '2025-06-12 20:32:00', NULL, NULL, NULL, 4, NULL, 'active'),
(74, 'Farida Wael', 'farida.wael.workspace@example.com', '01188990013', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 32, 'Workspace Owner', 'default.png', 'Obour', '2025-06-12 14:33:00', 3, '2025-06-12 20:33:00', NULL, NULL, NULL, 5, NULL, 'active'),
(75, 'Sherif Adel', 'sherif.adel.workspace@example.com', '01199001124', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 34, 'Workspace Owner', 'default.png', 'Mostakbal City', '2025-06-12 14:34:00', 3, '2025-06-12 20:34:00', NULL, NULL, NULL, 4, NULL, 'active'),
(76, 'Mona Karam', 'mona.karam.workspace@example.com', '01100112236', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 29, 'Workspace Owner', 'default.png', 'Katameya', '2025-06-12 14:35:00', 3, '2025-06-12 20:35:00', NULL, NULL, NULL, 5, NULL, 'active'),
(77, 'Hossam Ali', 'hossam.ali.workspace@example.com', '01111223347', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 37, 'Workspace Owner', 'default.png', 'New Capital', '2025-06-12 14:36:00', 3, '2025-06-12 20:36:00', NULL, NULL, NULL, 4, NULL, 'active'),
(78, 'Rana Tarek', 'rana.tarek.workspace@example.com', '01122334458', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 31, 'Workspace Owner', 'default.png', 'Alamein', '2025-06-12 14:37:00', 3, '2025-06-12 20:37:00', NULL, NULL, NULL, 5, NULL, 'active'),
(79, 'Ziad Omar', 'ziad.omar.workspace@example.com', '01133445569', '$2y$10$Zip4Ppx9MOwdDkwGOijrw.C7nanDdXqjBwKdx6IaAjCG.JcKmlvZ.', 33, 'Workspace Owner', 'default.png', 'North Coast', '2025-06-12 14:38:00', 3, '2025-06-12 20:38:00', NULL, NULL, NULL, 4, NULL, 'active');

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
(16, 24, 'Ice Cairo', 'In the heart of Downtown Cairo near Talaat Harb Square.', 'Your productivity oasis in the heart of the city modern, quiet, and inspiring.', 100, 4, '2025-06-12 00:31:50', 2, 30.000000, 31.239433),
(17, 25, 'The District', '18 Nasr Street, Maadi, Cairo, Egypt', 'Designed for thinkers, built for doers your ideal workspace awaits.', 190, 4, '2025-06-12 01:45:32', 2, 30.000000, 31.224239),
(18, 26, 'Innoventures Startup Circus', '124 Othman Ibn Affan Street, Third Floor, Heliopolis, Cairo, Egypt.', 'A creative corner where your ideas come alive fast Wi-Fi, coffee, and inspiration included.', 150, 4, '2025-06-12 02:09:00', 2, 30.000000, 31.349039),
(19, 27, 'Khanspace', '15 El Nasr Street in Rabaa Al Adawiyah, Nasr City, 8th Floor.', 'Private desks, bold vibes, and a community that hustles all in one space.', 120, 4, '2025-06-12 02:29:08', 2, 30.000000, 31.324184),
(20, 28, 'Makanak', '11 Ezbet Fahmy, Maadi, ', 'Say goodbye to boring offices. Say hello to your dream space.', 250, 4, '2025-06-12 13:29:20', 2, 29.000000, 31.282501),
(21, 29, 'KAPITALIZE', 'Tower 12 (over EG bank) , Bavaria Town, Ring Road', 'The office of tomorrow, available today.', 125, 4, '2025-06-12 13:46:53', 2, 29.000000, 31.340849),
(22, 30, 'The B-Hub', '16 El Khartoum St, Almazah, Heliopolis', 'Professional vibes with creative freedom.', 250, 4, '2025-06-12 14:06:08', 2, 30.000000, 31.332003),
(24, 57, 'Hive Space', '7 Mekhael Abadeir Street Roushdy', 'Workspace that makes Mondays feel like Fridays.', 200, 6, '2025-06-12 19:21:39', 2, 31.000000, 10000.000000),
(25, 58, 'Flux', '8 Al Azaa, Glim', 'Plug in. Power up. Make things happen.', 300, 6, '2025-06-12 19:43:32', 2, 31.000000, 10000.000000),
(26, 59, 'Business station', 'كوبرى ٤٥ العلوى, Miami, Alexandria ٣١٥ شارع جمال عبد الناصر, بحرى أسفل', 'Focus-friendly, team-ready spaces.\r\n\r\n', 250, 6, '2025-06-12 19:56:49', 2, 31.000000, 10000.000000);

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
  MODIFY `amenitiy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

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
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `conversation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `like`
--
ALTER TABLE `like`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `workspace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
