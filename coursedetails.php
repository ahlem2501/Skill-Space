<!-- ELearning\coursedetails.php -->
<?php
session_start();
include('./dbConnection.php');
include('./mainInclude/header.php');

if (!isset($_GET['course_id'])) {
    echo "<div class='alert alert-danger'>Course ID not provided.</div>";
    exit;
}

$course_id = intval($_GET['course_id']);
$_SESSION['course_id'] = $course_id;

$sql = "SELECT * FROM course WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Course not found.</div>";
    exit;
}
$course = $result->fetch_assoc();
$is_creator = (isset($_SESSION['is_login']) && $_SESSION['stuLogEmail'] === $course['created_by']);
$is_admin = (isset($_SESSION['is_admin_login'])); // Assuming admin session variable
$stmt->close();
?>

<!-- Banner -->
<div class="container-fluid bg-dark">
    <div class="row">
        <img src="./image/coursebanner.jpg" alt="courses" style="height:200px; width:100%; object-fit:cover; box-shadow:10px;" />
    </div>
</div>

<!-- Course Details -->
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <img src="<?php echo str_replace('..', '.', $course['course_img']); ?>" class="card-img-top" alt="course image" />
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title">Course Name: <?php echo htmlspecialchars($course['course_name']); ?></h5>
                <p class="card-text">Description: <?php echo htmlspecialchars($course['course_desc']); ?></p>
                <p class="card-text">Duration: <?php echo htmlspecialchars($course['course_duration']); ?></p>
                <p class="card-text d-inline">
                    Price: <small><del>$ <?php echo $course['course_original_price']; ?></del></small>
                    <span class="font-weight-bolder">$ <?php echo $course['course_price']; ?></span>
                </p>
                <a href="Student/watchcourse.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-primary text-white font-weight-bolder float-right">
                    Start
                </a>
                <?php if ($is_creator || $is_admin): ?>
                    <a href="Student/studentEditCourse.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-info text-white font-weight-bolder float-right mr-2">
                        Edit
                    </a>
                <?php endif; ?>
                <?php if ($is_creator): ?>
                    <a href="Student/studentAddLesson.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-success text-white font-weight-bolder float-right mr-2">
                        Add Lesson
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lesson List -->
<div class="container">
    <div class="row">
        <?php
        $sql_lessons = "SELECT * FROM lesson WHERE course_id = ?";
        $stmt_lessons = $conn->prepare($sql_lessons);
        $stmt_lessons->bind_param("i", $course_id);
        $stmt_lessons->execute();
        $result_lessons = $stmt_lessons->get_result();

        if ($result_lessons && $result_lessons->num_rows > 0) {
            echo '<table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Lesson No.</th>
                            <th scope="col">Lesson Name</th>
                        </tr>
                    </thead>
                    <tbody>';
            $num = 0;
            while ($lesson = $result_lessons->fetch_assoc()) {
                $num++;
                echo '<tr>
                        <th scope="row">' . $num . '</th>
                        <td>' . htmlspecialchars($lesson['lesson_name']) . '</td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No lessons available for this course.</p>';
        }
        $stmt_lessons->close();
        ?>
    </div>
</div>

<?php include('./mainInclude/footer.php'); ?>