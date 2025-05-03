-- --------------------------------------------------------
-- Database: `lms_db`
-- --------------------------------------------------------

-- Table structure for table `category`
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `category_desc` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `category` (`category_id`, `category_name`, `category_desc`) VALUES
(1, 'Programming Languages', 'Courses related to learning programming languages like Java, C++, Python, etc.'),
(2, 'Data Science', 'Courses focused on data analysis, machine learning, and artificial intelligence.'),
(3, 'Web Development', 'Courses focused on web technologies such as HTML, CSS, JavaScript, and frameworks.'),
(4, 'Business & Management', 'Courses related to business, project management, and leadership skills.');

-- Table structure for table `course`
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_name` text COLLATE utf8_bin NOT NULL,
  `course_desc` text COLLATE utf8_bin NOT NULL,
  `course_author` varchar(255) COLLATE utf8_bin NOT NULL,
  `course_img` text COLLATE utf8_bin NOT NULL,
  `course_duration` text COLLATE utf8_bin NOT NULL,
  `course_price` int(11) NOT NULL,
  `course_original_price` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,  -- New column for category relationship
  FOREIGN KEY (`category_id`) REFERENCES `category`(`category_id`) -- Foreign key for category relationship
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `course` (`course_id`, `course_name`, `course_desc`, `course_author`, `course_img`, `course_duration`, `course_price`, `course_original_price`, `category_id`) VALUES
(1, 'Learn Java Programming', 'Java Programming Video Tutorial basics for absolute beginners.', 'LearningLad', '../image/courseimg/img1.png', '3 Hours', 1655, 1800, 1),
(2, 'C Tutorial for Beginners', 'This course will help you get all the Object Oriented PHP, MYSQLi and ending the course by building a CMS system.', 'Rajesh Kumar', '../image/courseimg/img4.png', '3 Months', 700, 1700, 1),
(3, 'Python Tutorial for Beginners', 'Python Tutorial, Easy Python tutorial for beginner, learn Python Programming, learn python programming with example and syntax.', 'Telusko', '../image/courseimg/img2.png', '4 Months', 800, 1800, 1),
(4, 'Learn C++ Programming', 'In this beginners C++ video tutorials series, you will learn the C++ programming language from core(beginner) level in an easy way.', 'LearningLad', '../image/courseimg/img3.png', '6 Months', 900, 1900, 1),
(5, 'Arabic Language for Beginners', 'Beginners tutorial to learn the Arabic Language', 'Arabic Khatawaat', '../image/courseimg/img5.jpg', '2 Months', 100, 1000, 2),
(6, 'Learn English with Jennifer: Lessons for Beginners', 'American English for Beginners! No actors. No scripts. Real lessons with real results.', 'JenniferESL', '../image/courseimg/img6.jpg', '4 Months', 800, 1600, 2),
(7, 'French Lessons B1', 'Beginners guide to start learning French', 'Lingoni FRENCH', '../image/courseimg/img7.jpg', '4 hours', 500, 4000, 2),
(8, 'Computer Science Crash Course', 'This is react native for Android and iOS app development', 'CrashCourse', '../image/courseimg/img8.png', '2 months', 200, 3000, 3),
(9, 'MIT 16.885J Aircraft Systems Engineering, Fall 2005', 'This is react native for Android and iOS app development', 'MIT OpenCourseWare', '../image/courseimg/Machine.jpg', '2 months', 200, 3000, 3),
(10, 'Introduction to Chemical Engineering', 'Chemical Engineering (E20) is an introductory course offered by the Stanford University Engineering Department.', 'Stanfords', '../image/courseimg/Machine.jpg', '2 months', 200, 3000, 4);

-- Table structure for table `feedback`
CREATE TABLE `feedback` (
  `f_id` int(11) NOT NULL,
  `f_content` text COLLATE utf8_bin NOT NULL,
  `stu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `feedback` (`f_id`, `f_content`, `stu_id`) VALUES
(1, 'My life at iSchool made me stronger and took me a step ahead for being an independent women. I am thankful to all the teachers who supported us and corrected us throughout our career. I am very grateful to the Educode for providing us the best of placement opportunities .', 1);

