
<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\Admin\editcourse.php -->
<?php
session_start();
define('TITLE', 'Edit Course');
include('./adminInclude/header.php');
include('../dbConnection.php');

// Verify admin login
if (!isset($_SESSION['is_admin_login'])) {
    echo "<script> location.href='../index.php'; </script>";
    exit;
}

$msg = "";

// If the view button is pressed, fetch the course details
if (isset($_REQUEST['view']) && isset($_REQUEST['id'])) {
    $cid = $_REQUEST['id'];
    $sql = "SELECT * FROM course WHERE course_id = $cid";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Course Not Found</div>';
    }
}

// Process the update when the form is submitted
if (isset($_REQUEST['requpdate'])) {
    // Validate required fields (except image, which may be optional)
    if (empty($_REQUEST['course_id']) || empty($_REQUEST['course_name']) || empty($_REQUEST['course_desc']) || 
        empty($_REQUEST['course_author']) || empty($_REQUEST['course_duration']) || 
        empty($_REQUEST['course_price']) || empty($_REQUEST['course_original_price'])) {
            
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Fill All Fields</div>';
    } else {
        $cid = $_REQUEST['course_id'];
        $cname = $_REQUEST['course_name'];
        $cdesc = $_REQUEST['course_desc'];
        $cauthor = $_REQUEST['course_author'];
        $cduration = $_REQUEST['course_duration'];
        $cprice = $_REQUEST['course_price'];
        $coriginalprice = $_REQUEST['course_original_price'];
        
        // Check if a new image was uploaded; if not, retain the old image.
        if (!empty($_FILES['course_img']['name'])) {
            $course_image = $_FILES['course_img']['name'];
            $course_image_temp = $_FILES['course_img']['tmp_name'];
            $cimg = '../image/courseimg/' . $course_image;
            move_uploaded_file($course_image_temp, $cimg);
        } else {
            // Retrieve the current image from the database
            $sql_img = "SELECT course_img FROM course WHERE course_id = $cid";
            $result_img = $conn->query($sql_img);
            if ($result_img && $result_img->num_rows > 0) {
                $r = $result_img->fetch_assoc();
                $cimg = $r['course_img'];
            } else {
                $cimg = '';
            }
        }
        
        // Update query (do not update course_id as it is the primary key)
        $sql = "UPDATE course SET 
                  course_name = '$cname', 
                  course_desc = '$cdesc', 
                  course_author = '$cauthor', 
                  course_duration = '$cduration', 
                  course_price = '$cprice', 
                  course_original_price = '$coriginalprice', 
                  course_img = '$cimg' 
                WHERE course_id = '$cid'";
                
        if ($conn->query($sql) === TRUE) {
            $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Updated Successfully</div>';
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to Update</div>';
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-3 jumbotron">
  <h3 class="text-center">Update Course Details</h3>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="course_id">Course ID</label>
      <input type="text" class="form-control" id="course_id" name="course_id" value="<?php echo isset($row['course_id']) ? $row['course_id'] : ''; ?>" readonly>
    </div>
    <div class="form-group">
      <label for="course_name">Course Name</label>
      <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo isset($row['course_name']) ? $row['course_name'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="course_desc">Course Description</label>
      <textarea class="form-control" id="course_desc" name="course_desc" rows="2"><?php echo isset($row['course_desc']) ? $row['course_desc'] : ''; ?></textarea>
    </div>
    <div class="form-group">
      <label for="course_author">Author</label>
      <input type="text" class="form-control" id="course_author" name="course_author" value="<?php echo isset($row['course_author']) ? $row['course_author'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="course_duration">Course Duration</label>
      <input type="text" class="form-control" id="course_duration" name="course_duration" value="<?php echo isset($row['course_duration']) ? $row['course_duration'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="course_original_price">Course Original Price</label>
      <input type="text" class="form-control" id="course_original_price" name="course_original_price" onkeypress="isInputNumber(event)" value="<?php echo isset($row['course_original_price']) ? $row['course_original_price'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="course_price">Course Selling Price</label>
      <input type="text" class="form-control" id="course_price" name="course_price" onkeypress="isInputNumber(event)" value="<?php echo isset($row['course_price']) ? $row['course_price'] : ''; ?>">
    </div>
    <div class="form-group">
      <label for="course_img">Course Image</label>
      <?php if(isset($row['course_img']) && !empty($row['course_img'])) { ?>
      <img src="<?php echo $row['course_img']; ?>" alt="course image" class="img-thumbnail mb-2">
      <?php } ?>
      <input type="file" class="form-control-file" id="course_img" name="course_img">
    </div>
    <div class="text-center">
      <button type="submit" class="btn btn-danger" id="requpdate" name="requpdate">Update</button>
      <a href="courses.php" class="btn btn-secondary">Close</a>
    </div>
    <?php if(isset($msg)) { echo $msg; } ?>
  </form>
</div>

<!-- Only allow number input -->
<script>
function isInputNumber(evt) {
    var ch = String.fromCharCode(evt.which);
    if(!(/[0-9]/.test(ch))) {
      evt.preventDefault();
    }
}
</script>

<?php
include('./adminInclude/footer.php');
?>
