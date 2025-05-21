<?php
session_start();
if (!isset($_SESSION['verified'])) {
    // Redirect to send-code if the code wasn't verified
    header("Location: send-code.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="styles/newpass-styles.css">
    <meta charset="utf-8">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="assets/images/COTLOGO.png" alt="Logo" class="logo">
            <h5>Set your new Password</h5>

            <!-- Show error if password doesn't match -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
                    <script>
                 window.onload = function () {
                Swal.fire({
                title: 'Success!',
                text: 'Entered the code successfully. You may now set your new password.',
                icon: 'success',
                confirmButtonText: 'Continue',
                background: '#581413',
                color: '#FFFFFF',
                iconColor: '#D97706',
                customClass: {
                    confirmButton: 'custom-confirm-btn'
                }
            });
        };
    </script>
``

            <?php endif; ?>

            <!-- Show SweetAlert on successful code verification -->
            <?php if (isset($_SESSION['code_success'])): ?>
                <div class="alert alert-success">
                    Code verified successfully! Please enter your new password.
                </div>
                
                
                <?php unset($_SESSION['code_success']); ?>
            <?php endif; ?>

            <form action="newpassword_validate.php" method="POST">
                <input type="password" name="password" placeholder="Enter New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>