<?php
session_start();
include "db.php";

if(!isset($_SESSION['teacher_id'])) exit;

$teacher_id = $_SESSION['teacher_id'];
$today = date('Y-m-d');

// 1. Wipe Live Dashboard status
mysqli_query($conn, "UPDATE students SET status = 'Absent', last_scan = NULL WHERE teacher_id = '$teacher_id'");

// 2. Wipe today's history logs
mysqli_query($conn, "DELETE FROM attendance WHERE teacher_id = '$teacher_id' AND date = '$today'");

header("Location: dashboard.php?msg=cleared");
?>