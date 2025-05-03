<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

// Page constants
define('TITLE', 'My Courses');
define('PAGE', 'mycourse');

// Includes
include('./stuInclude/header.php');
include('../dbConnection.php');
include('../functions.php'); // <-- your utility file from above

$stuEmail = $_SESSION['stuLogEmail'];

// Get student info
$sql = "SELECT stu_id FROM student WHERE stu_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    echo "<div class='alert alert-danger'>Student not found.</div>";
    exit;
}
$row = $result->fetch_assoc();
$stu_id = $row['stu_id'];
$stmt->close();

// Courses the user created
$sql_created = "SELECT * FROM course WHERE created_by = ?";
$stmt = $conn->prepare($sql_created);
$stmt->bind_param("s", $stuEmail);
$stmt->execute();
$res_created = $stmt->get_result();
$stmt->close();

// Courses the user enrolled in
$sql_enroll = "
    SELECT c.* 
    FROM course c
    JOIN enrollment e ON c.course_id = e.course_id
    WHERE e.student_id = ?
";
$stmt = $conn->prepare($sql_enroll);
$stmt->bind_param("i", $stu_id);
$stmt->execute();
$res_enroll = $stmt->get_result();
$stmt->close();

// Handle deleting a created course
if (isset($_GET['del_id'])) {
    $delID = intval($_GET['del_id']);
    $sql_del = "DELETE FROM course WHERE course_id = ? AND created_by = ?";
    $stmt_del = $conn->prepare($sql_del);
    $stmt_del->bind_param("is", $delID, $stuEmail);
    if ($stmt_del->execute()) {
        echo "<script>alert('Course deleted!'); location.href='myCourse.php';</script>";
    } else {
        echo "<script>alert('Failed to delete course: " . $conn->error . "'); location.href='myCourse.php';</script>";
    }
    $stmt_del->close();
    exit;
}
?>

<div class="col-sm-9 mt-5">
    <h2 class="text-center">My Courses</h2>
    <hr/>

    <!-- Section: Courses I Created -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Courses I Created</h4>
            <a href="studentAddCourse.php" class="btn btn-success btn-sm">Add New Course</a>
        </div>
        <?php if ($res_created->num_rows > 0): ?>
            <div class="row mt-3">
                <?php while ($cr = $res_created->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="<?php echo $cr['course_img']; ?>" 
                                 class="card-img-top" 
                                 alt="Course Image" 
                                 style="height: 180px; object-fit: cover;"
                            />
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($cr['course_name']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($cr['course_desc']); ?>
                                </p>
                                <p class="card-text">
                                    <strong>Status:</strong> 
                                    <?php echo ucfirst($cr['status']); ?>
                                </p>
                                <!-- Buttons -->
                                <a href="../coursedetails.php?course_id=<?php echo $cr['course_id']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    View
                                </a>
                                <a href="studentEditCourse.php?course_id=<?php echo $cr['course_id']; ?>" 
                                   class="btn btn-info btn-sm">
                                    Edit
                                </a>
                                <a href="studentManageCourse.php?course_id=<?php echo $cr['course_id']; ?>" 
                                   class="btn btn-warning btn-sm">
                                    Manage Lessons
                                </a>

                                <!-- Publish / Unpublish -->
                                <?php if ($cr['status'] == 'draft'): ?>
                                    <a href="publishCourse.php?course_id=<?php echo $cr['course_id']; ?>" 
                                       class="btn btn-success btn-sm">
                                        Publish
                                    </a>
                                <?php else: ?>
                                    <a href="unpublishCourse.php?course_id=<?php echo $cr['course_id']; ?>" 
                                       class="btn btn-secondary btn-sm">
                                        Unpublish
                                    </a>
                                <?php endif; ?>

                                <!-- Delete -->
                                <a href="?del_id=<?php echo $cr['course_id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Are you sure?');">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>You haven't created any courses yet.</p>
        <?php endif; ?>
    </div>

    <hr/>

    <!-- Section: My Enrolled Courses -->
    <div>
        <h4>My Enrolled Courses</h4>
        <?php if ($res_enroll->num_rows > 0): ?>
            <div class="row mt-3">
                <?php while ($en = $res_enroll->fetch_assoc()): ?>
                    <?php
                    // Calculate progress
                    $progressData = getCourseProgress($conn, $en['course_id'], $stuEmail);
                    $progressPercent = $progressData['percent'];

                    // Determine last watched lesson for the "Resume" link
                    $lastLesson = getLastWatchedLesson($conn, $en['course_id'], $stuEmail);
                    // If no last lesson found, fall back to watchcourse with no lesson_id
                    $resumeLink = 'watchcourse.php?course_id=' . $en['course_id'];
                    if ($lastLesson) {
                        $resumeLink .= '&lesson_id=' . $lastLesson;
                    }
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?php echo $en['course_img']; ?>" 
                                 class="card-img-top" 
                                 alt="Course Image" 
                                 style="height: 180px; object-fit: cover;"
                            />
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($en['course_name']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($en['course_desc']); ?>
                                </p>

                                <!-- Progress Bar -->
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar" 
                                         role="progressbar" 
                                         style="width: <?php echo $progressPercent; ?>%;" 
                                         aria-valuenow="<?php echo $progressPercent; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo $progressPercent; ?>%
                                    </div>
                                </div>

                                <!-- Resume Button -->
                                <a href="<?php echo $resumeLink; ?>" 
                                   class="btn btn-success btn-sm">
                                    <?php echo ($progressPercent > 0 && $progressPercent < 100) ? 'Resume' : 'Start'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>You haven't enrolled in any courses yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('./stuInclude/footer.php'); ?>
