<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    
    <!-- Use absolute paths starting with /ELearning/... -->
    <link rel="stylesheet" type="text/css" href="/ELearning/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/ELearning/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="/ELearning/css/owl.min.css">
    <link rel="stylesheet" type="text/css" href="/ELearning/css/owl.theme.min.css">
    <link rel="stylesheet" type="text/css" href="/ELearning/css/testyslider.css">
    <link rel="stylesheet" type="text/css" href="/ELearning/css/style.css" />

    <title>EduCode</title>
  </head>
  <body>
    <!-- Start Navigation -->
    <nav class="navbar navbar-expand-sm navbar-dark pl-5 fixed-top">
      <a href="/ELearning/index.php" class="navbar-brand">EduCode</a>
      <span class="navbar-text">Learn and Implement</span>
      <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#myMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="myMenu">
        <ul class="navbar-nav pl-5 custom-nav">
          <li class="nav-item custom-nav-item"><a href="/ELearning/index.php" class="nav-link">Home</a></li>
          <li class="nav-item custom-nav-item"><a href="/ELearning/courses.php" class="nav-link">Courses</a></li>

          <?php 
              // If session not started, do so
              if (session_status() == PHP_SESSION_NONE) {
                  session_start();
              }
              if (isset($_SESSION['is_login'])) {
                  echo '<li class="nav-item custom-nav-item"><a href="/ELearning/student/studentProfile.php" class="nav-link">My Profile</a></li>';
                  echo '<li class="nav-item custom-nav-item"><a href="/ELearning/logout.php" class="nav-link">Logout</a></li>';
              } else {
                  echo '<li class="nav-item custom-nav-item"><a href="#login" class="nav-link" data-toggle="modal" data-target="#stuLoginModalCenter">Login</a></li>';
                  echo '<li class="nav-item custom-nav-item"><a href="#signup" class="nav-link" data-toggle="modal" data-target="#stuRegModalCenter">Signup</a></li>';
              }
          ?>

          <li class="nav-item custom-nav-item"><a href="#Feedback" class="nav-link">Feedback</a></li>
          <li class="nav-item custom-nav-item"><a href="#Contact" class="nav-link">Contact</a></li>
        </ul>
      </div>
    </nav>
