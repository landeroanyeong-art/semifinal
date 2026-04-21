<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$teacher_username = $_SESSION['user'];
$teacher_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$teacher_username'");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['id'];

$success = "";
$error = "";

// Get the student's current data
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM students WHERE id='$id' AND teacher_id='$teacher_id'");
    $student = mysqli_fetch_assoc($res);
    
    if (!$student) { header("Location: view_students.php"); exit; }
}

// Handle the Update
if (isset($_POST['update'])) {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $rfid = mysqli_real_escape_string($conn, $_POST['rfid']);
    $id = $_POST['id'];

    $sql = "UPDATE students SET student_id='$student_id', fullname='$fullname', rfid_uid='$rfid' 
            WHERE id='$id' AND teacher_id='$teacher_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: view_students.php?status=updated");
        exit;
    } else {
        $error = "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student | Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-pencil-square text-primary"></i> Edit Student</h2>
                <a href="view_students.php" class="btn btn-outline-secondary btn-sm">Back to List</a>
            </div>

            <div class="card shadow-sm p-4">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Student ID</label>
                        <input type="text" name="student_id" class="form-control" value="<?php echo $student['student_id']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="fullname" class="form-control" value="<?php echo $student['fullname']; ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">RFID UID</label>
                        <input type="text" name="rfid" class="form-control" value="<?php echo $student['rfid_uid']; ?>" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="update" class="btn btn-primary fw-bold">Update Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>