<?php
session_start();
include "db.php";

// 1. Security check
if(!isset($_SESSION['user'])) {
    echo json_encode(['present' => 0, 'absent' => 0]);
    exit;
}

// 2. Identify the Teacher
$teacher_username = $_SESSION['user'];
$teacher_query = mysqli_query($conn, "SELECT id FROM users WHERE username='$teacher_username'");
$teacher = mysqli_fetch_assoc($teacher_query);
$teacher_id = $teacher['id'];

// 3. Count Students who are CURRENTLY marked as 'Present'
// We check the 'students' table because db.php updates the 'status' column globally
$query_present = mysqli_query($conn, "SELECT COUNT(*) as total FROM students 
                                      WHERE teacher_id='$teacher_id' AND status='Present'");
$total_present = mysqli_fetch_assoc($query_present)['total'];

// 4. Count Students who are CURRENTLY marked as 'Absent'
$query_absent = mysqli_query($conn, "SELECT COUNT(*) as total FROM students 
                                     WHERE teacher_id='$teacher_id' AND status='Absent'");
$total_absent = mysqli_fetch_assoc($query_absent)['total'];

// 5. Send the data back to the Dashboard as JSON
echo json_encode([
    'present' => (int)$total_present,
    'absent' => (int)$total_absent
]);
?>