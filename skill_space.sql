SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table structure for table `course`
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_name` text COLLATE utf8_bin NOT NULL,
  `course_desc` text COLLATE utf8_bin NOT NULL,
  `course_author` varchar(255) COLLATE utf8_bin NOT NULL,
  `course_img` text COLLATE utf8_bin NOT NULL,
  `course_duration` text COLLATE utf8_bin NOT NULL,
  `course_price` int(11) NOT NULL,
  `course_original_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `course`
INSERT INTO `course` (`course_id`, `course_name`, `course_desc`, `course_author`, `course_img`, `course_duration`, `course_price`, `course_original_price`) VALUES
(8, 'Learn Guitar The Easy Wayy', 'This course is your \"Free Pass\" to playing guitar. It is the most direct and to the point complete online guitar course.', 'Adil', '../image/courseimg/Guitar.jpg', '3 Hours', 1655, 1800),
(9, 'Complete PHP Bootcamp', 'This course will help you get all the Object Oriented PHP, MYSQLi and ending the course by building a CMS system.', 'Rajesh Kumar', '../image/courseimg/php.jpg', '3 Months', 700, 1700),
(10, 'Learn Python A-Z', 'This is the most comprehensive, yet straight-forward, course for the Python programming language.', 'Rahul Kumar', '../image/courseimg/Python.jpg', '4 Months', 800, 1800);

-- --------------------------------------------------------

-- Table structure for table `feedback`
CREATE TABLE `feedback` (
  `f_id` int(11) NOT NULL,
  `f_content` text COLLATE utf8_bin NOT NULL,
  `stu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `feedback`
INSERT INTO `feedback` (`f_id`, `f_content`, `stu_id`) VALUES
(3, 'My life at iSchool made me stronger and took me a step ahead for being an independent woman.', 171),
(8, 'I am grateful to iSchool - both the faculty and the Training & Placement Department.', 172),
(9, 'iSchool is a place of learning, fun, culture, and many such life preaching activities.', 173);

-- --------------------------------------------------------

-- Table structure for table `lesson`
CREATE TABLE `lesson` (
  `lesson_id` int(11) NOT NULL,
  `lesson_name` text COLLATE utf8_bin NOT NULL,
  `lesson_desc` text COLLATE utf8_bin NOT NULL,
  `lesson_link` text COLLATE utf8_bin NOT NULL,
  `course_id` int(11) NOT NULL,
  `course_name` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `lesson`
INSERT INTO `lesson` (`lesson_id`, `lesson_name`, `lesson_desc`, `lesson_link`, `course_id`, `course_name`) VALUES
(32, 'Introduction to Python ', 'Introduction to Python Desc', '../lessonvid/video2.mp4', 10, 'Learn Python A-Z'),
(33, 'How Python Works', 'How Python Works Desc', '../lessonvid/video3.mp4', 10, 'Learn Python A-Z');

-- --------------------------------------------------------

-- Table structure for table `student`
CREATE TABLE `student` (
  `stu_id` int(11) NOT NULL,
  `stu_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_pass` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_occ` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_img` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `student`
INSERT INTO `student` (`stu_id`, `stu_name`, `stu_email`, `stu_pass`, `stu_occ`, `stu_img`) VALUES
(171, 'Captain Marvel', 'cap@example.com', '123456', ' Web Designer', '../image/stu/student2.jpg'),
(172, 'Ant Man', 'ant@example.com', '123456', ' Web Developer', '../image/stu/student4.jpg');

-- --------------------------------------------------------

-- Indexes for table `course`
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

-- Indexes for table `feedback`
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`f_id`);

-- Indexes for table `lesson`
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`lesson_id`);

-- Indexes for table `student`
ALTER TABLE `student`
  ADD PRIMARY KEY (`stu_id`);

-- --------------------------------------------------------

-- AUTO_INCREMENT for tables
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `feedback`
  MODIFY `f_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `lesson`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

ALTER TABLE `student`
  MODIFY `stu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

COMMIT;
