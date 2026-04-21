<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])) exit;

$teacher_username = $_SESSION['user'];
$teacher = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id FROM users WHERE username='$teacher_username'"));
$teacher_id = $teacher['id'];

$view_date = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : null;
$filename = $view_date ? "Attendance_Report_$view_date.csv" : "Full_Attendance_Report.csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');

$output = fopen('php://output', 'w');

// --- HEADER WITH STATUS COLUMN ---
fputcsv($output, array('DATE', 'TIME RECORDED', 'STUDENT NAME', 'STATUS/REMARKS'));

$sql = "SELECT a.date, a.time_in, s.fullname, a.status 
        FROM attendance a 
        JOIN students s ON a.student_id = s.id 
        WHERE a.teacher_id = '$teacher_id' " . 
        ($view_date ? "AND a.date = '$view_date' " : "") . 
        "ORDER BY a.date DESC, a.time_in ASC";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    // If status is blank in old database records, show 'Present'
    $final_status = !empty($row['status']) ? $row['status'] : 'Present';
    
    fputcsv($output, array(
        $row['date'], 
        date('h:i A', strtotime($row['time_in'])), 
        $row['fullname'], 
        $final_status
    ));
}

fclose($output);
?>