-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: 35.233.124.176
-- Generation Time: Mar 21, 2021 at 06:27 PM
-- Server version: 5.7.32-google-log
-- PHP Version: 7.0.33-0ubuntu0.16.04.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_prerelease`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `data_coding_id` int(11) NOT NULL COMMENT 'The id of the answer given (is the data_coding,not the class)',
  `user_id` int(11) NOT NULL,
  `value` text COMMENT 'The value if not differently coded (date,text or free fields) Use this field if value in data_coding is null'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Join user-classification one to many';

-- --------------------------------------------------------

--
-- Table structure for table `collector`
--

CREATE TABLE `collector` (
  `id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `name` varchar(300) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A Collector is the entity that collect data (mostly hospital)';

-- --------------------------------------------------------

--
-- Table structure for table `data_coding`
--

CREATE TABLE `data_coding` (
  `id` int(11) NOT NULL,
  `class` int(11) NOT NULL COMMENT 'The classification group',
  `value` int(11) DEFAULT NULL,
  `exclusive` tinyint(1) DEFAULT NULL,
  `type` enum('text','numeric','date','choice') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All the possible answers as for UK Biobank data-coding';

-- --------------------------------------------------------

--
-- Table structure for table `data_coding_text`
--

CREATE TABLE `data_coding_text` (
  `id` int(11) NOT NULL,
  `data_coding_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text,
  `info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Handle the multilanguages text for the possible answers';

-- --------------------------------------------------------

--
-- Table structure for table `join_user_collector`
--

CREATE TABLE `join_user_collector` (
  `user_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL,
  `code` varchar(256) DEFAULT NULL COMMENT 'Sample code provided by collector'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relation one to many with code (sample code given by collector) between user and collector';

-- --------------------------------------------------------

--
-- Table structure for table `label`
--

CREATE TABLE `label` (
  `id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `id_language` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Handle all the multilanguage text';

-- --------------------------------------------------------

--
-- Table structure for table `lang`
--

CREATE TABLE `lang` (
  `id` int(11) NOT NULL,
  `description` varchar(20) NOT NULL,
  `code` varchar(5) NOT NULL COMMENT 'short for language name',
  `icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All the languages available';

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `ord` int(11) NOT NULL,
  `flow` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `section` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The meta-structure of the question with versioning for handle changes in questions';

-- --------------------------------------------------------

--
-- Table structure for table `question_text`
--

CREATE TABLE `question_text` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text,
  `info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The multilanguage text for the questions';

-- --------------------------------------------------------

--
-- Table structure for table `question_trigger`
--

CREATE TABLE `question_trigger` (
  `parent` int(11) NOT NULL COMMENT 'Parent Question',
  `conditional` int(11) NOT NULL COMMENT 'data_coding that if checked trigger a new question',
  `triggered` int(11) NOT NULL COMMENT 'Question triggered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='List of Sub-Question asked conditionally upon specific answe';

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The role of a user. Numeric with description';

-- --------------------------------------------------------

--
-- Table structure for table `section_text`
--

CREATE TABLE `section_text` (
  `id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `class` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Handle all the used metric units';

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL COMMENT 'unique official ID if available in the country',
  `token` varchar(256) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `push_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Handle all the users with different roles';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date_question` (`date`,`question_id`,`user_id`) USING BTREE,
  ADD KEY `question` (`question_id`) USING BTREE,
  ADD KEY `user` (`user_id`) USING BTREE,
  ADD KEY `answer` (`data_coding_id`) USING BTREE;

--
-- Indexes for table `collector`
--
ALTER TABLE `collector`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_coding`
--
ALTER TABLE `data_coding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`class`);

--
-- Indexes for table `data_coding_text`
--
ALTER TABLE `data_coding_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language` (`language_id`),
  ADD KEY `data_coding` (`data_coding_id`) USING BTREE;

--
-- Indexes for table `join_user_collector`
--
ALTER TABLE `join_user_collector`
  ADD PRIMARY KEY (`user_id`,`collector_id`),
  ADD UNIQUE KEY `code` (`code`,`collector_id`),
  ADD KEY `collector_id` (`collector_id`);

--
-- Indexes for table `label`
--
ALTER TABLE `label`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language` (`id_language`) USING BTREE;

--
-- Indexes for table `lang`
--
ALTER TABLE `lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`class`),
  ADD KEY `section_ibfk_1` (`section`);

--
-- Indexes for table `question_text`
--
ALTER TABLE `question_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question` (`question_id`),
  ADD KEY `language` (`language_id`) USING BTREE;

--
-- Indexes for table `question_trigger`
--
ALTER TABLE `question_trigger`
  ADD PRIMARY KEY (`parent`,`conditional`,`triggered`),
  ADD KEY `triggered` (`triggered`),
  ADD KEY `conditional` (`conditional`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_text`
--
ALTER TABLE `section_text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_language` (`language_id`),
  ADD KEY `class` (`class`) USING BTREE;

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role` (`role`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1331;
--
-- AUTO_INCREMENT for table `collector`
--
ALTER TABLE `collector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `data_coding`
--
ALTER TABLE `data_coding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;
--
-- AUTO_INCREMENT for table `data_coding_text`
--
ALTER TABLE `data_coding_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;
--
-- AUTO_INCREMENT for table `label`
--
ALTER TABLE `label`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=776;
--
-- AUTO_INCREMENT for table `lang`
--
ALTER TABLE `lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=583;
--
-- AUTO_INCREMENT for table `question_text`
--
ALTER TABLE `question_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=934;
--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `section_text`
--
ALTER TABLE `section_text`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;
--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answer_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`),
  ADD CONSTRAINT `answer_ibfk_4` FOREIGN KEY (`data_coding_id`) REFERENCES `data_coding` (`id`);

--
-- Constraints for table `data_coding_text`
--
ALTER TABLE `data_coding_text`
  ADD CONSTRAINT `data_coding_text_ibfk_1` FOREIGN KEY (`data_coding_id`) REFERENCES `data_coding` (`id`),
  ADD CONSTRAINT `data_coding_text_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `lang` (`id`);

--
-- Constraints for table `join_user_collector`
--
ALTER TABLE `join_user_collector`
  ADD CONSTRAINT `join_user_colelctor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `join_user_collector_ibfk_1` FOREIGN KEY (`collector_id`) REFERENCES `collector` (`id`);

--
-- Constraints for table `label`
--
ALTER TABLE `label`
  ADD CONSTRAINT `label_ibfk_1` FOREIGN KEY (`id_language`) REFERENCES `lang` (`id`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`class`) REFERENCES `data_coding` (`class`),
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`section`) REFERENCES `section_text` (`class`);

--
-- Constraints for table `question_text`
--
ALTER TABLE `question_text`
  ADD CONSTRAINT `question_text_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `question_text_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `lang` (`id`);

--
-- Constraints for table `question_trigger`
--
ALTER TABLE `question_trigger`
  ADD CONSTRAINT `question_trigger_ibfk_2` FOREIGN KEY (`parent`) REFERENCES `question` (`id`),
  ADD CONSTRAINT `question_trigger_ibfk_3` FOREIGN KEY (`triggered`) REFERENCES `question` (`id`),
  ADD CONSTRAINT `question_trigger_ibfk_4` FOREIGN KEY (`conditional`) REFERENCES `data_coding` (`id`);

--
-- Constraints for table `section_text`
--
ALTER TABLE `section_text`
  ADD CONSTRAINT `section_language` FOREIGN KEY (`language_id`) REFERENCES `lang` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
