<!-- ELearning\Student\deleteLesson.php -->
<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

include('../dbConnection.php');

$stuEmail = $_SESSION['stuLogEmail'];

if (!isset($_GET['lesson_id'])) {
    echo "Lesson ID not provided";
    exit;
}

$lesson_id = $_GET['lesson_id'];

// Get the course_id from the lesson
$sql = "SELECT course_id FROM lesson WHERE lesson_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "Lesson not found";
    exit;
}
$lesson = $result->fetch_assoc();
$course_id = $lesson['course_id'];
$stmt->close();

// Verify that the course belongs to the student
$sql = "SELECT * FROM course WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "You don't have permission to delete this lesson";
    exit;
}
$stmt->close();

// Delete the lesson
$sql = "DELETE FROM lesson WHERE lesson_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
if ($stmt->execute()) {
    echo "<script>alert('Lesson deleted successfully'); location.href='studentManageCourse.php?course_id=$course_id';</script>";
} else {
    echo "<script>alert('Failed to delete lesson'); location.href='studentManageCourse.php?course_id=$course_id';</script>";
}
$stmt->close();
?>