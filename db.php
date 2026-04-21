<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "attendance";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// AUTOMATIC RESET: After 8 hours (480 mins) of inactivity, status flips to Absent.
$master_interval = 480; 
$reset_sql = "UPDATE students SET status = 'Absent' 
              WHERE last_scan < (NOW() - INTERVAL $master_interval MINUTE) 
              AND status != 'Absent'";
mysqli_query($conn, $reset_sql);
?>