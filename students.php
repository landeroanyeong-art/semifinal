<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit; 
}

$teacher_username = $_SESSION['user'];
$res = mysqli_query($conn, "SELECT id FROM users WHERE username='$teacher_username'");
$teacher = mysqli_fetch_assoc($res);
$teacher_id = $teacher['id'];

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $id_to_delete = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM students WHERE id = '$id_to_delete' AND teacher_id = '$teacher_id'";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: students.php?msg=deleted");
        exit;
    }
}

$students = mysqli_query($conn, "SELECT * FROM students WHERE teacher_id='$teacher_id' ORDER BY fullname ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass | Student Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; padding: 2rem; }
        .card { border: none; border-radius: 1rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .search-input { border-radius: 0.75rem; padding: 0.75rem 1.25rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Student Directory</h2>
            <p class="text-muted small">Manage your registered students and RFID tags</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-outline-secondary me-2">Dashboard</a>
            <a href="add_student.php" class="btn btn-primary fw-bold">+ Add Student</a>
        </div>
    </div>

    <div class="card bg-white">
        <div class="card-body p-4">
            <input type="text" id="studentSearch" class="form-control search-input mb-4" placeholder="Search by name, ID, or RFID...">
            
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>RFID UID</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="student-list">
                        <?php while($row = mysqli_fetch_assoc($students)): ?>
                        <tr>
                            <td class="fw-bold small text-primary"><?php echo $row['student_id']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><code class="bg-light px-2 py-1 rounded text-dark"><?php echo $row['rfid_uid']; ?></code></td>
                            <td class="text-end">
                                <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border me-1"><i class="bi bi-pencil"></i></a>
                                <a href="students.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Are you sure?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('studentSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#student-list tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

</body>
</html>