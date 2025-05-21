<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BukSu COT: Capstone Repository</title>
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../styles/index-styles.css">
</head>
<style>
  .main-banner a {
    position: relative;
    z-index: 10;
  }
</style>

<body>

<header>
  <div class="logo">
    <img src="../assets/images/COTLOGO.png" alt="Logo">
    <span>BukSu COT: Capstone Repository</span>
  </div>
  <nav>
    <ul>
                <li><a href="../student/student.php"><i class=""></i> HOME</a></li>
                <li><a href="\Sagayoc\student\about.php"><i class=""></i> ABOUT</a></li>
                <li><a href="\Sagayoc\student\publicfiles.php"><i class=""></i> FILES</a></li>
            </ul>
  </nav>
</header>


<div class="main-banner text-center py-5">
  <h1>BukSU COT: CAPSTONE REPOSITORY</h1>
  <p class="mb-4">is the official Institutional Repository of the College of Technologies
    and as the flagship department, it is also in charge of the permanent records of the
    offices of the BukSU System. It identifies, acquires, maintains, preserves, and allows
    access to the digital institutional records and memory of the College.</p>


<a href="/Sagayoc/student/publicfiles.php" class="btn btn-outline-light btn-lg px-4 shadow">
  <i class="bi bi-folder2-open"></i> Explore More Capstone Files!
</a>



</div>



<!-- Contact Info Section -->
<div class="contact-info-section">
  <div class="container">
    <h2 class="contact-title">CONTACT INFO</h2>
    <div class="title-underline"></div>
    
    <div class="contact-content">
      <p class="contact-intro">
        BuKSU COT: Capstone Repository is hosted and maintained by the College of Technologies,
        Bukidnon State University.
      </p>
      
      <div class="contact-details">
        <div class="contact-item">
          <span class="contact-label"><i class="bi bi-geo-alt-fill"></i> Address</span>
          <span class="contact-divider">:</span>
          <span class="contact-value">College of Technologies Department, Bukidnon State University, Fortich Street, Malaybalay City 8700</span>
        </div>
        
        <div class="contact-item">
          <span class="contact-label"><i class="bi bi-telephone-fill"></i> Phone</span>
          <span class="contact-divider">:</span>
          <span class="contact-value">(+63) 123-4567 local 123</span>
        </div>
        
        <div class="contact-item">
          <span class="contact-label"><i class="bi bi-envelope-fill"></i> Email</span>
          <span class="contact-divider">:</span>
          <span class="contact-value">
            buksucot.capstonerepository@gmail.com<br>
            
          </span>
        </div>
        
        <div class="contact-item">
          <span class="contact-label"><i class="bi bi-globe"></i> Website</span>
          <span class="contact-divider">:</span>
          <span class="contact-value">main.buksu.edu.ph</span>
        </div>
      </div>
    </div>
  </div>
</div>

<footer>
   &copy; 2025 Bukidnon State University – College of Technologies. All rights reserved. 
</footer>

<script src="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>