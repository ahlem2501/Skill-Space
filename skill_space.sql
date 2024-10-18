SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table structure for table `student`
CREATE TABLE `student` (
  `stu_id` int(11) NOT NULL,
  `stu_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `stu_pass` varchar(255) COLLATE utf8_bin NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Dumping data for table `student`
INSERT INTO `student` (`stu_id`, `stu_name`, `stu_email`, `stu_pass`) VALUES
(1, 'john', 'john@example.com', '1234', 
(2, 'ali, 'ali@example.com', '12345', );

-- --------------------------------------------------------


-- Indexes for table `student`
ALTER TABLE `student`
  ADD PRIMARY KEY (`stu_id`);

-- --------------------------------------------------------

ALTER TABLE `student`
  MODIFY `stu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

COMMIT;
