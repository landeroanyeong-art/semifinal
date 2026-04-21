<?php
session_start();
include "db.php";
if(!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$teacher_id = $_SESSION['teacher_id'];

// When you click "Save" or "Update", this changes the database automatically
if (isset($_POST['update_gate'])) {
    $opening_time = mysqli_real_escape_string($conn, $_POST['gate_open_time']);
    
    // This SQL command updates the existing time or creates it if it doesn't exist
    $sql = "INSERT INTO settings (teacher_id, class_start) 
            VALUES ('$teacher_id', '$opening_time') 
            ON DUPLICATE KEY UPDATE class_start='$opening_time'";
    
    if(mysqli_query($conn, $sql)) {
        $msg = "Gate opening time updated to " . date("h:i A", strtotime($opening_time));
    }
}

// Fetch current setting
$res = mysqli_query($conn, "SELECT class_start FROM settings WHERE teacher_id='$teacher_id'");
$set = mysqli_fetch_assoc($res);
$current_time = $set['class_start'] ?? '07:00';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass | Gate Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .settings-card { max-width: 500px; margin: 50px auto; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background: #4e73df; border: none; padding: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="settings-card card p-4">
            <div class="text-center mb-4">
                <div class="display-4 text-primary mb-2"><i class="bi bi-door-open-fill"></i></div>
                <h3 class="fw-bold">Gate Control</h3>
                <p class="text-muted">Set the time when scans are allowed</p>
            </div>

            <?php if(isset($msg)) echo "<div class='alert alert-success small text-center'>$msg</div>"; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">Gate Opening Time</label>
                    <input type="time" name="gate_open_time" class="form-control form-control-lg" value="<?php echo $current_time; ?>" required>
                    <div class="form-text">Students cannot scan before this exact time.</div>
                </div>

                <button name="update_gate" class="btn btn-primary w-100 rounded-3">Update Gate Schedule</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>