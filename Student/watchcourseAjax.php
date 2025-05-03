<?php
// ELearning\Student\watchcourseAjax.php

session_start();
include('../dbConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['is_login'])) {
        exit("Not logged in");
    }

    // Handle updateHistory action
    if (isset($_POST['action']) && $_POST['action'] === 'updateHistory') {
        $stuEmail   = $_SESSION['stuLogEmail'];
        $course_id  = intval($_POST['course_id']);
        $lesson_id  = intval($_POST['lesson_id']);
        $currentPos = floor(floatval($_POST['currentTime'])); // Convert to integer

        // Check for existing watch_history record
        $sql_check = "SELECT id FROM watch_history WHERE stu_email = ? AND course_id = ? AND lesson_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("sii", $stuEmail, $course_id, $lesson_id);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($res_check->num_rows > 0) {
            // Update existing record
            $row = $res_check->fetch_assoc();
            $id  = $row['id'];
            $sql_update = "UPDATE watch_history SET last_position = ? WHERE id = ?";
            $stmt_up = $conn->prepare($sql_update);
            $stmt_up->bind_param("ii", $currentPos, $id);
            $stmt_up->execute();
            $stmt_up->close();
        } else {
            // Insert new record
            $sql_insert = "INSERT INTO watch_history (stu_email, course_id, lesson_id, last_position) VALUES (?, ?, ?, ?)";
            $stmt_in = $conn->prepare($sql_insert);
            $stmt_in->bind_param("siii", $stuEmail, $course_id, $lesson_id, $currentPos);
            $stmt_in->execute();
            $stmt_in->close();
        }

        echo "OK";
        exit;
    }
}

exit("Invalid Request");