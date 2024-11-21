-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 06:00 PM
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
-- Database: `mina_saeed`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `user_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` varchar(20) NOT NULL,
  `content_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `approve` tinyint(1) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `see` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `tutor_id`, `playlist_id`, `title`, `description`, `video`, `audio`, `thumb`, `pdf`, `date`, `status`) VALUES
('hvG7vJACTQ97eAePJrrU', 'PHk4LbyjaZ67LH05ooP4', 'hzzZoNOesPZZrbUXnuia', 'سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسون', 'b', 'https://youtu.be/FGBiFUFyjQI?si=Elv-my0QWPTJItbv', NULL, 'DCfaNmM7y5o324oi88rR.jpeg', '6edrci9IJIXO8MnjPblX.pdf', '2024-08-26', 'active'),
('wVLjxAvCvI0Z9rGxnlQ8', 'PHk4LbyjaZ67LH05ooP4', 'hzzZoNOesPZZrbUXnuia', 'واجب الحصة الاولي', '', '', NULL, 'APYrbprs36GmItjQwXuf.png', 'z86UBzrF1n9YwrKluoDE.pdf', '2024-09-06', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `user_answer_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_answer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_name`
--

CREATE TABLE `exam_name` (
  `exam_id` int(11) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `exam_title` varchar(50) NOT NULL,
  `exam_description` varchar(100) NOT NULL,
  `playlist_id` varchar(20) NOT NULL,
  `exam_time` int(11) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_name`
--

INSERT INTO `exam_name` (`exam_id`, `tutor_id`, `exam_title`, `exam_description`, `playlist_id`, `exam_time`, `year`, `status`) VALUES
(16, 'PHk4LbyjaZ67LH05ooP4', 'اول امتحان', '', 'hzzZoNOesPZZrbUXnuia', 1, 3, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_question` varchar(1000) DEFAULT NULL,
  `ch_1` varchar(255) DEFAULT NULL,
  `ch_2` varchar(255) DEFAULT NULL,
  `ch_3` varchar(255) DEFAULT NULL,
  `ch_4` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(20) NOT NULL,
  `degree` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`question_id`, `exam_id`, `exam_question`, `ch_1`, `ch_2`, `ch_3`, `ch_4`, `img`, `correct_answer`, `degree`) VALUES
(112, 16, NULL, NULL, NULL, NULL, NULL, 'dPsE1wjYwvZxJChCZVmO.png', '', 4),
(113, 16, 'السؤال الاول ', 'a', 'b', 'c', 'd', NULL, 'd', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `exam_result_id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `gender` varchar(8) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `show_degree` tinyint(1) NOT NULL,
  `Q_is_marked` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` int(11) NOT NULL,
  `content_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `homework` varchar(255) DEFAULT NULL,
  `degree` int(11) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `homework`
--

INSERT INTO `homework` (`id`, `content_id`, `user_id`, `homework`, `degree`, `date`) VALUES
(34, 'wVLjxAvCvI0Z9rGxnlQ8', 'wr919COUOQWP5iv4zjJ9', NULL, 4, '2024-10-05'),
(35, 'wVLjxAvCvI0Z9rGxnlQ8', 'wr919COUOQWP5iv4zjJ9', NULL, 0, '2024-10-11');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `content_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `year` tinyint(4) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `playlist`
--

INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `year`, `date`, `status`) VALUES
('hzzZoNOesPZZrbUXnuia', 'PHk4LbyjaZ67LH05ooP4', 'سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسونج سامسون', '', 'F9iyApS6It6XkI4Ts6nI.jpeg', 3, '2024-08-26', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `name`, `profession`, `email`, `password`, `image`) VALUES
('PHk4LbyjaZ67LH05ooP4', 'Mina', 'math teacher', 'paulaessam8@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 'aBSaqsmLXf9bJWShWrJL.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(100) NOT NULL,
  `parent_num` varchar(100) NOT NULL,
  `gender` varchar(8) NOT NULL,
  `governorate` varchar(255) NOT NULL,
  `register` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `year` tinyint(4) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `is_logged_in` tinyint(1) NOT NULL,
  `last_login` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `number`, `parent_num`, `gender`, `governorate`, `register`, `points`, `password`, `image`, `year`, `session_id`, `is_logged_in`, `last_login`, `status`) VALUES
('wr919COUOQWP5iv4zjJ9', 'paula essam', 'poula21-01446@student.eelu.edu.eg', '01014628698', '01014628698', 'male', 'الفيوم', 'منصة فقط', 0, '7c222fb2927d828af22f592134e8932480637c0d', '', 3, 'e2dd8fac9c214f2e57aa0dea655ff030', 1, '1728004300', 1),
('jvJ6OInBA8fHZw4Un50h', 'toni essam', 'new@p.com', '123', '123', 'male', 'الفيوم', 'منصة فقط', 0, '7c222fb2927d828af22f592134e8932480637c0d', '', 3, '16453d6e2683b8800ded2a27c7f595d9', 1, '1728048986', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`user_answer_id`);

--
-- Indexes for table `exam_name`
--
ALTER TABLE `exam_name`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`exam_result_id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `user_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=812;

--
-- AUTO_INCREMENT for table `exam_name`
--
ALTER TABLE `exam_name`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `exam_result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
