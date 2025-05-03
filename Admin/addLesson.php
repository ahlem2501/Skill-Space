<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\Admin\addLesson.php -->
<?php
if (!isset($_SESSION)) {
    session_start();
}

// Define the constants so that the header can use them
define('TITLE', 'Add Lesson');
// Add this line to define PAGE for this page.
define('PAGE', 'addlesson');

include('./adminInclude/header.php');
include('../dbConnection.php');

if (isset($_SESSION['is_admin_login'])) {
    $adminEmail = $_SESSION['adminLogEmail'];
} else {
    echo "<script> location.href='../index.php'; </script>";
}

$msg = "";
if (isset($_REQUEST['lessonSubmitBtn'])) {
    // Checking for Empty Fields
    if (trim($_REQUEST['lesson_name']) == "" || 
        trim($_REQUEST['lesson_desc']) == "" || 
        trim($_REQUEST['course_id']) == "" || 
        trim($_REQUEST['course_name']) == "") {
        // Message displayed if a required field is missing
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fields </div>';
    } else {
        // Assigning user values to variables
        $lesson_name = $_REQUEST['lesson_name'];
        $lesson_desc = $_REQUEST['lesson_desc'];
        $course_id   = $_REQUEST['course_id'];
        $course_name = $_REQUEST['course_name'];
        $lesson_link = $_FILES['lesson_link']['name'];
        $lesson_link_temp = $_FILES['lesson_link']['tmp_name'];
        $link_folder = '../lessonvid/' . $lesson_link;
        
        // Move the uploaded video file to the lessonvid folder
        if (move_uploaded_file($lesson_link_temp, $link_folder)) {
            $sql = "INSERT INTO lesson (lesson_name, lesson_desc, lesson_link, course_id, course_name) 
                    VALUES ('$lesson_name', '$lesson_desc', '$link_folder', '$course_id', '$course_name')";
            if ($conn->query($sql) === TRUE) {
                $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Lesson Added Successfully </div>';
            } else {
                $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Add Lesson </div>';
            }
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> File Upload Failed </div>';
        }
    }
}
?>
<div class="col-sm-6 mt-5 mx-3 jumbotron">
    <h3 class="text-center">Add New Lesson</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input type="text" class="form-control" id="course_id" name="course_id" 
                   value ="<?php if (isset($_SESSION['course_id'])) { echo $_SESSION['course_id']; } ?>" readonly>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" 
                   value ="<?php if (isset($_SESSION['course_name'])) { echo $_SESSION['course_name']; } ?>" readonly>
        </div>
        <div class="form-group">
            <label for="lesson_name">Lesson Name</label>
            <input type="text" class="form-control" id="lesson_name" name="lesson_name">
        </div>
        <div class="form-group">
            <label for="lesson_desc">Lesson Description</label>
            <textarea class="form-control" id="lesson_desc" name="lesson_desc" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label for="lesson_link">Lesson Video Link</label>
            <input type="file" class="form-control-file" id="lesson_link" name="lesson_link">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger" id="lessonSubmitBtn" name="lessonSubmitBtn">Submit</button>
            <a href="lessons.php" class="btn btn-secondary">Close</a>
        </div>
        <?php if (isset($msg)) { echo $msg; } ?>
    </form>
</div>
<!-- Only Number for input fields -->
<script>
    function isInputNumber(evt) {
        var ch = String.fromCharCode(evt.which);
        if (!(/[0-9]/.test(ch))) {
            evt.preventDefault();
        }
    }
</script>
</div>  <!-- div Row close from header -->
</div>  <!-- div Container-fluid close from header -->

<?php
include('./adminInclude/footer.php'); 
?>

