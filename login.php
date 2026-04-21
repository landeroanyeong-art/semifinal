<?php
session_start();
include "db.php";
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        if(password_verify($password, $row['password'])){
            $_SESSION['user'] = $row['username'];
            $_SESSION['teacher_id'] = $row['id'];
            header("Location: dashboard.php");
            exit;
        }
    }
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GatePass | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { border-radius: 1rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; padding: 2.5rem; background: #fff; }
        .btn-primary { background: #4e73df; border: none; padding: 0.8rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Group 2</h2>
            <p class="text-muted">RFID Attendance System</p>
        </div>
        <?php if(isset($error)) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control rounded-3" placeholder="Enter username" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control rounded-3" placeholder="••••••••" required>
            </div>
            <button name="login" class="btn btn-primary w-100 fw-bold rounded-3">Sign In</button>
            <div class="text-center mt-3 small">
                Don't have an account? <a href="register_user.php" class="text-decoration-none">Register</a>
            </div>
        </form>
    </div>
</body>
</html>