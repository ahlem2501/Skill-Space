<?php
// For debugging, turn on error reporting if needed
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

define('TITLE', 'Edit Lesson');
define('PAGE', 'editlesson');
include('./stuInclude/header.php');
include('../dbConnection.php');

// Get student email from session
$stuEmail = $_SESSION['stuLogEmail'];

// Check if lesson_id is provided
if (!isset($_GET['lesson_id'])) {
    echo "<div class='alert alert-danger'>Lesson ID not provided.</div>";
    exit;
}

// Retrieve lesson data joined with course info
$lesson_id = intval($_GET['lesson_id']);
$sql = "SELECT l.*, c.course_name, c.created_by 
        FROM lesson l 
        JOIN course c ON l.course_id = c.course_id 
        WHERE l.lesson_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

// If no lesson found, bail
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Lesson not found.</div>";
    exit;
}
$lesson = $result->fetch_assoc();

// Check permission
if ($lesson['created_by'] !== $stuEmail) {
    echo "<div class='alert alert-danger'>You don't have permission to edit this lesson.</div>";
    exit;
}
$course_id = $lesson['course_id'];
$stmt->close();

$msg = "";

// Process form submission
if (isset($_POST['lessonSubmitBtn'])) {
    if (empty(trim($_POST['lesson_name'])) || empty(trim($_POST['lesson_desc']))) {
        $msg = "<div class='alert alert-warning'>Please fill all fields.</div>";
    } else {
        $lesson_name = trim($_POST['lesson_name']);
        $lesson_desc = trim($_POST['lesson_desc']);
        $link_folder = $lesson['lesson_link']; // keep old video by default

        // Check if user uploaded a new video
        if (!empty($_FILES['lesson_link']['name']) && $_FILES['lesson_link']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['video/mp4', 'video/webm'];
            if (!in_array($_FILES['lesson_link']['type'], $allowed_types)) {
                $msg = "<div class='alert alert-danger'>Only MP4 and WebM videos are allowed.</div>";
            } elseif ($_FILES['lesson_link']['size'] > 50 * 1024 * 1024) {
                $msg = "<div class='alert alert-danger'>Video size must not exceed 50MB.</div>";
            } else {
                $lesson_link = time() . '_' . basename($_FILES['lesson_link']['name']);
                $link_folder = '../lessonvid/' . $lesson_link;
                if (!move_uploaded_file($_FILES['lesson_link']['tmp_name'], $link_folder)) {
                    $msg = "<div class='alert alert-danger'>Failed to upload video.</div>";
                }
            }
        }

        // If no new error, update DB
        if (empty($msg)) {
            $sql_up = "UPDATE lesson 
                       SET lesson_name = ?, lesson_desc = ?, lesson_link = ? 
                       WHERE lesson_id = ?";
            $stmt_up = $conn->prepare($sql_up);
            $stmt_up->bind_param("sssi", $lesson_name, $lesson_desc, $link_folder, $lesson_id);
            if ($stmt_up->execute()) {
                $msg = "<div class='alert alert-success'>Lesson updated successfully!</div>";
                // Redirect back to manage lessons
                header("Location: studentManageCourse.php?course_id=$course_id");
                exit;
            } else {
                $msg = "<div class='alert alert-danger'>Failed to update lesson: " . $conn->error . "</div>";
            }
            $stmt_up->close();
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-auto jumbotron">
    <h3 class="text-center">Edit Lesson: <?php echo htmlspecialchars($lesson['lesson_name']); ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="lesson_name">Lesson Name</label>
            <input 
                type="text" 
                class="form-control" 
                id="lesson_name" 
                name="lesson_name" 
                value="<?php echo htmlspecialchars($lesson['lesson_name']); ?>" 
                required
            >
        </div>
        <div class="form-group">
            <label for="lesson_desc">Lesson Description</label>
            <textarea 
                class="form-control" 
                id="lesson_desc" 
                name="lesson_desc" 
                rows="3" 
                required
            ><?php echo htmlspecialchars($lesson['lesson_desc']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="lesson_link">Lesson Video</label>
            <p>Current Video: 
                <a href="<?php echo $lesson['lesson_link']; ?>" target="_blank">
                    View
                </a>
            </p>
            <input 
                type="file" 
                class="form-control-file" 
                id="lesson_link" 
                name="lesson_link" 
                accept="video/mp4,video/webm"
            >
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger" name="lessonSubmitBtn">Update</button>
            <a href="studentManageCourse.php?course_id=<?php echo $course_id; ?>" 
               class="btn btn-secondary">
                Cancel
            </a>
        </div>
        <?php if (!empty($msg)) echo $msg; ?>
    </form>
</div>

<?php include('./stuInclude/footer.php'); ?>
