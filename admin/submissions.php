<?php
session_start();  // Start the session at the top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Include PHPMailer autoloader
require '../vendor/autoload.php'; // Adjust path if needed

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Adjust path to where your .env file is located
$dotenv->load();

$conn = new mysqli("localhost", "root", "", "thesis_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to send approval email notification using PHPMailer
function sendApprovalEmail($to, $filename) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['MAIL_PORT'] ?? 587;

        // Recipients
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@thesisrealm.com', 
                      $_ENV['MAIL_FROM_NAME'] ?? 'BukSU Cot Thesis Realm');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Thesis Submission Approved - Thesis Realm";
        
        $mail->Body = "
        <html>
        <head>
            <title>Thesis Submission Approved</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { background-color: #d4edda; color: #155724; padding: 10px; text-align: center; border-radius: 3px; }
                .content { padding: 20px 0; }
                .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Thesis Submission Approved</h2>
                </div>
                <div class='content'>
                    <p>Dear User,</p>
                    <p>We are pleased to inform you that your thesis submission <strong>\"$filename\"</strong> has been approved!</p>
                    <p>Your thesis is now available in our repository and accessible to other users according to our access policies.</p>
                    <p>Thank you for contributing to our academic community.</p>
                    <p>If you have any questions or need assistance, please contact our support team.</p>
                    <p>Best regards,<br>Thesis Realm Administration</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Function to send rejection email notification using PHPMailer
function sendRejectionEmail($to, $filename, $reason = "Does not meet submission requirements") {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
        $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
        $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $_ENV['MAIL_PORT'] ?? 587;

        // Recipients
        $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@thesisrealm.com', 
                      $_ENV['MAIL_FROM_NAME'] ?? 'BukSU Cot Thesis Realm');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Thesis Submission Rejected - Thesis Realm";
        
        $mail->Body = "
        <html>
        <head>
            <title>Thesis Submission Rejected</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; border-radius: 3px; }
                .content { padding: 20px 0; }
                .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #777; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Thesis Submission Rejected</h2>
                </div>
                <div class='content'>
                    <p>Dear User,</p>
                    <p>We regret to inform you that your thesis submission <strong>\"$filename\"</strong> has been rejected.</p>
                    <p><strong>Reason for rejection:</strong></p>
                    <p>$reason</p>
                    <p>Please review the submission guidelines and consider uploading a revised version that meets our requirements.</p>
                    <p>If you have any questions or need assistance, please contact our support team.</p>
                    <p>Best regards,<br>Thesis Realm Administration</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Handle Approve with Reason from modal
