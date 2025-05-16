<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>login</title>
        <link rel="stylesheet"href="styles/signup-styles.css">
        <link rel="stylesheet"href="/bootstrap-5.3.3-dist/bootstrap.min.css">
        <meta charset="utf-8">
    </head>
    <body>
        <div class="container">
            <div class="card">
            <img src="images/COTLOGO.png" alt="Logo" class="logo">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?=$_SESSION['error']; unset ($_SESSION['error']);?>
                    </div>
                    <?php endif; ?>
                    <form action="signup_validate.php" method="POST">
                        <input class = "form-control" type="text" placeholder="Firstname" name="firstname" required>
                        <input class = "form-control" type="text" placeholder="Last Name" name="lastname" required>
                        <input class = "form-control" type="text" placeholder="Username" name="username" required>
                        <input class = "form-control" type="email" placeholder="Email" name="email" required>
                        <input class = "form-control" type="password" placeholder="Password" name="password" required>
                        <input class = "form-control" type="password" placeholder="Re-enter Password" name="confirm_password" required>
                       
                        <button type="submit">Sign-up</button>
                    </form>
                    <p>Already have and account? <a href ="login.php"> Log-in </a></p>
            </div>
        </div>
    </body>
</html>

