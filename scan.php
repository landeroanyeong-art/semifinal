<?php
include "db.php";
date_default_timezone_set('Asia/Manila'); 

$secret_key = "my_secret_arduino_key";
$api_key = isset($_GET['api_key']) ? $_GET['api_key'] : '';

if ($api_key !== $secret_key) {
    die("UNAUTHORIZED");
}

if (isset($_GET['uid'])) {
    $uid = mysqli_real_escape_string($conn, $_GET['uid']);
    $teacher_id = 1; 

    // 1. Get the Late Time you set on the dashboard
    $set_res = mysqli_query($conn, "SELECT late_time FROM settings WHERE teacher_id='$teacher_id'");
    $setting = mysqli_fetch_assoc($set_res);
    $late_limit = $setting['late_time'] ?? '08:00:00';

    $current_time = date("H:i:s");
    
    // 2. Logic: If current time is past the limit, mark as Late
    $status = ($current_time > $late_limit) ? 'Late' : 'Present';

    $student_query = mysqli_query($conn, "SELECT id, fullname FROM students WHERE rfid_uid='$uid' AND teacher_id='$teacher_id'");
    
    if ($row = mysqli_fetch_assoc($student_query)) {
        $sid = $row['id'];
        $name = $row['fullname'];

        // 3. Update Database (Live Dashboard)
        mysqli_query($conn, "UPDATE students SET status='$status', last_scan=NOW() WHERE id='$sid'");

        // 4. Log to Database History
        mysqli_query($conn, "INSERT INTO attendance (student_id, teacher_id, date, time_in, status) 
                             VALUES ('$sid', '$teacher_id', CURDATE(), '$current_time', '$status')");

        // 5. DAILY SEPARATED CSV LOGGING (Your original Archive logic)
        $today = date('Y-m-d');
        $log_dir = 'logs/';
        if (!is_dir($log_dir)) { mkdir($log_dir, 0777, true); }
        
        // This line ensures each day gets its own file name
        $filename = $log_dir . "attendance_" . $today . ".csv";
        
        $file_exists = file_exists($filename);
        $handle = fopen($filename, 'a');

        if (!$file_exists) {
            // Header for the new daily file
            fputcsv($handle, ['Time', 'Student Name', 'Status']);
        }
        
        // Save the scan with the status
        fputcsv($handle, [$current_time, $name, $status]);
        fclose($handle);

        echo "Success: $name marked as $status";
    } else {
        echo "Error: Card not recognized";
    }
}
?>