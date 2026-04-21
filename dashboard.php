<?php
session_start();
include "db.php";

// Redirect if not logged in
if(!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit; 
}

$teacher_id = $_SESSION['teacher_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GatePass | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fc; font-family: 'Inter', sans-serif; }
        .sidebar { height: 100vh; width: 250px; position: fixed; background: #222e3c; color: #fff; padding-top: 20px; transition: all 0.3s; }
        .main { margin-left: 250px; padding: 30px; }
        .nav-link { color: #adb5bd; margin: 5px 15px; border-radius: 5px; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: #fff; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
        .fw-600 { font-weight: 600; }
        
        /* Badge colors */
        .badge-present { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-late { background: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        .badge-absent { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    </style>
</head>
<body>

    <div class="sidebar shadow">
        <div class="px-4 mb-4">
            <h4 class="fw-bold text-white"><i class="bi bi-shield-check me-2"></i>GatePass</h4>
            <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Teacher Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a class="nav-link" href="students.php"><i class="bi bi-people me-2"></i> Students</a>
            <a class="nav-link" href="archive.php"><i class="bi bi-clock-history me-2"></i> History</a>
            <a class="nav-link" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a>
            <hr class="mx-3 opacity-10">
            <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-gray-800 mb-0">Attendance Overview</h3>
                <p class="text-muted small mb-0">Real-time monitoring for <?php echo date('F d, Y'); ?></p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-circle-fill text-success me-1 small"></i> System Live
                </span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card border-start border-success border-4 p-3 h-100">
                    <div class="small fw-bold text-success mb-1 text-uppercase">Present</div>
                    <div class="h2 fw-bold mb-0" id="count-present">0</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-warning border-4 p-3 h-100">
                    <div class="small fw-bold text-warning mb-1 text-uppercase">Late</div>
                    <div class="h2 fw-bold mb-0" id="count-late">0</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card border-start border-danger border-4 p-3 h-100">
                    <div class="small fw-bold text-danger mb-1 text-uppercase">Absent</div>
                    <div class="h2 fw-bold mb-0" id="count-absent">0</div>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-gray-800"><i class="bi bi-broadcast me-2 text-primary"></i>Live Scans</h5>
                
                <a href="clear_attendance.php" 
                   class="btn btn-sm btn-danger px-3 fw-bold shadow-sm" 
                   onclick="return confirm('WARNING: This will clear all present/late scans for today and reset the dashboard. Proceed?')">
                    <i class="bi bi-trash3-fill me-1"></i> Clear Scans
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Student Name</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0">Time In</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-body">
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                <span class="text-muted">Loading attendance data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function refreshData() {
            // Update the table list
            $.get('fetch_attendance.php', function(data) {
                $('#attendance-body').html(data);
            });

            // Update the top 3 stat boxes
            $.getJSON('fetch_stats.php', function(data) {
                $('#count-present').text(data.present);
                $('#count-late').text(data.late);
                $('#count-absent').text(data.absent);
            });
        }

        // Auto-refresh every 2 seconds for real-time feel
        $(document).ready(function() {
            refreshData();
            setInterval(refreshData, 2000);
        });
    </script>
</body>
</html>