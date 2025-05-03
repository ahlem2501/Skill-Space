<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

include('../dbConnection.php');

$stuEmail = $_SESSION['stuLogEmail'];

// Verify course_id is provided
if (!isset($_GET['course_id'])) {
    header("Location: ../courses.php");
    exit;
}
$course_id = intval($_GET['course_id']);

// Get student's ID
$sql = "SELECT stu_id FROM student WHERE stu_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows < 1) {
    echo "<div class='alert alert-danger'>Student not found.</div>";
    exit;
}
$stu_row = $result->fetch_assoc();
$stu_id = $stu_row['stu_id'];
$stmt->close();

// Ensure user is enrolled in the course
$sql_enroll = "INSERT IGNORE INTO enrollment (student_id, course_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql_enroll);
$stmt->bind_param("ii", $stu_id, $course_id);
$stmt->execute();
$stmt->close();

// Determine the lesson_id to display
$lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;

// Fetch all lessons for the sidebar
$sql_lessons = "SELECT * FROM lesson WHERE course_id = ? ORDER BY lesson_id ASC";
$stmt = $conn->prepare($sql_lessons);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$res_lessons = $stmt->get_result();

// If no lesson_id provided, select the first lesson
if ($lesson_id == 0 && $res_lessons->num_rows > 0) {
    $temp = $res_lessons->fetch_assoc();
    $lesson_id = $temp['lesson_id'];
    $res_lessons->data_seek(0); // Reset pointer for sidebar loop
}

// Fetch current lesson data
$sql_curr = "SELECT * FROM lesson WHERE lesson_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql_curr);
$stmt->bind_param("ii", $lesson_id, $course_id);
$stmt->execute();
$res_curr = $stmt->get_result();
if ($res_curr->num_rows < 1) {
    echo "<div class='alert alert-danger'>Lesson not found for this course.</div>";
    exit;
}
$lesson = $res_curr->fetch_assoc();
$stmt->close();

// Fetch last_position for the current lesson
$sql_hist = "SELECT last_position FROM watch_history WHERE stu_email = ? AND course_id = ? AND lesson_id = ?";
$stmt = $conn->prepare($sql_hist);
$stmt->bind_param("sii", $stuEmail, $course_id, $lesson_id);
$stmt->execute();
$res_hist = $stmt->get_result();
$last_position = 0;
if ($res_hist->num_rows > 0) {
    $last_position = (int)$res_hist->fetch_assoc()['last_position'];
}
$stmt->close();

// Fetch watch history for all lessons in the course
$sql_hist_all = "SELECT lesson_id, last_position FROM watch_history WHERE stu_email = ? AND course_id = ?";
$stmt_hist_all = $conn->prepare($sql_hist_all);
$stmt_hist_all->bind_param("si", $stuEmail, $course_id);
$stmt_hist_all->execute();
$res_hist_all = $stmt_hist_all->get_result();
$watch_history = [];
while ($row = $res_hist_all->fetch_assoc()) {
    $watch_history[$row['lesson_id']] = (int)$row['last_position'];
}
$stmt_hist_all->close();

