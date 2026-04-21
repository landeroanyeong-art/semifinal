<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit; 
}

$teacher_id = $_SESSION['teacher_id'] ?? 1;

// Fetch history joined with student names
$query = "SELECT a.date, a.time_in, a.status, s.fullname, s.student_id as id_num
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          WHERE a.teacher_id = '$teacher_id' 
          ORDER BY a.date DESC, a.time_in DESC";

$res = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GatePass | History Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-bg: #1a1c23; --accent: #6366f1; }
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }
        
        /* Sidebar Styles */
        .sidebar { height: 100vh; width: 260px; position: fixed; background: var(--sidebar-bg); color: white; padding: 1.5rem; }
        .main-content { margin-left: 260px; padding: 2.5rem; }
        .nav-link { color: #94a3b8; border-radius: 12px; margin-bottom: 0.5rem; padding: 12px 15px; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(99, 102, 241, 0.1); color: var(--accent); }
        .nav-link.active { background: var(--accent); color: white; }

        /* Table Card Styles */
        .glass-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            overflow: hidden;
        }
        .table thead { background: #f1f5f9; }
        .table th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #64748b; padding: 1.25rem; }
        .table td { padding: 1.25rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        /* Status Badges */
        .badge-present { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
        .badge-late { background: #fffbeb; color: #f59e0b; border: 1px solid #fef3c7; }
        .badge-absent { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
    </style>
</head>
<body>

    <div class="sidebar shadow">
        <div class="d-flex align-items-center mb-5 px-2">
            <i class="bi bi-shield-check-fill text-primary fs-3 me-2"></i>
            <h4 class="fw-bold mb-0">Group 2</h4>
        </div>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-grid-fill me-2"></i> Dashboard</a>
            <a href="students.php" class="nav-link"><i class="bi bi-people-fill me-2"></i> Students</a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear-fill me-2"></i> Settings</a>
            <a href="archive.php" class="nav-link active"><i class="bi bi-clock-history me-2"></i> History</a>
            <hr class="text-secondary opacity-25 my-4">
            <a href="logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Attendance History</h2>
                <p class="text-muted mb-0">Review all previous gate entry logs</p>
            </div>
            <a href="export.php" class="btn btn-dark rounded-3 px-4 fw-bold">
                <i class="bi bi-download me-2"></i> Export CSV
            </a>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Date</th>
                            <th>Time Scanned</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($res) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $row['fullname']; ?></div>
                                    <div class="text-muted small">ID: <?php echo $row['id_num']; ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo date("M d, Y", strtotime($row['date'])); ?></div>
                                </td>
                                <td>
                                    <i class="bi bi-clock me-1 text-muted"></i>
                                    <?php echo date("h:i A", strtotime($row['time_in'])); ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = $row['status'];
                                    $class = ($status == 'Present') ? 'badge-present' : (($status == 'Late') ? 'badge-late' : 'badge-absent');
                                    ?>
                                    <span class="badge <?php echo $class; ?> px-3 py-2 rounded-pill shadow-sm">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                    No records found in the history.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>