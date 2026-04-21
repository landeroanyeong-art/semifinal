<?php
include "db.php";
date_default_timezone_set('Asia/Manila'); 

if (isset($_GET['uid'])) {
    $raw_uid = $_GET['uid'];
    $clean_uid = strtoupper(str_replace(' ', '', $raw_uid));
    $safe_uid = mysqli_real_escape_string($conn, $clean_uid);
    
    $today = date("Y-m-d");
    $current_time = date("H:i:s");

    // 1. Find the student by RFID
    $st_res = mysqli_query($conn, "SELECT * FROM students WHERE UPPER(REPLACE(rfid_uid, ' ', '')) = '$safe_uid' LIMIT 1");
    $student = mysqli_fetch_assoc($st_res);

    if (!$student) { 
        die("NOT_FOUND|Card not in system"); 
    }

    $t_id = $student['teacher_id'];
    $s_id = $student['id'];
    $fullname = $student['fullname'];

    // 2. Check Gate Security (Settings)
    $set_res = mysqli_query($conn, "SELECT class_start, late_time FROM settings WHERE teacher_id='$t_id'");
    $set = mysqli_fetch_assoc($set_res);
    
    $opening_time = $set['class_start'] ?? '07:00:00';
    $late_time = $set['late_time'] ?? '08:30:00';

    if (time() < strtotime($today . " " . $opening_time)) {
        die("LOCKED|Gate opens at " . date("h:i A", strtotime($opening_time)));
    }

    // 3. Determine Status (Present vs Late)
    $status = (time() > strtotime($today . " " . $late_time)) ? 'Late' : 'Present';

    // 4. Update Live Status in 'students' table (for Dashboard stats)
    mysqli_query($conn, "UPDATE students SET status = '$status', last_scan = NOW() WHERE id = '$s_id'");

    // 5. Record in permanent 'attendance' table if not already scanned today
    $check = mysqli_query($conn, "SELECT * FROM attendance WHERE student_id = '$s_id' AND date = '$today'");
    
    if (mysqli_num_rows($check) == 0) {
        $log_sql = "INSERT INTO attendance (student_id, teacher_id, date, time_in, status) 
                    VALUES ('$s_id', '$t_id', '$today', '$current_time', '$status')";
        if (mysqli_query($conn, $log_sql)) {
            echo "SUCCESS|$status|$fullname";
        } else {
            echo "ERROR|Database failed";
        }
    } else {
        echo "SUCCESS|ALREADY_SCANNED|$fullname";
    }
}
?>