<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\index.php -->
<?php
// index.php

session_start();
include('./dbConnection.php');
include('./mainInclude/header.php');
include('./functions.php'); // utility

$userEmail = isset($_SESSION['stuLogEmail']) ? $_SESSION['stuLogEmail'] : '';
$stu_id = 0;

// If user is logged in, get student ID
if ($userEmail) {
    $stmtStu = $conn->prepare("SELECT stu_id FROM student WHERE stu_email = ? LIMIT 1");
    $stmtStu->bind_param("s", $userEmail);
    $stmtStu->execute();
    $resStu = $stmtStu->get_result();
    if ($resStu->num_rows > 0) {
        $stu_id = (int)$resStu->fetch_assoc()['stu_id'];
    }
    $stmtStu->close();
}
?>
<!-- Start Video Background -->
<div class="container-fluid remove-vid-marg">
    <div class="vid-parent">
        <video playsinline autoplay muted loop>
            <source src="video/banvid.mp4" type="video/mp4" />
        </video>
        <div class="vid-overlay"></div>
    </div>
    <div class="vid-content">
        <h1 class="my-content">Welcome to EduCode</h1>
        <small class="my-content">Learn and Implement</small><br />
        <?php
        if (!$userEmail) {
            echo '<a class="btn btn-danger mt-3" href="#" data-toggle="modal" data-target="#stuRegModalCenter">Get Started</a>';
        } else {
            echo '<a class="btn btn-primary mt-3" href="student/studentProfile.php">My Profile</a>';
        }
        ?>
    </div>
</div>
<!-- End Video Background -->

<!-- Start Text Banner -->
<div class="container-fluid bg-danger txt-banner">
    <div class="row bottom-banner text-white text-center">
        <div class="col-sm">
            <h5><i class="fas fa-book-open mr-3"></i> 100+ Online Courses</h5>
        </div>
        <div class="col-sm">
            <h5><i class="fas fa-users mr-3"></i> Expert Instructors</h5>
        </div>
        <div class="col-sm">
            <h5><i class="fas fa-keyboard mr-3"></i> Lifetime Access</h5>
        </div>
        <div class="col-sm">
            <h5><i class="fas fa-graduation-cap mr-3"></i> Get Skills</h5>
        </div>
    </div>
</div>
<!-- End Text Banner -->

<!-- Start Most Popular Courses -->
<div class="container mt-5">
    <h1 class="text-center">Popular Courses</h1>
    
    <!-- Category Filter Form -->
    <form method="GET" action="" class="mt-4">
        <!-- ... same logic as before if you want category filters ... -->
    </form>

    <div class="row mt-4">
        <?php
        // E.g. get some "popular" courses, or just the first N:
        $sqlPopular = "SELECT * FROM course LIMIT 6";
        $resPop = $conn->query($sqlPopular);

        if ($resPop && $resPop->num_rows > 0) {
            while ($cRow = $resPop->fetch_assoc()) {
                $course_id   = $cRow['course_id'];
                $course_img  = str_replace('..', '.', $cRow['course_img']);
                $course_name = htmlspecialchars($cRow['course_name']);
                $course_desc = htmlspecialchars(substr($cRow['course_desc'], 0, 80)) . '...';
                $original_price = $cRow['course_original_price'];
                $price = $cRow['course_price'];
                $creator = $cRow['created_by'] ?? '';

                echo '<div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img 
                                src="' . $course_img . '" 
                                class="card-img-top" 
                                alt="' . $course_name . '" 
                                style="height: 200px; object-fit: cover;" 
                            />
                            <div class="card-body">
                                <h5 class="card-title">' . $course_name . '</h5>
                                <p class="card-text">' . $course_desc . '</p>
                            </div>
                            <div class="card-footer">
                                <p class="card-text d-inline">
                                    Price: 
                                    <small><del>$' . $original_price . '</del></small> 
                                    <span class="font-weight-bolder">$' . $price . '</span>
                                </p>';

                if ($stu_id > 0 && isUserEnrolled($conn, $stu_id, $course_id)) {
                    // Enrolled => show resume
                    $progressData = getCourseProgress($conn, $course_id, $userEmail);
                    $progressPercent = $progressData['percent'];
                    $lastLesson = getLastWatchedLesson($conn, $course_id, $userEmail);
                    $resumeLink = 'student/watchcourse.php?course_id=' . $course_id;
                    if ($lastLesson) {
                        $resumeLink .= '&lesson_id=' . $lastLesson;
                    }

                    echo '<div class="mt-2">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width: ' . $progressPercent . '%;"
                                     aria-valuenow="' . $progressPercent . '"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                     ' . $progressPercent . '%
                                </div>
                            </div>
                          </div>';

                    echo '<a class="btn btn-success text-white font-weight-bolder float-right mt-2"
                              href="' . $resumeLink . '">
                              ' . ($progressPercent > 0 && $progressPercent < 100 ? 'Resume' : 'Start') . '
                          </a>';
                } else {
                    // Not enrolled => normal "Enroll"
                    echo '<a class="btn btn-primary text-white font-weight-bolder float-right"
                              href="coursedetails.php?course_id=' . $course_id . '">
                              Enroll
                          </a>';
                }

                echo '</div></div></div>';
            }
        } else {
            echo '<div class="col-12"><p class="text-center">No popular courses found.</p></div>';
        }
        ?>
    </div>

    <!-- View All Courses Button -->
    <div class="text-center mt-3">
        <a class="btn btn-danger" href="courses.php">View All Courses</a>
    </div>
</div>
<!-- End Most Popular Courses -->
<!-- Start Students Testimonial -->
<div class="container-fluid mt-5" style="background-color: #4B7289" id="Feedback">
        <h1 class="text-center testyheading p-4"> Student's Feedback </h1>
        <div class="row">
          <div class="col-md-12">
            <div id="testimonial-slider" class="owl-carousel">
            <?php 
              $sql = "SELECT s.stu_name, s.stu_occ, s.stu_img, f.f_content FROM student AS s JOIN feedback AS f ON s.stu_id = f.stu_id";
              $result = $conn->query($sql);
              if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()){
                  $s_img = $row['stu_img'];
                  $n_img = str_replace('../','',$s_img)
            ?>
              <div class="testimonial">
                <p class="description">
                <?php echo $row['f_content'];?>  
                </p>
                <div class="pic">
                  <img src="<?php echo $n_img; ?>" alt=""/>
                </div>
                <div class="testimonial-prof">
                  <h4><?php echo $row['stu_name']; ?></h4>
                  <small><?php echo $row['stu_occ']; ?></small>
                </div>
              </div>
              <?php }} ?>
            </div>
          </div>
        </div>
    </div>  <!-- End Students Testimonial -->

<?php include('./contact.php'); ?>
<!-- The rest of index page goes here (testimonials, etc.) -->
<?php include('./mainInclude/footer.php'); ?>
