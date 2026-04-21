<?php
include "db.php";

if(isset($_POST['register'])){
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    // Securely hash the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (fullname, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $username, $password);
    
    if($stmt->execute()){
        $success = "Account Created Successfully! You can now Login.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px;">
        <h3 class="text-center mb-4">Create Admin Account</h3>
        <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <a href="login.php" class="btn btn-secondary mb-3 text-white">Back</a>
        <form method="POST">
            <input type="text" name="fullname" class="form-control mb-3" placeholder="Full Name" required>
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>
</body>
</html>