<?php
session_start();
include "db.php";
date_default_timezone_set('Asia/Manila');

$teacher_id = $_SESSION['teacher_id'] ?? 1;
$today = date("Y-m-d");

// We group by student ID to prevent duplicate rows if a student scans twice
$query = "SELECT 
            s.fullname, 
            s.student_id as id_num,
            IFNULL(a.status, 'Absent') as live_status, 
            MAX(a.time_in) as latest_time 
          FROM students s 
          LEFT JOIN attendance a ON s.id = a.student_id AND a.date = '$today'
          WHERE s.teacher_id = '$teacher_id' 
          GROUP BY s.id
          ORDER BY latest_time DESC, s.fullname ASC";

$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        $status = $row['live_status'];
        $time_display = ($row['latest_time']) ? date("h:i A", strtotime($row['latest_time'])) : "---";

        $badge_class = 'bg-danger-subtle text-danger border-danger'; 
        if ($status == 'Present') $badge_class = 'bg-success-subtle text-success border-success';
        elseif ($status == 'Late') $badge_class = 'bg-warning-subtle text-warning border-warning';

        echo "<tr>
                <td>
                    <div class='d-flex align-items-center'>
                        <div class='avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center' style='width:35px; height:35px;'>
                            <i class='bi bi-person text-secondary'></i>
                        </div>
                        <div>
                            <div class='fw-bold text-dark'>{$row['fullname']}</div>
                            <div class='text-muted small'>ID: {$row['id_num']}</div>
                        </div>
                    </div>
                </td>
                <td><span class='badge rounded-pill border px-3 py-2 $badge_class'>$status</span></td>
                <td><span class='text-muted small fw-bold'><i class='bi bi-clock me-1'></i>$time_display</span></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center py-5 text-muted'>No students registered.</td></tr>";
}
?>