<?php
session_start();
include "db.php";
if(!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$teacher_id = $_SESSION['teacher_id'];
$msg = "";

if(isset($_POST['save'])){
    $sid = mysqli_real_escape_string($conn, $_POST['student_id']);
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $rfid = strtoupper(str_replace(' ', '', $_POST['rfid']));

    $check = mysqli_query($conn, "SELECT * FROM students WHERE rfid_uid='$rfid'");
    if(mysqli_num_rows($check) > 0){
        $msg = "<div class='alert alert-danger'>RFID Card is already assigned!</div>";
    } else {
        $sql = "INSERT INTO students (student_id, fullname, rfid_uid, teacher_id, status) 
                VALUES ('$sid', '$name', '$rfid', '$teacher_id', 'Absent')";
        if(mysqli_query($conn, $sql)){
            header("Location: students.php?msg=added");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass | Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <h3 class="fw-bold mb-4">Register Student</h3>
            <?php echo $msg; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Student ID Number</label>
                    <input type="text" name="student_id" class="form-control" placeholder="e.g. 2024-0001" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Full Name</label>
                    <input type="text" name="fullname" class="form-control" placeholder="First M. Last" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">RFID Card UID</label>
                    <input type="text" name="rfid" class="form-control" placeholder="Scan card to get UID" required>
                </div>
                <button name="save" class="btn btn-primary w-100 py-2 fw-bold rounded-3">Register Student</button>
            </form>
            <div class="text-center mt-3">
                <a href="students.php" class="text-decoration-none small text-muted">Cancel and Go Back</a>
            </div>
        </div>
    </div>
</body>
</html>