if (isset($_POST['approve_with_message'])) {
    $id = $_POST['file_id'];
    $message = $_POST['approval_message'] ?? ''; // Default to empty string if not provided
    
    // Get the uploader's email and filename to send email
    $stmt = $conn->prepare("SELECT filename, uploader_email FROM uploads WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename, $uploader_email);
    $stmt->fetch();
    $stmt->close();
    
    // Update status to approved - First check if the approval_message column exists
    $check_column = $conn->query("SHOW COLUMNS FROM `uploads` LIKE 'approval_message'");
    if ($check_column->num_rows > 0) {
        // The approval_message column exists, use it
        $stmt = $conn->prepare("UPDATE uploads SET status = 'Approved', approved = 1, approval_message = ? WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("si", $message, $id);
    } else {
        // The column doesn't exist, just update status and approved fields
        $stmt = $conn->prepare("UPDATE uploads SET status = 'Approved', approved = 1 WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
    $stmt->close();
    
    // Send approval email
    $email_sent = sendApprovalEmail($uploader_email, $filename);
    
    // Set a session message to show confirmation
    if ($email_sent) {
        $_SESSION['message'] = "Submission approved and email sent to $uploader_email";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Submission approved but email could not be sent to $uploader_email. Check server logs.";
        $_SESSION['message_type'] = "warning";
    }
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Reject with Reason from modal
if (isset($_POST['reject_with_reason'])) {
    $id = $_POST['file_id'];
    $reason = $_POST['rejection_reason'] ?? 'Does not meet submission requirements'; // Default reason
    
    // Get the uploader's email and filename to send email
    $stmt = $conn->prepare("SELECT filename, uploader_email FROM uploads WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename, $uploader_email);
    $stmt->fetch();
    $stmt->close();
    
    // Update status and reason - First check if the rejection_reason column exists
    $check_column = $conn->query("SHOW COLUMNS FROM `uploads` LIKE 'rejection_reason'");
    if ($check_column->num_rows > 0) {
        // The rejection_reason column exists, use it
        $stmt = $conn->prepare("UPDATE uploads SET status = 'Rejected', approved = 0, rejection_reason = ? WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("si", $reason, $id);
    } else {
        // The column doesn't exist, just update status and approved fields
        $stmt = $conn->prepare("UPDATE uploads SET status = 'Rejected', approved = 0 WHERE id = ?");
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
    $stmt->close();
    
    // Send rejection email
    $email_sent = sendRejectionEmail($uploader_email, $filename, $reason);
    
    // Set a session message to show confirmation
    if ($email_sent) {
        $_SESSION['message'] = "Submission rejected and email sent to $uploader_email";
        $_SESSION['message_type'] = "warning";
    } else {
        $_SESSION['message'] = "Submission rejected but email could not be sent to $uploader_email. Check server logs.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Delete
if (isset($_POST['delete_submission'])) {
    $id = $_POST['submission_id'];
    
    // Get the filename to delete from the filesystem
    $stmt = $conn->prepare("SELECT filename FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($filename);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the file system
    if (file_exists("../uploads/$filename")) {
        unlink("../uploads/$filename");
    }
    
    $stmt = $conn->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['message'] = "Submission deleted successfully";
    $_SESSION['message_type'] = "danger";
    
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Add a column for approval_message in your uploads table if it doesn't exist yet
// ALTER TABLE uploads ADD COLUMN approval_message TEXT AFTER rejection_reason;

// Fetch all uploaded files
$result = $conn->query("SELECT * FROM uploads ORDER BY upload_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions | Thesis Realm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-container">
    <!-- Top Bar -->
    <div class="top-bar">
        <h5 class="mb-0">Thesis Realm Admin</h5>
        <div class="user-profile">
            <img src="../assets/images/464677697_444110865091918_7101498701914949461_n.jpg" alt="Admin User">
            <span>Admin User</span>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper d-flex" style="height: 100vh; overflow: hidden;">

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar" style="width: 250px; flex-shrink: 0;">

            <div class="sidebar-header">
                <div class="sidebar-logo text-center py-3">
                    <img src="../assets/images/COTLOGO.png" alt="Thesis Realm Logo" class="img-fluid" style="max-width: 150px;">
                    <h6 class="mt-2 text-center">Thesis Realm Admin</h6>
                </div>
            </div>
            <div class="sidebar-menu">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="users.php"><i class="fas fa-users"></i> User Management</a>
                <a href="#"><i class="fas fa-file-alt"></i> Thesis Management</a>
                <a href="submissions.php" class="active"><i class="fas fa-tasks"></i> Submissions</a>
                <a href="#"><i class="fas fa-chart-bar"></i> Reports & Analytics</a>
                <a href="#"><i class="fas fa-cog"></i> System Settings</a>
                <a href="#"><i class="fas fa-question-circle"></i> Help Center</a>
                <div class="mt-auto p-3">
                    <a href="\Sagayoc\login.php" class="btn btn-sm btn-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content p-3 flex-grow-1 overflow-auto">

            <h3>Uploaded Thesis Submissions</h3>
            
            <!-- Display any messages -->
            <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                // Clear the message after displaying
                unset($_SESSION['message']); 
                unset($_SESSION['message_type']);
            endif; 
            ?>
            
            <!-- Table -->
            <div class="table-responsive mt-3" style="max-height: 100vh; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="submissionTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Filename</th>
                            <th>Uploader</th>
                            <th>Email</th>
                            <th>Group</th>
                            <th>Department</th>
                            <th>Upload Date</th>
                            <th>Status</th>
                            <th style="min-width: 160px;">Actions</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['filename']) ?></td>
                                <td><?= htmlspecialchars($row['uploaded_by']) ?></td>
                                <td><?= htmlspecialchars($row['uploader_email']) ?></td>
                                <td><?= htmlspecialchars($row['group_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['department_name']) ?></td>
                                <td><?= date('Y-m-d', strtotime($row['upload_date'])) ?></td>
                                <?php
                                $status = htmlspecialchars($row['status'] ?? 'Pending');
                                $badgeClass = match($status) {
                                    'Approved' => 'success',
                                    'Rejected' => 'danger',
                                    default => 'secondary', // Pending or other
                                };
                                ?>
                                <td><span class="badge bg-<?= $badgeClass ?>"><?= $status ?></span></td>
                                <td>
    <?php if (strtolower($row['status']) === 'pending'): ?>
        <div class="d-flex flex-nowrap gap-1">
            <!-- Approve Button -->
            <button type="button" class="btn btn-success btn-sm" 
                    onclick="openApproveMessageModal(<?= $row['id'] ?>, 
                    '<?= addslashes(htmlspecialchars($row['filename'])) ?>', 
                    '<?= addslashes(htmlspecialchars($row['uploader_email'])) ?>')">
                <i class="fas fa-check-circle"></i>
            </button>

            <!-- Reject Button -->
            <button type="button" class="btn btn-warning btn-sm" 
                    onclick="openRejectReasonModal(<?= $row['id'] ?>, 
                    '<?= addslashes(htmlspecialchars($row['filename'])) ?>', 
                    '<?= addslashes(htmlspecialchars($row['uploader_email'])) ?>')">
                <i class="fas fa-times-circle"></i>
            </button>

            <!-- Delete Button -->
            <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                <input type="hidden" name="submission_id" value="<?= $row['id'] ?>">
                <button name="delete_submission" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    <?php endif; ?>
</td>

                                <td>
                                    <a href="../uploads/<?= htmlspecialchars($row['filename']) ?>" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Approval Message Modal -->
            <div class="modal fade" id="approveMessageModal" tabindex="-1" aria-labelledby="approveMessageModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="approveMessageModalLabel">Approve Submission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="approveMessageForm" method="post">
                            <div class="modal-body">
                                <p>Are you sure you want to approve this submission?</p>
                                <p><strong>File:</strong> <span id="approve-filename"></span></p>
                                <p><strong>Email:</strong> <span id="approve-email"></span></p>
                                
                                <input type="hidden" id="approve-file-id" name="file_id">
                                
                                <div class="mb-3">
                                    <label for="approval_message" class="form-label">Additional Message (Optional):</label>
                                    <select class="form-select mb-2" id="approval_message_template" onchange="updateApprovalMessage()">
                                        <option value="">-- Select a template or write custom message --</option>
                                        <option value="Excellent work! Your submission has been approved without any revisions needed.">Excellent work - No revisions</option>
                                        <option value="Your thesis has been approved. Minor formatting issues were noted but not significant enough to require resubmission.">Approved with minor formatting notes</option>
                                        <option value="Approved. Please consider the feedback provided by your department for future academic work.">Approved with department feedback</option>
                                        <option value="Congratulations on your approval! Your work will be featured in our highlighted research section.">Approval with featured recognition</option>
                                    </select>
                                    <textarea class="form-control" id="approval_message" name="approval_message" rows="3" placeholder="Add any additional information for the student..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="approve_with_message" class="btn btn-success">Approve Submission</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason Modal -->
            <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="rejectReasonModalLabel">Rejection Reason</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="rejectReasonForm" method="post">
                            <div class="modal-body">
                                <p>Please provide a reason for rejecting the submission:</p>
                                <p><strong>File:</strong> <span id="reject-filename"></span></p>
                                <p><strong>Email:</strong> <span id="reject-email"></span></p>
                                
                                <input type="hidden" id="reject-file-id" name="file_id">
                                
                                <div class="mb-3">
                                    <label for="rejection_reason" class="form-label">Reason for Rejection:</label>
                                    <select class="form-select mb-2" id="rejection_reason_template" onchange="updateRejectionReason()">
                                        <option value="">-- Select a template or write custom message --</option>
                                        <option value="The thesis format does not follow the required university guidelines. Please review our formatting requirements and resubmit.">Formatting issues</option>
                                        <option value="The thesis content is incomplete or missing required sections. Please ensure all required components are included before resubmission.">Incomplete content</option>
                                        <option value="The thesis contains significant plagiarism issues. Please revise and ensure proper citation of all sources.">Plagiarism concerns</option>
                                        <option value="The research methodology is inadequately described or inappropriate for the study objectives. Please revise accordingly.">Methodology issues</option>
                                        <option value="The literature review is insufficient or outdated. Please include more recent and relevant research.">Insufficient literature review</option>
                                    </select>
                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Explain why this submission is being rejected..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="reject_with_reason" class="btn btn-warning">Reject Submission</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTable with enhanced options
    document.addEventListener('DOMContentLoaded', function() {
        new DataTable('#submissionTable', {
            responsive: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            columnDefs: [
                { orderable: false, targets: [8, 9] }, // Disable sorting on action columns
                { className: "text-center", targets: [7, 8, 9] } // Center align status and action columns
            ],
            language: {
                search: "Search submissions:",
                lengthMenu: "Show _MENU_ submissions per page",
                info: "Showing _START_ to _END_ of _TOTAL_ submissions",
                emptyTable: "No submissions available"
            }
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);
    });
    
    // Approval modal handling
    function openApproveMessageModal(fileId, filename, email) {
        document.getElementById('approve-file-id').value = fileId;
        document.getElementById('approve-filename').textContent = filename;
        document.getElementById('approve-email').textContent = email;
        
        // Show the modal using Bootstrap's modal API
        var approveModal = new bootstrap.Modal(document.getElementById('approveMessageModal'));
        approveModal.show();
    }
    
    // Rejection modal handling
    function openRejectReasonModal(fileId, filename, email) {
        document.getElementById('reject-file-id').value = fileId;
        document.getElementById('reject-filename').textContent = filename;
        document.getElementById('reject-email').textContent = email;
        
        // Show the modal using Bootstrap's modal API
        var rejectModal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        rejectModal.show();
    }
    
    // Form validation for rejection
    document.getElementById('rejectReasonForm').addEventListener('submit', function(e) {
        if (!document.getElementById('rejection_reason').value.trim()) {
            e.preventDefault();
            alert('Please provide a rejection reason');
        }
    });
    
    // Function to update rejection reason from template
    function updateRejectionReason() {
        const template = document.getElementById('rejection_reason_template').value;
        if (template) {
            document.getElementById('rejection_reason').value = template;
        }
    }
    
    // Function to update approval message from template
    function updateApprovalMessage() {
        const template = document.getElementById('approval_message_template').value;
        if (template) {
            document.getElementById('approval_message').value = template;
        }
    }
</script>
</body>
</html>

<?php $conn->close(); ?>