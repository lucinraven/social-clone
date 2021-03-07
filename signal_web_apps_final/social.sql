-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2021 at 11:17 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `social`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_body` text NOT NULL,
  `posted_by` varchar(60) NOT NULL,
  `posted_to` varchar(60) NOT NULL,
  `date_added` datetime NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_body`, `posted_by`, `posted_to`, `date_added`, `post_id`) VALUES
(9, 'hey', 'zaira_mundo', 'zaira_mundo', '2021-02-15 13:18:51', 69),
(10, 'hello', 'zaira_mundo', 'zaira_mundo', '2021-02-15 18:34:19', 71),
(11, 'hey hey', 'zaira_mundo', 'zaira_mundo', '2021-02-15 18:44:16', 71),
(12, 'hey hey', 'zaira_mundo', 'zaira_mundo', '2021-02-15 20:02:26', 84),
(13, 'I like it', 'raven_lucin', 'zaira_mundo', '2021-02-17 20:19:07', 82),
(14, 'yes yes', 'zaira_mundo', 'zaira_mundo', '2021-02-18 22:49:52', 82);

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) NOT NULL,
  `user_from` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `user_to`, `user_from`) VALUES
(16, 'zaira_mundo', 'selena_mendoza');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `username`, `post_id`) VALUES
(35, 'zaira_mundo', 84),
(36, 'zaira_mundo', 82),
(37, 'raven_lucin', 82),
(38, 'zaira_mundo', 90),
(39, 'gina_lopez', 91);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) NOT NULL,
  `user_from` varchar(50) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  `opened` varchar(3) NOT NULL,
  `viewed` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_to`, `user_from`, `body`, `date`, `opened`, `viewed`) VALUES
(44, 'zaira_mundo', 'zaira_mundo', 'hyey', '2021-02-15 15:15:16', 'yes', 'yes'),
(45, 'raven_lucin', 'zaira_mundo', 'hey hey', '2021-02-16 19:15:11', 'yes', 'yes'),
(46, 'raven_lucin', 'zaira_mundo', 'hey hey', '2021-02-16 19:27:15', 'yes', 'yes'),
(47, 'zaira_mundo', 'raven_lucin', 'hey hey', '2021-02-16 19:36:53', 'yes', 'yes'),
(48, 'raven_lucin', 'zaira_mundo', 'hey', '2021-02-16 19:37:36', 'no', 'yes'),
(49, 'raven_lucin', 'zaira_mundo', 'hello again', '2021-02-16 19:49:11', 'no', 'yes'),
(50, 'zaira_mundo', 'oliver_smith', 'Hey, how are you?', '2021-02-17 21:13:05', 'yes', 'yes'),
(51, 'gina_lopez', 'zaira_mundo', 'hey hey', '2021-02-18 02:16:29', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_to` varchar(50) NOT NULL,
  `user_from` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `opened` varchar(3) NOT NULL,
  `viewed` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_to`, `user_from`, `message`, `link`, `datetime`, `opened`, `viewed`) VALUES
(19, 'zaira_mundo', 'raven_lucin', 'Raven Lucin liked your post', 'post.php?id=82', '2021-02-17 20:06:00', 'yes', 'yes'),
(20, 'zaira_mundo', 'raven_lucin', 'Raven Lucin commented on your post', 'post.php?id=82', '2021-02-17 20:19:07', 'yes', 'yes'),
(21, 'raven_lucin', 'zaira_mundo', 'Zaira Mundo liked your post', 'post.php?id=89', '2021-02-18 19:40:02', 'no', 'no'),
(22, 'raven_lucin', 'zaira_mundo', 'Zaira Mundo commented on a post you commented on', 'post.php?id=82', '2021-02-18 22:49:52', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `body` text NOT NULL,
  `added_by` varchar(60) NOT NULL,
  `user_to` varchar(60) NOT NULL,
  `date_added` datetime NOT NULL,
  `user_closed` varchar(3) NOT NULL,
  `likes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `body`, `added_by`, `user_to`, `date_added`, `user_closed`, `likes`) VALUES
