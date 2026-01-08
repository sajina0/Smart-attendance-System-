<?php
session_start();
include 'db_connect.php';

// Only admin can approve
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized Access");
}

// Check if student ID is sent
if (!isset($_GET['student_id'])) {
    die("No student ID provided.");
}

$student_id = intval($_GET['student_id']);

// Update student status to active/approved
$update = mysqli_query($conn, "
    UPDATE student 
    SET status = 'active'
    WHERE student_id = $student_id
");

if ($update) {
    header("Location: verify_accounts.php?success=1");
    exit;
} else {
    echo "Failed to approve student: " . mysqli_error($conn);
}
?>
