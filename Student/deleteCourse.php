<!-- ELearning\Student\deleteCourse.php -->
<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

include('../dbConnection.php');

$stuEmail = $_SESSION['stuLogEmail'];

if (!isset($_GET['course_id'])) {
    echo "Course ID not provided";
    exit;
}

$course_id = $_GET['course_id'];

// Verify that the course belongs to the student
$sql = "SELECT * FROM course WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "Course not found or you don't have permission to delete this course";
    exit;
}
$stmt->close();

// Delete lessons first
$sql = "DELETE FROM lesson WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->close();

// Delete the course
$sql = "DELETE FROM course WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
if ($stmt->execute()) {
    echo "<script>alert('Course deleted successfully'); location.href='myCourse.php';</script>";
} else {
    echo "<script>alert('Failed to delete course'); location.href='myCourse.php';</script>";
}
$stmt->close();
?>