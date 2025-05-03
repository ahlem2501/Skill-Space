<?php
// courses.php

session_start();
include(__DIR__ . '/dbConnection.php');
include(__DIR__ . '/mainInclude/header.php');
include(__DIR__ . '/functions.php'); // Updated to point to the same directoryl

$userEmail = isset($_SESSION['stuLogEmail']) ? $_SESSION['stuLogEmail'] : '';
$stu_id = 0;

// If logged in, get the student's ID
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

// Search & category filters
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_categories = isset($_GET['categories']) ? $_GET['categories'] : [];

?>

<!-- Start Course Page Banner -->
<div class="container-fluid bg-dark">
    <div class="row">
        <img 
            src="./image/coursebanner.jpg" 
            alt="Courses Banner" 
            style="height: 500px; width: 100%; object-fit: cover; 
                   box-shadow: 0 10px 20px rgba(0,0,0,0.5);" 
        />
    </div>
</div>
<!-- End Course Page Banner -->

<!-- Start All Courses -->
<div class="container mt-5">
    <h1 class="text-center">All Courses</h1>

    <!-- Filter Form -->
    <form method="GET" action="" class="mt-4">
        <div class="row align-items-end">
            <!-- Search Field -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="search">Search Courses</label>
                    <input 
                        class="form-control" 
                        type="text" 
                        name="search" 
                        id="search" 
                        placeholder="Search by name or description" 
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                    >
                </div>
            </div>

            <!-- Category Checkboxes -->
            <div class="col-md-6">
                <div class="form-group">
                    <label>Filter by Categories</label>
                    <div class="d-flex flex-wrap">
                        <?php
                        $sql_categories = "SELECT * FROM category";
                        $result_categories = $conn->query($sql_categories);
                        if ($result_categories && $result_categories->num_rows > 0) {
                            while ($rowCat = $result_categories->fetch_assoc()) {
                                $checked = (in_array($rowCat['category_id'], $selected_categories)) ? 'checked' : '';
                                echo '<div class="form-check form-check-inline mr-3">
                                        <input class="form-check-input" type="checkbox" 
                                               name="categories[]" 
                                               value="' . $rowCat['category_id'] . '" 
                                               id="cat_' . $rowCat['category_id'] . '" 
                                               ' . $checked . '>
                                        <label class="form-check-label" for="cat_' . $rowCat['category_id'] . '">'
                                            . htmlspecialchars($rowCat['category_name']) . 
                                        '</label>
                                      </div>';
                            }
                        } else {
                            echo '<p class="text-muted">No categories available.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Filter Button -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </div>
    </form>

    <!-- Course Cards -->
    <div class="row mt-4">
        <?php
        // Build WHERE clause
        $where_clauses = [];
        if (!empty($searchQuery)) {
            $searchLower = $conn->real_escape_string(strtolower($searchQuery));
            $where_clauses[] = "(LOWER(course_name) LIKE '%$searchLower%' 
                                OR LOWER(course_desc) LIKE '%$searchLower%')";
        }
        if (!empty($selected_categories)) {
            $cats_str = implode(",", array_map('intval', $selected_categories));
            $where_clauses[] = "category_id IN ($cats_str)";
        }
        $where_clause = '';
        if ($where_clauses) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_clauses);
        }

        $sql = "SELECT * FROM course $where_clause";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($rowC = $result->fetch_assoc()) {
                $course_id   = $rowC['course_id'];
                $course_img  = str_replace('..', '.', $rowC['course_img']);
                $course_name = htmlspecialchars($rowC['course_name']);
                $course_desc = htmlspecialchars(substr($rowC['course_desc'], 0, 100)) . '...';
                $original_price = $rowC['course_original_price'];
                $price = $rowC['course_price'];

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
                    // The user is enrolled, show "Resume" + progress bar
                    $progressData = getCourseProgress($conn, $course_id, $userEmail);
                    $progressPercent = $progressData['percent'];
                    $lastLesson = getLastWatchedLesson($conn, $course_id, $userEmail);

                    $resumeLink = 'Student/watchcourse.php?course_id=' . $course_id;
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
                    // Not enrolled => show "Enroll"
                    echo '<a class="btn btn-primary text-white font-weight-bolder float-right"
                              href="coursedetails.php?course_id=' . $course_id . '">
                              Enroll
                          </a>';
                }

                echo '  </div>
                      </div>
                    </div>';
            }
        } else {
            echo '<div class="col-12">
                    <p class="text-center text-muted">No courses found matching your criteria.</p>
                  </div>';
        }
        ?>
    </div>
</div>
<!-- End All Courses -->

<?php 
include(__DIR__ . '/contact.php');
include(__DIR__ . '/mainInclude/footer.php');
?>
