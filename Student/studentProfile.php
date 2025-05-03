<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\Student\studentProfile.php -->
<?php
if(!isset($_SESSION)){
  session_start();
}

define('TITLE', 'Student Profile');
define('PAGE', 'profile');

// Include the student header (which contains the sidebar and layout)
include('./stuInclude/header.php');
// Include database connection
include_once('../dbConnection.php');

// Redirect if not logged in
if(!isset($_SESSION['is_login'])){
  echo "<script> location.href='../loginorsignup.php'; </script>";
  exit;
}

$stuEmail = $_SESSION['stuLogEmail'];

// 1) Fetch Student Info
$sql = "SELECT * FROM student WHERE stu_email='$stuEmail'";
$result = $conn->query($sql);
if($result->num_rows == 1){
  $row = $result->fetch_assoc();
  $stuId   = $row["stu_id"];
  $stuName = $row["stu_name"];
  $stuOcc  = $row["stu_occ"];
  $stuImg  = $row["stu_img"];
}

// 2) If the user updates their profile
if(isset($_REQUEST['updateStuNameBtn'])){
  if(trim($_REQUEST['stuName']) == ""){
    $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Fill All Fields</div>';
  } else {
    $stuName = $_REQUEST["stuName"];
    $stuOcc  = $_REQUEST["stuOcc"];
    // Check if a new image is uploaded; otherwise, keep the old image
    if(!empty($_FILES['stuImg']['name'])){
      $stu_image      = $_FILES['stuImg']['name'];
      $stu_image_temp = $_FILES['stuImg']['tmp_name'];
      $img_folder     = '../image/stu/' . $stu_image; 
      move_uploaded_file($stu_image_temp, $img_folder);
    } else {
      $img_folder = $stuImg; // keep old image
    }

    // Update student table
    $sql_update = "UPDATE student 
                   SET stu_name='$stuName', stu_occ='$stuOcc', stu_img='$img_folder'
                   WHERE stu_email='$stuEmail'";
    if($conn->query($sql_update) === TRUE){
      $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert">Updated Successfully</div>';
      // Refresh local variables so the page displays updated info
      $stuImg  = $img_folder;
    } else {
      $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">Unable to Update</div>';
    }
  }
}
?>

<!-- Main Content Area -->
<div class="col-sm-9 mt-5">
  <div class="row mx-3">
    <!-- Left Column: Profile Update Form -->
    <div class="col-md-5">
      <h3 class="text-center">Update Profile</h3>
      <form method="POST" enctype="multipart/form-data" class="mt-3">
        <div class="form-group">
          <label for="stuId">Student ID</label>
          <input type="text" class="form-control" id="stuId" name="stuId"
                 value="<?php if(isset($stuId)) {echo $stuId;} ?>" readonly>
        </div>
        <div class="form-group">
          <label for="stuEmail">Email</label>
          <input type="email" class="form-control" id="stuEmail"
                 value="<?php echo $stuEmail; ?>" readonly>
        </div>
        <div class="form-group">
          <label for="stuName">Name</label>
          <input type="text" class="form-control" id="stuName" name="stuName"
                 value="<?php if(isset($stuName)) {echo $stuName;} ?>">
        </div>
        <div class="form-group">
          <label for="stuOcc">Occupation</label>
          <input type="text" class="form-control" id="stuOcc" name="stuOcc"
                 value="<?php if(isset($stuOcc)) {echo $stuOcc;} ?>">
        </div>
        <div class="form-group">
          <label for="stuImg">Upload Image</label>
          <input type="file" class="form-control-file" id="stuImg" name="stuImg">
          <?php 
          // Show the current profile image if available
          if(!empty($stuImg)): ?>
            <img src="<?php echo $stuImg; ?>" alt="Profile"
                 class="img-thumbnail mt-2" style="width:120px;">
          <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary" name="updateStuNameBtn">Update</button>
        <?php if(isset($passmsg)) {echo $passmsg;} ?>
      </form>
    </div>

    

<?php
// Include the student footer
include('./stuInclude/footer.php');
?>
