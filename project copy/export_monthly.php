<?php
include 'db_connect.php'; // Your database connection file

// Check if month and year are provided
if (!isset($_GET['month']) || !isset($_GET['year'])) {
    die(" Month and Year required. Example: export_monthly.php?month=10&year=2025");
}

$month = intval($_GET['month']);
$year = intval($_GET['year']);

// Prepare filename
$filename = "attendance_report_{$year}_{$month}.csv";

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header row
fputcsv($output, ['Student ID', 'Student Name', 'Class', 'Date', 'Status', 'Time']);

// Fetch attendance records for given month/year
$query = "
    SELECT a.student_id, s.fullname AS student_name, c.class_name, 
           a.attendance_date, a.status, a.time_marked
    FROM attendance a
    JOIN users s ON a.student_id = s.id
    JOIN classes c ON a.class_id = c.id
    WHERE MONTH(a.attendance_date) = '$month' 
      AND YEAR(a.attendance_date) = '$year'
    ORDER BY a.attendance_date ASC
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ['No records found']);
}

fclose($output);
exit;
?>
