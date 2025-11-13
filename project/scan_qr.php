<?php 
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
  header("Location: landing.php");
  exit;
}
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['fullname'] ?? 'Unknown Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Scan QR - Smart Attendance</title>
  <script src="https://unpkg.com/html5-qrcode"></script>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #F4F6F7;
      text-align: center;
      padding: 40px;
    }
    h2 {
      color: #4A148C;
    }
    #reader {
      width: 320px;
      margin: 30px auto;
      border: 3px solid #BA68C8;
      border-radius: 10px;
      background: #fff;
      padding: 10px;
    }
    .message {
      margin-top: 20px;
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #4A148C;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }
    .back-btn:hover {
      background-color: #7B1FA2;
    }
  </style>
</head>
<body>
  <h2>📷 Scan QR to Mark Attendance</h2>
  <div id="reader"></div>
  <div id="result" class="message">Align your camera with the QR code...</div>

  <a href="student_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>

  <script>
    const studentId = "<?php echo $student_id; ?>";
    const studentName = "<?php echo addslashes($student_name); ?>";
    const html5QrCode = new Html5Qrcode("reader");

    function onScanSuccess(decodedText) {
      html5QrCode.stop().then(() => {
        document.getElementById("result").innerHTML = "✅ Scanned Successfully! Processing...";
      });

      // Send scanned data + student info to PHP
      fetch("mark_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `qr_data=${encodeURIComponent(decodedText)}&student_id=${encodeURIComponent(studentId)}&student_name=${encodeURIComponent(studentName)}`
      })
      .then(response => response.text())
      .then(data => {
        document.body.innerHTML = data;
      })
      .catch(error => {
        document.getElementById("result").innerHTML = "l Error: " + error;
      });
    }

    html5QrCode.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: 250 },
      onScanSuccess
    ).catch(err => {
      document.getElementById("result").innerHTML = "Camera access denied or unavailable.";
      console.error(err);
    });
  </script>
</body>
</html>
