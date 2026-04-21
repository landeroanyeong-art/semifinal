<?php
session_start();
include "db.php";
header('Content-Type: application/json');

$teacher_id = $_SESSION['teacher_id'] ?? 1;
$today = date("Y-m-d");

// 1. Get total students registered to this teacher
$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM students WHERE teacher_id = '$teacher_id'");
$total_students = mysqli_fetch_assoc($total_res)['total'];

// 2. Get counts for Present and Late from attendance table
$stats = ['present' => 0, 'late' => 0, 'absent' => 0];
$res = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM attendance WHERE teacher_id = '$teacher_id' AND date = '$today' GROUP BY status");

while ($row = mysqli_fetch_assoc($res)) {
    $status = strtolower($row['status']);
    if (isset($stats[$status])) {
        $stats[$status] = (int)$row['count'];
    }
}

// 3. Logic: Absent = Total Students - (Present + Late)
$stats['absent'] = max(0, $total_students - ($stats['present'] + $stats['late']));

echo json_encode($stats);