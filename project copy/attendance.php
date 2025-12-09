<?php
session_start();
include 'db_connect.php';

// Check student login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['token'])) {
    die("Invalid attendance link.");
}

$token = $_GET['token'];

// Get session from token
$stmt = $conn->prepare("SELECT * FROM sessions WHERE token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired attendance link.");
}

$session = $result->fetch_assoc();
$class_id = $session['class_id'];
$date = $session['session_date'];
$time_marked = date('H:i:s');
$device_id = 'mobile';

// Check if attendance already marked
$check = $conn->prepare("SELECT * FROM attendance WHERE student_id=? AND class_id=? AND date=?");
$check->bind_param("iis", $student_id, $class_id, $date);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    $message = "Attendance already marked.";
} else {
    // Insert attendance
    $status = 'present';
    $stmtInsert = $conn->prepare("
        INSERT INTO attendance (student_id, class_id, time_marked, date, time_in, status, device_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmtInsert->bind_param("iisssss", $student_id, $class_id, $time_marked, $date, $time_marked, $status, $device_id);
    $stmtInsert->execute();
    $message = "Attendance recorded successfully!";
}

// Redirect to dashboard
header("refresh:2;url=student_dashboard.php");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance Status</title>
</head>
<body>
    <h2><?= htmlspecialchars($message) ?></h2>
    <p>You will be redirected to your dashboard...</p>
</body>
</html>
