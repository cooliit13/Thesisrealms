<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css"> <!-- Bootstrap Link-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <title>College of Technologies Thesis Realm</title>
    <link rel="stylesheet" href="../styles/about-styles.css">
</head>
<body>
    <header>
        <h5>
            <img src="../assets/images/COTLOGO.png" alt="Logo" style="vertical-align: middle; height: 40px; padding-left: 10px; margin-top: 10px;">
            College of Technologies Thesis Realm
        </h5>
        <nav>
        <ul>
                <li><a href="../student/student.php"><i class=""></i> HOME</a></li>
                <li><a href="\Sagayoc\student\about.php"><i class=""></i> ABOUT</a></li>
                <li><a href="\Sagayoc\student\publicfiles.php"><i class=""></i> FILES</a></li>
            </ul>
        </nav>
    </header>

        
    <main class="container">  
    <section class="mt-5">  
        <h2 class="text-center mb-5">About Us</h2>
        <p>
            Thesis Realm is a web-based system developed for the College of Technologies at Bukidnon State University. It serves as a centralized digital platform for uploading, browsing, and managing student thesis works. The system aims to streamline the research process by offering an accessible, organized, and secure environment where students, faculty, and administrators can collaborate and manage academic outputs efficiently.
        </p>
    </section>
</main>


        <section class="my-5">
            <h2 class="text-center mb-5" style="text-align: center;">Meet Our Team</h2>
            <div class="developers-container">
    <!-- Developer 1 -->
    <div class="developer-card armando-card" style="animation-delay: 0.2s;">
        <img src="../assets/images/464677697_444110865091918_7101498701914949461_n.jpg" alt="Armando Sagayoc Jr." class="developer-img">
        <h5>Armando Sagayoc Jr.</h5>
        <p class="developer-role">Back-end Developer</p>
        <p>Implements server-side logic and manages database operations for secure and efficient data handling.</p>
        <div class="social-links">
            <a href="https://www.facebook.com/xButZ" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://github.com" target="_blank"><i class="bi bi-github"></i></a>
            <a href="cooliit13@gmail.com"><i class="bi bi-envelope-fill"></i></a>
        </div>
    </div>


    <!-- Developer 2 -->
     <div class="developer-card kate-card" style="animation-delay: 0.2s;">
        <img src="../assets/images/keyt.jpg" alt="Kate Alysabelle S. Castro" class="developer-img">
        <h5>Kate Alysabelle S. Castro</h5>
        <p class="developer-role">Front-end Developer</p>
        <p>Develops responsive user interfaces and ensures seamless user experience across the platform.</p>
         <div class="social-links">
            <a href="https://www.facebook.com/katesuazo.castro" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://github.com" target="_blank"><i class="bi bi-github"></i></a>
            <a href="mailto:katecastro12.kc@gmail.com"><i class="bi bi-envelope-fill"></i></a>
        </div>
    </div>

    <!-- Developer 3 -->
     <div class="developer-card janus-card" style="animation-delay: 0.2s;">
        <img src="../assets/images/januz.jpg" alt="Janus Ezam Tagud" class="developer-img">
        <h5>Janus Ezam Tagud</h5>
        <p class="developer-role">Integration Developer</p>
        <p>Facilitates integration between front-end and back-end, supporting system flow, testing, and optimization.</p>
         <div class="social-links">
            <a href="https://www.facebook.com/janus.ezam.tagud" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="https://github.com" target="_blank"><i class="bi bi-github"></i></a>
            <a href="mailto:janus@example.com"><i class="bi bi-envelope-fill"></i></a>
        </div>
    </div>
        </section>
    </main>

   

    <script src="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
