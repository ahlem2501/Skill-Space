<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}
define('TITLE', 'Manage Course');
define('PAGE', 'managecourse');
include('./stuInclude/header.php');
include('../dbConnection.php');
$stuEmail = $_SESSION['stuLogEmail'];
if (!isset($_GET['course_id'])) {
    echo "<div class='alert alert-danger'>Course ID not provided.</div>";
    exit;
}
$course_id = intval($_GET['course_id']);
$sql = "SELECT course_name, course_desc FROM course WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Course not found or you don't have permission.</div>";
    exit;
}
$course = $result->fetch_assoc();
$stmt->close();
$sql = "SELECT * FROM lesson WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$lessons = $stmt->get_result();
$stmt->close();
?>
<div class="container mt-5">
    <h3 class="text-center">Manage Course: <?php echo htmlspecialchars($course['course_name']); ?></h3>
    <p><?php echo htmlspecialchars($course['course_desc']); ?></p>
    <div class="d-flex justify-content-between mb-3">
        <a href="studentAddLesson.php?course_id=<?php echo $course_id; ?>" class="btn btn-success">Add Lesson</a>
        <a href="myCourse.php" class="btn btn-secondary">Back to My Courses</a>
    </div>
    <?php if ($lessons->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Lesson ID</th>
                    <th>Lesson Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($lesson = $lessons->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $lesson['lesson_id']; ?></td>
                        <td><?php echo htmlspecialchars($lesson['lesson_name']); ?></td>
                        <td><?php echo htmlspecialchars($lesson['lesson_desc']); ?></td>
                        <td>
                            <a href="studentEditLesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>" class="btn btn-info btn-sm">Edit</a>
                            <a href="deleteLesson.php?lesson_id=<?php echo $lesson['lesson_id']; ?>&course_id=<?php echo $course_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No lessons added yet. Start by adding a lesson!</p>
    <?php endif; ?>
</div>
<?php include('./stuInclude/footer.php'); ?>