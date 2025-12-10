<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $student_name = $_POST['student_name'] ?? '';
    $qr_data = $_POST['qr_data'] ?? '';

    if (empty($student_id) || empty($qr_data)) {
        echo "<h2>Invalid data. Please try again.</h2>";
        exit;
    }

    // Extract teacher_id and date from QR data (QR_12_2025-10-10)
    $parts = explode('_', $qr_data);
    $teacher_id = $parts[1] ?? 0;
    $date = $parts[2] ?? date('Y-m-d');

    // Check if already marked
    $check = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ?");
    $check->bind_param("is", $student_id, $date);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        echo "<h2> Attendance already marked for today.</h2>";
        exit;
    }

    // Insert attendance record
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, student_name, qr_data, teacher_id, attendance_date, time_marked) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issis", $student_id, $student_name, $qr_data, $teacher_id, $date);

    if ($stmt->execute()) {
        echo "<h2> Attendance Marked Successfully!</h2>";
        echo "<p><strong>Student:</strong> $student_name (ID: $student_id)</p>";
        echo "<p><strong>Date:</strong> $date</p>";
        echo "<p><strong>QR:</strong> $qr_data</p>";
    } else {
        echo "<h2> Error saving attendance.</h2>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student - Mark Attendance</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .scanner-container {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 350px;
    }
    h2 {
      color: #003366;
      margin-bottom: 15px;
    }
    #reader {
      width: 100%;
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid #ddd;
      margin-bottom: 15px;
    }
    #message {
      font-weight: bold;
      margin-top: 10px;
    }
    button {
      background-color: #003366;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="scanner-container">
    <h2>Scan QR to Mark Attendance</h2>
    <div id="reader"></div>
    <p id="message">Point your camera to the QR code.</p>
    <button onclick="stopScanner()">Stop Scanner</button>
  </div>  
</body>
</html>
