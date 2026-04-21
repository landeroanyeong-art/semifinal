<?php
include "db.php";

$date = $_GET['date'] ?? date('Y-m-d');

// Get all students
$students = mysqli_query($conn,"SELECT * FROM students");

?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2>Attendance History for <?php echo $date; ?></h2>

    <!-- Back button -->
    <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Back</a>

    <form method="GET" class="mb-3">
        <input type="date" name="date" class="form-control w-25 d-inline" value="<?php echo $date; ?>">
        <button class="btn btn-primary">View</button>
    </form>

    <table class="table table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>Student</th>
                <th>Status</th>
                <th>Time In</th>
            </tr>
        </thead>
        <tbody>
            <?php while($student = mysqli_fetch_assoc($students)): 
                $att = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM attendance WHERE student_id='{$student['id']}' AND date='$date'"));
                ?>
                <tr>
                    <td><?php echo $student['fullname']; ?></td>
                    <td><?php echo $att ? 'Present' : 'Absent'; ?></td>
                    <td><?php echo $att ? $att['time_in'] : '-'; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>