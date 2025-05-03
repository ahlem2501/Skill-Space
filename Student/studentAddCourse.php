<!-- ELearning\Student\studentAddCourse.php -->
<?php
session_start();
if (!isset($_SESSION['is_login'])) {
    header("Location: ../loginorsignup.php");
    exit;
}

define('TITLE', 'Add Course');
define('PAGE', 'addcourse');
include('./stuInclude/header.php');
include('../dbConnection.php');

$stuEmail = $_SESSION['stuLogEmail'];
$msg = "";

if (isset($_POST['courseSubmitBtn'])) {
    $required_fields = [
        'course_name' => 'Course Name',
        'course_desc' => 'Course Description',
        'course_author' => 'Author',
        'course_duration' => 'Course Duration',
        'course_price' => 'Selling Price',
        'course_original_price' => 'Original Price',
        'category_id' => 'Category',
        'course_status' => 'Status'
    ];

    foreach ($required_fields as $field => $label) {
        if (empty(trim($_POST[$field]))) {
            $msg = "<div class='alert alert-warning'>Please fill the $label field.</div>";
            break;
        }
    }

    if (empty($msg)) {
        $course_price = floatval($_POST['course_price']);
        $course_original_price = floatval($_POST['course_original_price']);
        if ($course_price < 0 || $course_original_price < 0) {
            $msg = "<div class='alert alert-warning'>Prices must be non-negative.</div>";
        } elseif (!isset($_FILES['course_img']) || $_FILES['course_img']['error'] !== UPLOAD_ERR_OK) {
            $msg = "<div class='alert alert-warning'>Please upload a course image.</div>";
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['course_img']['type'], $allowed_types)) {
                $msg = "<div class='alert alert-danger'>Only JPEG, PNG, and GIF images are allowed.</div>";
            } elseif ($_FILES['course_img']['size'] > 5 * 1024 * 1024) { // 5MB limit
                $msg = "<div class='alert alert-danger'>Image size must not exceed 5MB.</div>";
            } else {
                $course_name = trim($_POST['course_name']);
                $course_desc = trim($_POST['course_desc']);
                $course_author = trim($_POST['course_author']);
                $course_duration = trim($_POST['course_duration']);
                $category_id = intval($_POST['category_id']);
                $course_status = trim($_POST['course_status']);

                $course_image = time() . '_' . basename($_FILES['course_img']['name']);
                $img_folder = '../image/courseimg/' . $course_image;
                if (move_uploaded_file($_FILES['course_img']['tmp_name'], $img_folder)) {
                    $sql = "INSERT INTO course (course_name, course_desc, course_author, course_img, course_duration, course_price, course_original_price, category_id, created_by, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssddiss", $course_name, $course_desc, $course_author, $img_folder, $course_duration, $course_price, $course_original_price, $category_id, $stuEmail, $course_status);
                    if ($stmt->execute()) {
                        $course_id = $conn->insert_id;
                        $msg = "<div class='alert alert-success'>Course added successfully!</div>";
                        header("Location: studentManageCourse.php?course_id=$course_id");
                        exit;
                    } else {
                        $msg = "<div class='alert alert-danger'>Failed to add course: " . $conn->error . "</div>";
                    }
                    $stmt->close();
                } else {
                    $msg = "<div class='alert alert-danger'>Failed to upload image.</div>";
                }
            }
        }
    }
}
?>

<div class="col-sm-6 mt-5 mx-auto jumbotron">
    <h3 class="text-center">Add New Course</h3>
    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="form-group">
            <label for="course_desc">Course Description</label>
            <textarea class="form-control" id="course_desc" name="course_desc" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="course_author">Author</label>
            <input type="text" class="form-control" id="course_author" name="course_author" required>
        </div>
        <div class="form-group">
            <label for="course_duration">Course Duration</label>
            <input type="text" class="form-control" id="course_duration" name="course_duration" required placeholder="e.g., 4 weeks">
        </div>
        <div class="form-group">
            <label for="course_original_price">Original Price ($)</label>
            <input type="number" step="0.01" class="form-control" id="course_original_price" name="course_original_price" required min="0">
        </div>
        <div class="form-group">
            <label for="course_price">Selling Price ($)</label>
            <input type="number" step="0.01" class="form-control" id="course_price" name="course_price" required min="0">
        </div>
        <div class="form-group">
            <label for="course_img">Course Image</label>
            <input type="file" class="form-control-file" id="course_img" name="course_img" required accept="image/jpeg,image/png,image/gif" onchange="previewImage(event)">
            <img id="imagePreview" class="mt-2" style="max-width: 200px; display: none;" alt="Image Preview">
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select class="form-control" id="category_id" name="category_id" required>
                <option value="">Select a Category</option>
                <?php
                $sql = "SELECT * FROM category";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['category_id'] . '">' . htmlspecialchars($row['category_name']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="course_status">Status</label>
            <select class="form-control" id="course_status" name="course_status" required>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-danger" name="courseSubmitBtn">Submit</button>
            <a href="myCourse.php" class="btn btn-secondary">Cancel</a>
        </div>
        <?php if (!empty($msg)) echo $msg; ?>
    </form>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.style.display = 'block';
}
function validateForm() {
    const price = document.getElementById('course_price').value;
    const originalPrice = document.getElementById('course_original_price').value;
    if (price < 0 || originalPrice < 0) {
        alert('Prices must be non-negative.');
        return false;
    }
    return true;
}
</script>

<?php include('./stuInclude/footer.php'); ?>