<!-- C:\xampp\htdocs\Educode_update\Educode\ELearning\dbConnection.php -->
<?php
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "lms_db1";

// Create Connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
// $conn = new mysqli($db_host, $db_user, $db_password, $db_name);
// Check Connection
if($conn->connect_error) {
 die("connection failed");
} 
// else {
//  echo"connected";
// }
?>