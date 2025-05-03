<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\Admin\lessons.php -->

<?php
if(!isset($_SESSION)){ 
  session_start(); 
}
define('TITLE', 'Lessons');
define('PAGE', 'lessons');

include('./adminInclude/header.php'); 
include('../dbConnection.php');

if(!isset($_SESSION['is_admin_login'])){
  echo "<script> location.href='../index.php'; </script>";
  exit;
}
?>
<div class="col-sm-9 mt-5 mx-3">
  <form action="" class="mt-3 form-inline d-print-none">
    <div class="form-group mr-3">
      <label for="checkid">Enter Course ID: </label>
      <input type="text" class="form-control ml-3" id="checkid" name="checkid" onkeypress="isInputNumber(event)">
    </div>
    <button type="submit" class="btn btn-danger">Search</button>
  </form>
  <?php
    $sql = "SELECT course_id FROM course";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
      if(isset($_REQUEST['checkid']) && $_REQUEST['checkid'] == $row['course_id']){
        $sql2 = "SELECT * FROM course WHERE course_id = {$_REQUEST['checkid']}";
        $result2 = $conn->query($sql2);
        $row2 = $result2->fetch_assoc();
        if(($row2['course_id']) == $_REQUEST['checkid']){
          $_SESSION['course_id'] = $row2['course_id'];
          $_SESSION['course_name'] = $row2['course_name'];

          echo '<h3 class="mt-5 bg-dark text-white p-2">
                 Course ID: '.$row2['course_id'].' 
                 Course Name: '.$row2['course_name'].'
                </h3>';

          $sql3 = "SELECT * FROM lesson WHERE course_id = {$_REQUEST['checkid']}";
          $result3 = $conn->query($sql3);
          if($result3->num_rows > 0){
            echo '<table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Lesson ID</th>
                        <th scope="col">Lesson Name</th>
                        <th scope="col">Lesson Link</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>';
            while($row3 = $result3->fetch_assoc()){
              echo '<tr>
                      <th scope="row">'.$row3["lesson_id"].'</th>
                      <td>'.$row3["lesson_name"].'</td>
                      <td>'.$row3["lesson_link"].'</td>
                      <td>
                        <form action="editlesson.php" method="POST" class="d-inline">
                          <input type="hidden" name="id" value="'.$row3["lesson_id"].'">
                          <button type="submit" class="btn btn-info mr-3" name="view" value="View">
                            <i class="fas fa-pen"></i>
                          </button>
                        </form>
                        <form action="" method="POST" class="d-inline">
                          <input type="hidden" name="id" value="'.$row3["lesson_id"].'">
                          <button type="submit" class="btn btn-secondary" name="delete" value="Delete">
                            <i class="far fa-trash-alt"></i>
                          </button>
                        </form>
                      </td>
                    </tr>';
            }
            echo '</tbody></table>';
          } else {
            echo '<p class="mt-5">No Lessons Found!</p>';
          }
        } else {
          echo '<div class="alert alert-dark mt-4" role="alert">
                  Course Not Found!
                </div>';
        }
      }
    }
    if(isset($_REQUEST['delete'])){
      $sql = "DELETE FROM lesson WHERE lesson_id = {$_REQUEST['id']}";
      if($conn->query($sql) === TRUE){
        echo '<meta http-equiv="refresh" content="0;URL=?deleted" />';
      } else {
        echo "Unable to Delete Data";
      }
    }
  ?>
</div>
<!-- Only Number for input fields -->
<script>
  function isInputNumber(evt) {
    var ch = String.fromCharCode(evt.which);
    if(!(/[0-9]/.test(ch))) {
      evt.preventDefault();
    }
  }
</script>
</div> <!-- Close Row from header -->
<?php if(isset($_SESSION['course_id'])){
  echo '<div><a class="btn btn-danger box" href="addLesson.php"><i class="fas fa-plus fa-2x"></i></a></div>';
} ?>
</div> <!-- Close Container-fluid from header -->

<?php
include('./adminInclude/footer.php');
?>
