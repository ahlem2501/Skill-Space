<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

define('TITLE', 'Add Lesson');
define('PAGE', 'addlesson');
include('./stuInclude/header.php');
include('../dbConnection.php');

$stuEmail = $_SESSION['stuLogEmail'];
if (!isset($_GET['course_id'])) {
    echo "<div class='alert alert-danger'>Course ID not provided.</div>";
    exit;
}

$course_id = intval($_GET['course_id']);
$sql = "SELECT course_name FROM course WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Course not found or you don't have permission.</div>";
    exit;
}
$course = $result->fetch_assoc();
$course_name = $course['course_name'];
$stmt->close();

$msg = "";
$upload_dir = '../lessonvid/';
if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
    $msg = "<div class='alert alert-danger'>Failed to create upload directory.</div>";
}

if (isset($_POST['lessonSubmitBtn'])) {
    set_time_limit(300); // 5-minute timeout for large uploads
    if (empty(trim($_POST['lesson_name'])) || empty(trim($_POST['lesson_desc']))) {
        $msg = "<div class='alert alert-warning'>Please fill all fields.</div>";
    } elseif (!isset($_FILES['lesson_link']) || $_FILES['lesson_link']['error'] !== UPLOAD_ERR_OK) {
        $msg = "<div class='alert alert-warning'>Please upload a lesson video.</div>";
    } else {
        $allowed_types = ['video/mp4', 'video/webm'];
        if (!in_array($_FILES['lesson_link']['type'], $allowed_types)) {
            $msg = "<div class='alert alert-danger'>Only MP4 and WebM videos are allowed.</div>";
        } elseif ($_FILES['lesson_link']['size'] > 50 * 1024 * 1024) { // 50MB limit
            $msg = "<div class='alert alert-danger'>Video size must not exceed 50MB.</div>";
        } else {
            $lesson_name = trim($_POST['lesson_name']);
            $lesson_desc = trim($_POST['lesson_desc']);
            $lesson_link = time() . '_' . basename($_FILES['lesson_link']['name']);
            $link_folder = $upload_dir . $lesson_link;

            if (move_uploaded_file($_FILES['lesson_link']['tmp_name'], $link_folder)) {
                $sql = "INSERT INTO lesson (lesson_name, lesson_desc, lesson_link, course_id, course_name) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssis", $lesson_name, $lesson_desc, $link_folder, $course_id, $course_name);
                if ($stmt->execute()) {
                    $msg = "<div class='alert alert-success'>Lesson added successfully!</div>";
                    header("Location: studentManageCourse.php?course_id=$course_id");
                    exit;
                } else {
                    $msg = "<div class='alert alert-danger'>Failed to add lesson: " . $conn->error . "</div>";
                    error_log("Lesson insert failed: " . $conn->error);
                }
                $stmt->close();
            } else {
                $msg = "<div class='alert alert-danger'>Failed to upload video. Check server permissions.</div>";
                error_log("Video upload failed: " . $_FILES['lesson_link']['tmp_name']);
            }
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-auto jumbotron">
    <h3 class="text-center">Add New Lesson to <?php echo htmlspecialchars($course_name); ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" id="lesson_name" name="lesson_name" required>
        </div>
        <div class="form-group">
            <label for="lesson_desc">Lesson Description</label>
            <textarea class="form-control" id="lesson_desc" name="lesson_desc" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="lesson_link">Lesson Video</label>
            <input type="file" class="form-control-file" id="lesson_link" name="lesson_link" required accept="video/mp4,video/webm">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger" name="lessonSubmitBtn">Submit</button>
            <a href="studentManageCourse.php?course_id=<?php echo $course_id; ?>" class="btn btn-secondary">Cancel</a>
        </div>
        <?php if (!empty($msg)) echo $msg; ?>
    </form>
</div>

<?php include('./stuInclude/footer.php'); ?>