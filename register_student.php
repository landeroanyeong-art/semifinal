<?php
session_start();
include "db.php";

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get teacher ID for the logged-in user
$teacher_username = $_SESSION['user'];
$teacher_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$teacher_username'");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['id'];

$success = "";
$error = "";

if (isset($_POST['save'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $rfid = mysqli_real_escape_string($conn, $_POST['rfid']);

    // Check if UID is already taken
    $check_rfid = mysqli_query($conn, "SELECT * FROM students WHERE rfid_uid='$rfid'");
    
    if (mysqli_num_rows($check_rfid) > 0) {
        $error = "This RFID card is already assigned to another student!";
    } else {
        $sql = "INSERT INTO students (student_id, fullname, rfid_uid, teacher_id) 
                VALUES ('$student_id', '$fullname', '$rfid', '$teacher_id')";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Student '$fullname' registered successfully!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Student | Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; }
        .btn-primary { border-radius: 8px; padding: 10px 20px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-plus-fill text-primary"></i> Register New Student</h2>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm p-4">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Student ID / Serial Number</label>
                        <input type="text" name="student_id" class="form-control" placeholder="e.g. 2024-0001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Enter student's full name" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">RFID UID (Card ID)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="rfid" id="rfid_field" class="form-control" 
                                   placeholder="Paste UID here (e.g. 2A B8 CA B3)" required>
                        </div>
                        <small class="text-muted">Tip: Scan the card while bridge.py is running and copy the UID from your terminal.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="save" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Student
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted small">Logged in as: <strong><?php echo $_SESSION['user']; ?></strong> (ID: <?php echo $teacher_id; ?>)</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>