// Convert last_position to minutes and seconds for display
$mins = floor($last_position / 60);
$secs = $last_position % 60;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Watch Course</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Currently Watching: <?php echo htmlspecialchars($lesson['lesson_name']); ?></h3>
    <?php if ($last_position > 0): ?>
        <p class="text-info">
            You left off at 
            <strong><?php echo $mins; ?>m : <?php echo $secs; ?>s</strong>. 
            We’ll resume from there automatically.
        </p>
    <?php endif; ?>

    <div class="row">
        <!-- Video Player Column -->
        <div class="col-md-8">
            <?php if (!empty($lesson['lesson_link']) && file_exists($lesson['lesson_link'])): ?>
              <video id="myPlayer" width="100%" controls>
                  <source src="<?php echo htmlspecialchars($lesson['lesson_link']); ?>" type="video/mp4">
                  Your browser does not support HTML5 video.
              </video>
            <?php else: ?>
                <div class="alert alert-warning">Video file not found.</div>
            <?php endif; ?>

            <p class="mt-3"><?php echo nl2br(htmlspecialchars($lesson['lesson_desc'])); ?></p>
        </div>

        <!-- Lesson Sidebar Column -->
        <div class="col-md-4">
            <h5>All Lessons in This Course</h5>
            <ul class="list-group">
                <?php
                $res_lessons->data_seek(0); // Reset pointer for the loop
                while ($l = $res_lessons->fetch_assoc()):
                    $lesson_id_sidebar = $l['lesson_id'];
                    $watched_time = isset($watch_history[$lesson_id_sidebar]) ? $watch_history[$lesson_id_sidebar] : 0;
                    $mins_sidebar = floor($watched_time / 60);
                    $secs_sidebar = $watched_time % 60;
                    $watched_display = $watched_time > 0 ? " (Last watched: {$mins_sidebar}m {$secs_sidebar}s)" : "";
                ?>
                    <li class="list-group-item <?php echo ($l['lesson_id'] == $lesson_id) ? 'active' : ''; ?>">
                        <a href="watchcourse.php?course_id=<?php echo $course_id; ?>&lesson_id=<?php echo $l['lesson_id']; ?>"
                           class="<?php echo ($l['lesson_id'] == $lesson_id) ? 'text-white' : ''; ?>">
                            <?php echo htmlspecialchars($l['lesson_name']) . $watched_display; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>

            <a href="myCourse.php" class="btn btn-secondary mt-3">
                Back to My Courses
            </a>
        </div>
    </div>
</div>

<script src="../js/jquery.min.js"></script>
<script>
// Video resume and history update logic
const video = document.getElementById('myPlayer');
if (video) {
    const lastPosition = <?php echo $last_position; ?>;
    let isResumeAttempted = false;
    let resumeTimeout;
    
    // First attempt: Set time on loadedmetadata
    video.addEventListener('loadedmetadata', function() {
        if (lastPosition > 0) {
            // Clear any previous timeout
            clearTimeout(resumeTimeout);
            video.currentTime = lastPosition;
            isResumeAttempted = true;
            console.log('Resume attempt 1: Set currentTime to ' + lastPosition);
            
            // Set a backup timeout in case the first attempt doesn't work
            resumeTimeout = setTimeout(() => {
                if (video.currentTime < lastPosition) {
                    video.currentTime = lastPosition;
                    console.log('Resume attempt 2: Set currentTime to ' + lastPosition);
                }
            }, 1000);
        }
    });
    
    // Second attempt: After play starts
    video.addEventListener('play', function() {
        if (lastPosition > 0 && !isResumeAttempted) {
            video.currentTime = lastPosition;
            console.log('Resume attempt 3: Set currentTime on play to ' + lastPosition);
            isResumeAttempted = true;
        }
    });
    
    // Third attempt: On canplay
    video.addEventListener('canplay', function() {
        if (lastPosition > 0 && video.currentTime < 1) {
            video.currentTime = lastPosition;
            console.log('Resume attempt 4: Set currentTime on canplay to ' + lastPosition);
        }
    });
    
    // Handle watch history updates
    let lastSavedPosition = 0;
    function updateHistory() {
        $.post('watchcourseAjax.php', {
            action: 'updateHistory',
            course_id: '<?php echo $course_id; ?>',
            lesson_id: '<?php echo $lesson_id; ?>',
            currentTime: Math.floor(video.currentTime)
        }, function(data) {
            console.log('History updated: ' + data);
        });
    }
    
    video.addEventListener('timeupdate', function() {
        if (Math.abs(video.currentTime - lastSavedPosition) >= 5) {
            console.log('Updating history at ' + video.currentTime);
            updateHistory();
            lastSavedPosition = video.currentTime;
        }
    });
    
    video.addEventListener('pause', updateHistory);
    video.addEventListener('ended', updateHistory);
}
</script>
</body>
</html>