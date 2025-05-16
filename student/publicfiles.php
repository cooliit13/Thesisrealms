<?php 
session_start();
$mysqli = new mysqli("localhost", "root", "", "thesis_db");


 

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
    <title>College of Technologies Thesis Realm</title>
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

    <main class="container my-4">
        
        <!-- Search bar -->
        <div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="GET" class="d-flex justify-content-center">
                <input type="hidden" name="department" value="<?php echo htmlspecialchars($departmentFilter); ?>">
                <input class="form-control me-2" type="search" name="search" placeholder="Search thesis, capstones, or authors..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>
</div>

        <!-- Department Cards -->
        <div class="row mb-4">
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
            <div class="col-md-2 col-sm-4 mb-3">
                <div class="card department-card text-center p-3 <?php echo (empty($departmentFilter)) ? 'active' : ''; ?>" onclick="filterByDepartment('')">
                    <i class="fas fa-list fa-3x mb-2"></i>
                    <h5>All</h5>
                </div>
            </div>
        </div>

        <!-- Thesis Grid -->
        <div class="row mt-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4">
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
