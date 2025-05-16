<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link rel="stylesheet" href="styles/sendcode-styles.css">
    
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="images/COTLOGO.png" alt="Logo" class="logo">
            <h5>Enter the code to continue.</h5>

            <!-- Display error message from session -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Display success message from session -->
            <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    
<?php endif; ?>


            <!-- Display info message after the code has been sent -->
            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert alert-info">
                    <?= $_SESSION['info']; unset($_SESSION['info']); ?>
                </div>
            <?php endif; ?>

            <form action="sendcode_validate.php" method="POST">
                <input type="text" placeholder="Enter code" name="code" required>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>