-- Table structure for table `lesson`
CREATE TABLE `lesson` (
  `lesson_id` int(11) NOT NULL,
  `lesson_name` text COLLATE utf8_bin NOT NULL,
  `lesson_desc` text COLLATE utf8_bin NOT NULL,
  `lesson_link` text COLLATE utf8_bin NOT NULL,
  `course_id` int(11) NOT NULL,
  `course_name` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `lesson` (`lesson_id`, `lesson_name`, `lesson_desc`, `lesson_link`, `course_id`, `course_name`) VALUES
(1, 'Introduction to Python ', 'Introduction to Python Desc', '../lessonvid/python1.mp4', 3, 'Learn Python A-Z'),
(2, 'Python Installation | PyCharm', 'How Python Works Descc', '../lessonvid/python2.mp4', 3, 'Learn Python A-Z'),
(3, 'Getting Started with Python', 'Why Python is powerful Desc', '../lessonvid/python3.mp4', 3, 'Learn Python A-Z'),
(4, ' Comments ', 'Everyone should learn Python  Desccc', '../lessonvid/python4.mp4',3, 'Learn Python A-Z'),
(5, 'What is C++, Its Introduction and History', 'Introduction to PHP Desc', '../lessonvid/C++1.mp4',4, 'Complete PHP Bootcamp'),
(6, 'Where CPP is Used, Why Learn C++ Programming Language', 'How PHP works Desc', '../lessonvid/C++2.mp4', 4, 'Complete PHP Bootcamp'),
(7, 'C++ Source Code to Executable | Compilation, Linking, Pre Processing | Build Process Explained ?', 'is PHP really easy ? desc', '../lessonvid/C++3.mp4', 4, 'Complete PHP Bootcamp'),
(8, 'Tool Set, Tool Chain and IDE ', 'Introduction to Python Desc', '../lessonvid/C++4.mp4', 4, 'Learn Python A-Z'),
(9, ' Installing Code Blocks IDE with Compiler for C and C++', 'Introduction to Python Desc', '../lessonvid/C++5.mp4', 4, 'Learn Python A-Z'),
(10, ' C++ First Hello World Program', 'Introduction to Python Desc', '../lessonvid/python1.mp4', 4, 'Learn Python A-Z'),
(11, 'Introduction to Java Programming, Its History, Why Study it', 'Introduction to Guitar desc1', '../lessonvid/java1.mp4', 1, 'Learn Java Programming'),
(12, 'How Java Program Works, Compiler, Interpreter', 'Type of Guitar Desc2', '../lessonvid/java2.mp4', 1, 'Learn Java Programming'),
(13, 'How To Download and Install Eclipse IDE for Java Programming', 'Intro Hands-on Artificial Intelligence desc', '../lessonvid/java3.mp4', 1, 'Learn Java Programming'),
(14, 'My First Java Hello World Program', 'How it works descccccc', '../lessonvid/video11.mp4', 1, 'Learn Java Programming');

-- Table structure for table `student`
CREATE TABLE `student` (
  `stu_id` int(11) NOT NULL,
  `stu_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_pass` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_occ` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_img` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `student` (`stu_id`, `stu_name`, `stu_email`, `stu_pass`, `stu_occ`, `stu_img`) VALUES
(1, 'Alimata', 'alimata@gmail.com', '123456', 'Data Analyst', '../image/stu/student2.jpg');

-- Create new table for watch history
CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `last_position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `watch_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `stu_email` VARCHAR(255) NOT NULL,
  `course_id` INT NOT NULL,
  `lesson_id` INT NOT NULL,
  `last_position` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Create the new enrollment table
CREATE TABLE `enrollment` (
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  PRIMARY KEY (`student_id`, `course_id`),
  FOREIGN KEY (`student_id`) REFERENCES `student`(`stu_id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `course`(`course_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Create the new Category table with sample data
-- TODO : Add more categories as needed
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `category_desc` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `course` ADD `category_id` int(11) NOT NULL;

ALTER TABLE `course` 
ADD `status` ENUM('draft', 'published') NOT NULL DEFAULT 'draft';

INSERT INTO `category` (`category_name`, `category_desc`) VALUES
('Programming Languages', 'Courses related to learning programming languages like Java, C++, Python, etc.'),
('Data Science', 'Courses focused on data analysis, machine learning, and artificial intelligence.'),
('Web Development', 'Courses focused on web technologies such as HTML, CSS, JavaScript, and frameworks.'),
('Business & Management', 'Courses related to business, project management, and leadership skills.'),
('Language Learning', 'Courses for learning new languages'),
('Engineering', 'Courses related to engineering disciplines'),
('Computer Science', 'Courses covering fundamental and advanced topics in computer science');

UPDATE `course` SET `category_id` = 1 WHERE `course_id` IN (1, 2, 3, 4);
UPDATE `course` SET `category_id` = 5 WHERE `course_id` IN (5, 6, 7);
UPDATE `course` SET `category_id` = 7 WHERE `course_id` = 8;
UPDATE `course` SET `category_id` = 6 WHERE `course_id` IN (9, 10);
-- --------------------------------------------------------
-- Indexes for dumped tables
-- --------------------------------------------------------

ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

ALTER TABLE `feedback`
  ADD PRIMARY KEY (`f_id`);

ALTER TABLE `lesson`
  ADD PRIMARY KEY (`lesson_id`);

ALTER TABLE `student`
  ADD PRIMARY KEY (`stu_id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT for dumped tables
-- --------------------------------------------------------

ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

ALTER TABLE `feedback`
  MODIFY `f_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `lesson`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

ALTER TABLE `student`
  MODIFY `stu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;


UPDATE course
SET status = 'published'
WHERE status = 'draft';

COMMIT;
