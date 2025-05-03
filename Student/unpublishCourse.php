<?php
session_start();
include('../dbConnection.php');

if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

$stuEmail = $_SESSION['stuLogEmail'];
$course_id = intval($_GET['course_id']);

$sql = "UPDATE course SET status = 'draft' WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
if ($stmt->execute()) {
    echo "<script>alert('Course unpublished successfully!'); location.href='myCourse.php';</script>";
} else {
    echo "<script>alert('Failed to unpublish course: " . $conn->error . "'); location.href='myCourse.php';</script>";
}
$stmt->close();
?>