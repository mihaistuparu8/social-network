-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 14 Mai 2019 la 19:58
-- Versiune server: 5.7.21
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `social_network`
--
CREATE DATABASE IF NOT EXISTS `social_network` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `social_network`;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `posted_at` datetime NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `comments`
--

INSERT INTO `comments` (`id`, `comment`, `user_id`, `posted_at`, `post_id`) VALUES
(1, 'Multumesc!', 3, '2018-08-15 02:28:43', 2),
(2, 'Un alt comentariu la postare!', 3, '2018-08-15 02:29:02', 2),
(3, 'Acesta este un comentariu!', 4, '2018-08-15 02:32:42', 1),
(6, 'cometariu', 2, '2018-08-16 10:07:27', 7),
(8, '#merge ', 2, '2018-12-16 12:09:31', 7),
(9, '#merge ', 2, '2018-12-16 12:10:15', 7),
(11, '@popescu_andrei ce faci?', 2, '2018-12-16 15:02:53', 7);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `followers`
--

DROP TABLE IF EXISTS `followers`;
CREATE TABLE IF NOT EXISTS `followers` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `follower_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES
(1, 2, 1),
(2, 5, 1),
(3, 4, 1),
(4, 5, 2),
(6, 2, 3),
(7, 4, 3),
(8, 5, 3),
(9, 2, 4),
(10, 5, 4),
(11, 3, 4),
(12, 2, 5),
(13, 3, 5),
(15, 3, 2);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `login_tokens`
--

DROP TABLE IF EXISTS `login_tokens`;
CREATE TABLE IF NOT EXISTS `login_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` char(64) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `login_tokens`
--

INSERT INTO `login_tokens` (`id`, `token`, `user_id`) VALUES
(1, 'c8402abaae8a088afa104b9c3388d4567f83426e', 2),
(6, '6b7d498447beaecbee3d41af4fb64e37995b4dbe', 2),
(8, 'b43eaba02249868c1ade69d99424a8de2f7c11b0', 2),
(12, 'ae25bd07dc80ffdfc8fd40406880a3b5a0af7d7b', 8),
(13, '52808f1e110738ed612417b3a286609cb8fd2266', 2),
(15, '8d733bd4b932c012cc3a7c4b5063269caa96e223', 2),
(16, 'cd1d46462e3a6b0847bfd02430e6b80e1a43324c', 2);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `body` text NOT NULL,
  `sender` int(11) UNSIGNED NOT NULL,
  `receiver` int(11) UNSIGNED NOT NULL,
  `readed` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `messages`
--

INSERT INTO `messages` (`id`, `body`, `sender`, `receiver`, `readed`) VALUES
(5, 'bine te-am gasit!', 2, 3, 1),
(7, 'ce mai faci?', 2, 3, 1),
(8, 'bine, tu?', 2, 3, 0);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` int(11) UNSIGNED NOT NULL,
  `receiver` int(10) UNSIGNED NOT NULL,
  `sender` int(11) UNSIGNED NOT NULL,
  `extra` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `receiver`, `sender`, `extra`) VALUES
(1, 1, 3, 2, NULL),
(2, 1, 3, 2, NULL),
(3, 1, 3, 2, NULL),
(4, 1, 2, 2, NULL),
(8, 1, 2, 2, '{ \"postbody\": \"@mihai_iulian ce faci?\"} '),
(9, 2, 2, 2, ''),
(10, 2, 2, 2, '');

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `password_tokens`
--

DROP TABLE IF EXISTS `password_tokens`;
CREATE TABLE IF NOT EXISTS `password_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `token` char(64) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `body` varchar(160) NOT NULL,
  `posted_at` datetime NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `likes` int(10) UNSIGNED NOT NULL,
  `postimg` varchar(255) DEFAULT NULL,
  `topics` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `posts`
--

INSERT INTO `posts` (`id`, `body`, `posted_at`, `user_id`, `likes`, `postimg`, `topics`) VALUES
(7, '', '2018-08-15 02:29:45', 3, 3, 'https://i.imgur.com/vHHaAom.png', NULL),
(8, '\"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. \r\n', '2018-08-15 02:33:59', 4, 2, '', NULL),
(9, 'Salutare! Acesta este un comentariu!', '2018-08-15 02:36:12', 5, 1, '', NULL),
(10, 'Acesta este primul cont pentru verificare!', '2018-08-15 02:37:09', 1, 0, '', NULL),
(31, '@popescu_andrei ce faci?', '2018-12-16 15:03:24', 2, 0, '', ''),
(35, '@aleluia', '2018-12-16 15:14:32', 2, 0, '', ''),
(37, '@popescu_andrei boxx', '2018-12-16 15:16:08', 2, 0, '', ''),
(43, '@salut mihai_iulian', '2018-12-23 19:30:06', 2, 1, '', '');

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `post_likes`
--

DROP TABLE IF EXISTS `post_likes`;
CREATE TABLE IF NOT EXISTS `post_likes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`) VALUES
(81, 8, 4),
(82, 7, 4),
(85, 7, 5),
(86, 8, 5),
(101, 9, 2),
(106, 13, 2),
(111, 7, 2),
(115, 42, 2),
(116, 41, 2),
(136, 40, 2),
(140, 43, 2);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` varchar(60) NOT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `profileimg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `verified`, `profileimg`) VALUES
(1, 'verify', '$2y$10$ILe2hg9T5d47SlC7mEgFM.XQX4NQAcrLU/RsBrBIofjmyMRNARXke', 'verify@gmail.com', 0, ''),
(2, 'mihai_iulian', '$2y$10$af50A3XbxiVz/nn2gEo5meGMtHcB1YZvOKYa4Xhx2Xb/.8wT1XuWe', 'mihai10shiros@yahoo.com', 1, 'https://i.imgur.com/d3q4Sjp.jpg'),
(3, 'popescu_andrei', '$2y$10$KonSAUWOLoJNbiUqeeVkIOmlkqrAMfxp79yQ2NxFIEQkGBoZwrgJe', 'popescu@gmail.com', 0, ''),
(4, 'marian98', '$2y$10$DOqonrZOUsi0us7.li0Su.mgOYc3o7yHJxmyv2PlFoI59VDVzclOS', 'marian98@gmail.com', 1, ''),
(5, 'george', '$2y$10$UwLYNp5/VAIlPbIm7LyVIuYduTG7Y/Iihg4mnb2SiwdXuaZrLY1n6', 'george93@gmail.com', 1, ''),
(8, 'mihaistuparu8', '$2y$10$tuc1zdnMnGEGOvmu3CD48uqGYSAUtiRFTkhTrS8QcE4sg3wiPPnge', 'mihaistuparu8@gmail.com', 0, '');

--
-- Restrictii pentru tabele sterse
--

--
-- Restrictii pentru tabele `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrictii pentru tabele `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD CONSTRAINT `login_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrictii pentru tabele `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Restrictii pentru tabele `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
