<?php
/*****************************************************
 * studentEditCourse.php
 * 
 * This file allows an authenticated user to edit a course
 * that they created. We define TITLE before including
 * header.php to avoid the "Undefined constant TITLE" error.
 *****************************************************/

// 1) Define TITLE and PAGE *before* including header.php
define('TITLE', 'Edit Course');
define('PAGE', 'studentEditCourse');

// 2) Start the session and check login
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

// 3) Include the DB connection and the header
include('../dbConnection.php');
include('./stuInclude/header.php');

/******************************************************
 * Main Logic
 ******************************************************/

$stuEmail = $_SESSION['stuLogEmail'];
$msg = "";

// Ensure we have a course_id in the URL
if (!isset($_GET['course_id'])) {
    echo "<script>alert('Course ID not provided'); location.href='myCourse.php';</script>";
    exit;
}

$course_id = intval($_GET['course_id']);

// Fetch the course data (verify the user is the creator)
$sql = "SELECT * FROM course WHERE course_id = ? AND created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $stuEmail);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows < 1) {
    echo "<script>alert('Course not found or not authorized'); location.href='myCourse.php';</script>";
    exit;
}
$courseData = $result->fetch_assoc();
$stmt->close();

// Prepare upload directory (if needed)
$upload_dir = '../image/courseimg/';
if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
    $msg = "<div class='alert alert-danger'>Failed to create image directory.</div>";
}

/******************************************************
 * Handle form submission
 ******************************************************/
if (isset($_POST['updateBtn'])) {
    // List of required fields
    $required_fields = [
        'course_name', 
        'course_desc', 
        'course_author', 
        'course_duration', 
        'course_price', 
        'course_original_price', 
        'category_id', 
        'course_status'
    ];

    // Check each required field
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            $msg = "<div class='alert alert-warning'>All fields are required.</div>";
            break;
        }
    }

    // If no error message so far, proceed
    if (empty($msg)) {
        $course_price = floatval($_POST['course_price']);
        $course_original_price = floatval($_POST['course_original_price']);

        // Validate price
        if ($course_price < 0 || $course_original_price < 0) {
            $msg = "<div class='alert alert-warning'>Prices must be non-negative.</div>";
        } else {
            // Collect sanitized inputs
            $course_name     = trim($_POST['course_name']);
            $course_desc     = trim($_POST['course_desc']);
            $course_author   = trim($_POST['course_author']);
            $course_duration = trim($_POST['course_duration']);
            $category_id     = intval($_POST['category_id']);
            $course_status   = trim($_POST['course_status']);

            // Keep old image unless a new one is uploaded
            $img_folder = $courseData['course_img'];

            // Handle new image upload if any
            if (!empty($_FILES['course_img']['name']) && $_FILES['course_img']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['course_img']['type'], $allowed_types)) {
                    $msg = "<div class='alert alert-danger'>Only JPEG, PNG, and GIF images are allowed.</div>";
                } elseif ($_FILES['course_img']['size'] > 5 * 1024 * 1024) {
                    $msg = "<div class='alert alert-danger'>Image size must not exceed 5MB.</div>";
                } else {
                    $course_img = time() . '_' . basename($_FILES['course_img']['name']);
                    $img_folder = $upload_dir . $course_img;
                    if (!move_uploaded_file($_FILES['course_img']['tmp_name'], $img_folder)) {
                        $msg = "<div class='alert alert-danger'>Failed to upload image.</div>";
                        error_log("Image upload failed: " . $_FILES['course_img']['tmp_name']);
                    }
                }
            }

            // If still no error, update the course
            if (empty($msg)) {
                $sql_update = "UPDATE course 
                               SET course_name = ?, 
                                   course_desc = ?, 
                                   course_author = ?, 
                                   course_duration = ?, 
                                   course_price = ?, 
                                   course_original_price = ?, 
                                   category_id = ?, 
                                   course_img = ?, 
                                   status = ?
                               WHERE course_id = ? 
                                 AND created_by = ?";
                $stmt_up = $conn->prepare($sql_update);
                $stmt_up->bind_param(
                    "ssssddisssi",
                    $course_name,
                    $course_desc,
                    $course_author,
                    $course_duration,
                    $course_price,
                    $course_original_price,
                    $category_id,
                    $img_folder,
                    $course_status,
                    $course_id,
                    $stuEmail
                );
                if ($stmt_up->execute()) {
                    $msg = "<div class='alert alert-success'>Course updated successfully!</div>";
                    // Redirect back to myCourse after success
                    header("Location: myCourse.php");
                    exit;
                } else {
                    $msg = "<div class='alert alert-danger'>Could not update course: " . $conn->error . "</div>";
                    error_log("Course update failed: " . $conn->error);
                }
                $stmt_up->close();
            }
        }
    }
}
?>

