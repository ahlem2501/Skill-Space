<?php
// ELearning\functions.php
function getCourseProgress($conn, $course_id, $stuEmail) {
    // 1) Count how many lessons total in this course
    $sql_total = "SELECT COUNT(*) AS total_lessons FROM lesson WHERE course_id = ?";
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("i", $course_id);
    $stmt_total->execute();
    $res_total = $stmt_total->get_result()->fetch_assoc();
    $stmt_total->close();
    $totalLessons = (int) $res_total['total_lessons'];

    if ($totalLessons === 0) {
        // Avoid division by zero
        return ['percent' => 0, 'watchedCount' => 0, 'totalLessons' => 0];
    }

    // 2) Count how many lessons have a watch_history entry for this user/course
    //    We consider a lesson "in progress or done" if there's a row in watch_history
    $sql_watched = "
        SELECT COUNT(DISTINCT lesson_id) AS watched_count 
        FROM watch_history
        WHERE stu_email = ? AND course_id = ?
    ";
    $stmt_watched = $conn->prepare($sql_watched);
    $stmt_watched->bind_param("si", $stuEmail, $course_id);
    $stmt_watched->execute();
    $res_watched = $stmt_watched->get_result()->fetch_assoc();
    $stmt_watched->close();
    $watchedCount = (int) $res_watched['watched_count'];

    $percent = 0;
    if ($watchedCount > 0) {
        $percent = round(($watchedCount / $totalLessons) * 100);
    }

    return [
        'percent' => $percent,
        'watchedCount' => $watchedCount,
        'totalLessons' => $totalLessons
    ];
}

/**
 * Returns the lesson_id the user watched *most recently* in this course,
 * or NULL if no watch_history found.
 */
function getLastWatchedLesson($conn, $course_id, $stuEmail) {
    $sql = "
        SELECT lesson_id 
        FROM watch_history
        WHERE stu_email = ? AND course_id = ?
        ORDER BY id DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $stuEmail, $course_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return (int) $row['lesson_id'];
    }
    return null;
}

/**
 * Check if user is enrolled in the course (returns boolean).
 */
function isUserEnrolled($conn, $stu_id, $course_id) {
    $sql = "SELECT 1 FROM enrollment WHERE student_id = ? AND course_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $stu_id, $course_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
    return ($res->num_rows > 0);
}
?>