(66, 'hello', 'zaira_mundo', 'none', '2021-02-15 01:52:44', 'no', 0),
(67, 'hello again', 'zaira_mundo', 'none', '2021-02-15 09:22:47', 'no', 0),
(69, 'hello1', 'zaira_mundo', 'none', '2021-02-15 14:16:12', 'no', 0),
(70, 'There I saw you', 'zaira_mundo', 'none', '2021-02-15 18:30:38', 'no', 0),
(71, 'hey hey hey\r\n\r\nhey hey', 'zaira_mundo', 'none', '2021-02-15 18:30:50', 'no', 0),
(72, 'hey hye', 'zaira_mundo', 'none', '2021-02-15 15:35:00', 'no', 0),
(73, 'hey hye', 'zaira_mundo', 'none', '2021-02-15 15:35:05', 'no', 0),
(74, 'hey hey\r\n', 'zaira_mundo', 'none', '2021-02-15 15:35:18', 'no', 0),
(76, 'hey hey', 'zaira_mundo', 'none', '2021-02-15 19:44:40', 'no', 0),
(77, 'hey hey', 'zaira_mundo', 'none', '2021-02-15 19:44:45', 'no', 0),
(78, 'hey heygbbvwiebviordkmj', 'zaira_mundo', 'none', '2021-02-15 19:47:02', 'no', 0),
(79, 'hey heygbbvwiebviordkmj', 'zaira_mundo', 'none', '2021-02-15 19:47:08', 'no', 0),
(80, 'hey heygbbvwiebviordkmj', 'zaira_mundo', 'none', '2021-02-15 19:47:13', 'no', 0),
(81, 'hey heygbbvwiebviordkmj', 'zaira_mundo', 'none', '2021-02-15 19:47:18', 'no', 0),
(82, 'hello hello', 'zaira_mundo', 'none', '2021-02-15 19:57:08', 'no', 2),
(83, 'hello hello', 'zaira_mundo', 'none', '2021-02-15 19:57:13', 'no', 0),
(84, 'hello hello', 'zaira_mundo', 'none', '2021-02-15 19:57:18', 'no', 1),
(87, 'hey hey', 'raven_lucin', 'none', '2021-02-16 02:08:55', 'no', 0),
(88, 'post 2', 'raven_lucin', 'none', '2021-02-16 02:09:01', 'no', 0),
(89, 'Hello, Raven', 'zaira_mundo', 'raven_lucin', '2021-02-18 19:40:02', 'no', 0),
(90, 'hello, zaira is the name', 'zaira_mundo', 'none', '2021-02-18 22:04:31', 'no', 1),
(91, 'Hey', 'gina_lopez', 'none', '2021-02-19 01:10:12', 'no', 1);

-- --------------------------------------------------------

--
-- Table structure for table `profile_user`
--

CREATE TABLE `profile_user` (
  `id` int(11) NOT NULL,
  `users` varchar(60) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `works` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `contacts` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `profile_user`
--

INSERT INTO `profile_user` (`id`, `users`, `address`, `works`, `education`, `contacts`) VALUES
(1, 'zaira_mundo', 'Dubai, UAE', '', 'Bath Spa University', ''),
(5, 'patrick_kim', 'Australia', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `signup_date` date NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `cover_pic` varchar(255) NOT NULL,
  `num_posts` int(11) NOT NULL,
  `user_closed` varchar(3) NOT NULL,
  `friend_array` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `signup_date`, `profile_pic`, `cover_pic`, `num_posts`, `user_closed`, `friend_array`) VALUES
(11, 'Zaira', 'Mundo', 'zaira_mundo', 'zairamundo@gmail.com', '2544e1ea197f8c284d10c004d153bcb8', '2021-02-14', 'assets/images/profile_pics/profilezaira_mundo.jpg', 'assets/images/cover_pics/coverzaira_mundo.png', 21, 'no', ',raven_lucin,oliver_smith,gina_lopez,'),
(14, 'Raven', 'Lucin', 'raven_lucin', 'ravenlucin@gmail.com', '5e718c36b8a3e65b05659736a034c48e', '2021-02-16', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ',zaira_mundo,oliver_smith,'),
(15, 'Oliver', 'Smith', 'oliver_smith', 'oliversmith@gmail.com', '553fcb594976460e66e32da18a2b6f88', '2021-02-17', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ',raven_lucin,zaira_mundo,'),
(16, 'Gina', 'Lopez', 'gina_lopez', 'ginalopez@gmail.com', 'fa0e62ff4c88ad384523d595cb1ba738', '2021-02-17', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 1, 'no', ',zaira_mundo,'),
(17, 'Selena', 'Mendoza', 'selena_mendoza', 'selenamendoza@gmail.com', 'ec0b01f9a4cefe9de999a768fa46f393', '2021-02-18', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ','),
(18, 'River', 'Mayo', 'river_mayo', 'rivermayo@gmail.com', '2a91844209f358425371096b21b80309', '2021-02-18', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ','),
(19, 'Anne', 'Bridgeton', 'anne_bridgeton', 'annebridgeton@gmail.com', 'aa1ba83ca0ff1918bf3f66e2d31da359', '2021-02-18', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ','),
(20, 'Patrick', 'Kim', 'patrick_kim', 'patrickkim@gmail.com', '7cc2ae164fbe5a3b4fb70c2ecf667fe2', '2021-02-18', 'assets/images/profile_pics/default_profile.png', 'assets/images/cover_pics/default_cover.jpg', 0, 'no', ',');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_user`
--
ALTER TABLE `profile_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `profile_user`
--
ALTER TABLE `profile_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
