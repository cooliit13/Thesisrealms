<?php 
session_start();
$mysqli = new mysqli("localhost", "root", "", "thesis_db");

// Check for database connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize upload status variable
$uploadStatus = "";

// Check if session variables are set
if (!isset($_SESSION['username'])) {
    $uploadStatus = '<div class="alert alert-danger">Session expired. Please log in again.</div>';
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['thesis_file'])) {
    $thesisName = $_POST['thesis_name'] ?? '';
    $groupName = $_POST['group_name'] ?? '';
    $departmentName = $_POST['department_name'] ?? '';
    $uploadedBy = $_SESSION['username'] ?? 'Unknown';
    
    // Get the uploader's email (either from session or database)
    $uploadedByEmail = null;

    if (isset($_SESSION['email'])) {
        $uploadedByEmail = $_SESSION['email'];
    } elseif (isset($_SESSION['google_email'])) {
        $uploadedByEmail = $_SESSION['google_email'];
        
        // Store Google email if not already in DB - FIXED ERROR HERE
        $query = "INSERT INTO user (username, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE email = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("sss", $_SESSION['username'], $_SESSION['google_email'], $_SESSION['google_email']);
            $stmt->execute();
            $stmt->close();
        } else {
            // Log the error but continue with upload
            error_log("Database prepare failed: " . $mysqli->error);
        }
    } else {
        // Attempt to fetch from DB
        $query = "SELECT email FROM user WHERE username = ?";
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $uploadedByEmail = $user['email'];
            }
            $stmt->close();
        } else {
            // Log the error
            error_log("Database prepare failed: " . $mysqli->error);
        }
    }
    
    // Final fallback if email is still not found
    if (empty($uploadedByEmail)) {
        $uploadedByEmail = 'No email provided';
    }
    
    $uploadDir = '../uploads/';
    $fileName = basename($_FILES['thesis_file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['thesis_file']['tmp_name'], $targetPath)) {
        $query = "INSERT INTO uploads (filename, thesis_name, group_name, department_name, uploaded_by, uploader_email, status, upload_date) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("ssssss", $fileName, $thesisName, $groupName, $departmentName, $uploadedBy, $uploadedByEmail);
            if ($stmt->execute()) {
                $uploadStatus = '
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Your file has been uploaded successfully. Please wait for admin approval.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else {
                $uploadStatus = '<div class="alert alert-danger">Database error: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        } else {
            $uploadStatus = '<div class="alert alert-danger">Database prepare error: ' . $mysqli->error . '</div>';
        }
    } else {
        $uploadStatus = '<div class="alert alert-danger">Failed to upload the file. Please try again.</div>';
    }
}

// Get department filter
$departmentFilter = $_GET['department'] ?? '';
$search = $_GET['search'] ?? '';

// Build query based on filters
$query = "SELECT * FROM uploads WHERE status = 'Approved'";
$params = [];

if (!empty($departmentFilter)) {
    $query .= " AND department_name = ?";
    $params[] = $departmentFilter;
}

if (!empty($search)) {
    $query .= " AND (thesis_name LIKE ? OR uploaded_by LIKE ? OR department_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY upload_date DESC";

// Prepare and execute query
$result = null;
if ($stmt = $mysqli->prepare($query)) {
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    // Don't close stmt here as we need the result for the HTML below
} else {
    $uploadStatus = '<div class="alert alert-danger">Query prepare failed: ' . $mysqli->error . '</div>';
}
?>

<!DOCTYPE html>
<!-- Rest of HTML remains unchanged -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../styles/files-styles.css">
    <title>BukSu COT: Capstone Repository</title>
    <style>
        .department-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .department-card.active {
            border: 2px solid #0d6efd;
            background-color: #f8f9fa;
        }
        .thesis-card {
            transition: all 0.3s ease;
        }
        .thesis-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .upload-btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        #uploadForm {
            display: none;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            position: relative;
            margin-top: 20px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .search-bar {
            margin: 20px 0;
        }
        .search-bar input {
            padding: 10px;
            width: 100%;
            max-width: 500px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <header>
        <h5>
            <img src="../assets/images/COTLOGO.png" alt="Logo" style="vertical-align: middle; height: 40px; padding-left: 10px; margin-top: 10px;">
            BukSu COT: Capstone Repository
        </h5>
        <nav>
            <ul>
                <li><a href="../dashboards/dashboard.php"><i class=""></i> HOME</a></li>
                <li><a href="../dashboards/about.php"><i class=""></i> ABOUT</a></li>
                <li><a href="../dashboards/files.php"><i class=""></i> FILES</a></li>
                <li><a href="../dashboards/accounts.php"><i class=""></i> ACCOUNT</a></li>
            </ul>
        </nav>
    </header>

    <main class="container my-4">
        <?php 
        if (!empty($uploadStatus)) {
            echo '<div id="uploadAlert">' . $uploadStatus . '</div>';
        }
        ?>

        <!-- Upload button -->
        <button class="upload-btn" onclick="toggleUploadForm()">
            <i class="fas fa-upload"></i> Upload Files
        </button>

        <!-- Upload form -->
        <div id="uploadForm">
            <button class="close-btn" onclick="toggleUploadForm()">
                <i class="fas fa-times"></i>
            </button>
            <h4>Upload Your Thesis/Capstone</h4>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Select File</label>
                    <input class="form-control" type="file" name="thesis_file" required>
                    
                    <label class="form-label mt-3">Thesis/Capstone Name</label>
                    <input class="form-control" type="text" name="thesis_name" required>
                    
                    <label class="form-label mt-3">Group Name</label>
                    <input class="form-control" type="text" name="group_name" required>
                    
                    <label class="form-label mt-3">Department</label>
                    <select class="form-select" name="department_name" required>
                        <option value="">Select Department</option>
                        <option value="BSIT">BS Information Technology</option>
                        <option value="BSEMC">BS Entertainment Media Computing</option>
                        <option value="BSAT">BS Automotive Technology</option>
                        <option value="BSET">BS Electronics Technology</option>
                        <option value="BSFT">BS Food Technology</option>
                    </select>
                </div>
                <button class="btn btn-primary mt-2" type="submit">
                    <i class="fas fa-cloud-upload-alt"></i> Upload
                </button>
            </form>
        </div>

        <!-- Search bar -->
        <div class="search-bar">
            <form method="GET" class="d-flex">
                <input type="hidden" name="department" value="<?php echo htmlspecialchars($departmentFilter); ?>">
                <input class="form-control me-2" type="search" name="search" placeholder="Search thesis, capstones, or authors..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <!-- Department Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo (empty($departmentFilter)) ? 'active' : ''; ?>" onclick="filterByDepartment('')">
                    <i class="fas fa-list fa-3x mb-2"></i>
                    <h5>All</h5>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo ($departmentFilter === 'BSIT') ? 'active' : ''; ?>" onclick="filterByDepartment('BSIT')">
                    <i class="fas fa-laptop-code fa-3x mb-2"></i>
                    <h5>BSIT</h5>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo ($departmentFilter === 'BSEMC') ? 'active' : ''; ?>" onclick="filterByDepartment('BSEMC')">
                    <i class="fas fa-film fa-3x mb-2"></i>
                    <h5>BSEMC</h5>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo ($departmentFilter === 'BSAT') ? 'active' : ''; ?>" onclick="filterByDepartment('BSAT')">
                    <i class="fas fa-tools fa-3x mb-2"></i>
                    <h5>BSAT</h5>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo ($departmentFilter === 'BSET') ? 'active' : ''; ?>" onclick="filterByDepartment('BSET')">
                    <i class="fas fa-bolt fa-3x mb-2"></i>
                    <h5>BSET</h5>
                </div>
            </div>
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo ($departmentFilter === 'BSFT') ? 'active' : ''; ?>" onclick="filterByDepartment('BSFT')">
                    <i class="fas fa-utensils fa-3x mb-2"></i>
                    <h5>BSFT</h5>
                </div>
            </div>
            
        </div>

        <!-- Thesis Grid -->
        <div class="row mt-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card thesis-card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                    <div>
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['thesis_name']); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['department_name']); ?></small>
                                    </div>
                                </div>
                                <p class="card-text"><small class="text-muted">Uploaded by: <?php echo htmlspecialchars($row['uploaded_by']); ?></small></p>
                                <p class="card-text"><small class="text-muted">Uploaded on: <?php echo date('M d, Y', strtotime($row['upload_date'])); ?></small></p>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="../uploads/<?php echo htmlspecialchars($row['filename']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="../uploads/<?php echo htmlspecialchars($row['filename']); ?>" download class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No thesis/capstone projects found. <?php echo (!empty($search)) ? 'Try a different search term.' : ''; ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function toggleUploadForm() {
            const form = document.getElementById('uploadForm');
            form.style.display = (form.style.display === 'block') ? 'none' : 'block';
        }

        function filterByDepartment(department) {
            window.location.href = '?department=' + department;
        }
    </script>
    
</body>
</html>