<!-- Page Content -->
<div class="container mt-5">
    <h3>Edit My Course</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        
        <!-- Course Name -->
        <div class="form-group">
            <label>Course Name</label>
            <input 
                type="text" 
                class="form-control" 
                name="course_name" 
                value="<?php echo htmlspecialchars($courseData['course_name']); ?>" 
                required
            >
        </div>

        <!-- Course Description -->
        <div class="form-group">
            <label>Course Description</label>
            <textarea 
                class="form-control" 
                name="course_desc" 
                rows="3" 
                required
            ><?php echo htmlspecialchars($courseData['course_desc']); ?></textarea>
        </div>

        <!-- Author -->
        <div class="form-group">
            <label>Author</label>
            <input 
                type="text" 
                class="form-control" 
                name="course_author" 
                value="<?php echo htmlspecialchars($courseData['course_author']); ?>" 
                required
            >
        </div>

        <!-- Course Duration -->
        <div class="form-group">
            <label>Course Duration</label>
            <input 
                type="text" 
                class="form-control" 
                name="course_duration" 
                value="<?php echo htmlspecialchars($courseData['course_duration']); ?>" 
                required
            >
        </div>

        <!-- Original Price -->
        <div class="form-group">
            <label>Original Price ($)</label>
            <input 
                type="number" 
                step="0.01" 
                class="form-control" 
                name="course_original_price" 
                value="<?php echo $courseData['course_original_price']; ?>" 
                required 
                min="0"
            >
        </div>

        <!-- Selling Price -->
        <div class="form-group">
            <label>Selling Price ($)</label>
            <input 
                type="number" 
                step="0.01" 
                class="form-control" 
                name="course_price" 
                value="<?php echo $courseData['course_price']; ?>" 
                required 
                min="0"
            >
        </div>

        <!-- Category -->
        <div class="form-group">
            <label>Category</label>
            <select class="form-control" name="category_id" required>
                <?php
                $sql_cat = "SELECT * FROM category";
                $res_cat = $conn->query($sql_cat);
                while ($c = $res_cat->fetch_assoc()) {
                    $selected = ($c['category_id'] == $courseData['category_id']) ? 'selected' : '';
                    echo "<option value='{$c['category_id']}' $selected>" 
                         . htmlspecialchars($c['category_name']) 
                         . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Course Image -->
        <div class="form-group">
            <label>Course Image</label><br>
            <img 
                src="<?php echo $courseData['course_img']; ?>" 
                alt="Course Image" 
                width="150" 
                class="img-thumbnail mb-2"
            >
            <input 
                type="file" 
                class="form-control-file" 
                name="course_img" 
                accept="image/jpeg,image/png,image/gif"
            >
        </div>

        <!-- Course Status -->
        <div class="form-group">
            <label>Status</label>
            <select class="form-control" name="course_status" required>
                <option value="draft" 
                    <?php echo ($courseData['status'] == 'draft') ? 'selected' : ''; ?>>
                    Draft
                </option>
                <option value="published" 
                    <?php echo ($courseData['status'] == 'published') ? 'selected' : ''; ?>>
                    Published
                </option>
            </select>
        </div>

        <!-- Submit & Cancel -->
        <button type="submit" class="btn btn-primary" name="updateBtn">Update</button>
        <a href="myCourse.php" class="btn btn-secondary">Cancel</a>

        <!-- Display any message -->
        <?php if (!empty($msg)) echo "<br>$msg"; ?>
    </form>
</div>

<?php include('./stuInclude/footer.php'); ?>
