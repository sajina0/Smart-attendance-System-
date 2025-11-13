<?php
session_start();
include 'db_connect.php';

// Access control: only teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: landing.php");
    exit;
}

require_once 'phpqrcode/qrlib.php';

// Generate unique QR data for today
$teacher_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$code_data = "QR_" . $teacher_id . "_" . $today;

// Check if QR already exists
$stmt = $conn->prepare("SELECT * FROM qr_codes WHERE code_data = ?");
$stmt->bind_param("s", $code_data);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $insert = $conn->prepare("INSERT INTO qr_codes (code_data, created_at) VALUES (?, NOW())");
    $insert->bind_param("s", $code_data);
    $insert->execute();
}

// Generate QR image
$path = "qr_images/";
if (!is_dir($path)) mkdir($path, 0777, true);

$filename = $path . $code_data . ".png";
if (!file_exists($filename)) {
    QRcode::png($code_data, $filename, QR_ECLEVEL_L, 6);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR - Smart Attendance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #F4F6F7;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }
        .container {
            background: white;
            padding: 30px 50px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 380px;
        }
        h1 {
            color: #4A148C;
            font-size: 24px;
            margin-bottom: 15px;
        }
        p {
            color: #333;
            font-size: 16px;
        }
        img {
            margin-top: 20px;
            width: 250px;
            height: 250px;
            border: 4px solid #D1C4E9;
            border-radius: 10px;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #4A148C;
            color: white;
            border-radius: 8px;
            transition: 0.3s;
        }
        a.button:hover {
            background-color: #7B1FA2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Generated</h1>
        <p><strong>Date:</strong> <?php echo $today; ?></p>
        <p>Students can scan this QR to mark attendance.</p>
        <img src="<?php echo $filename; ?>" alt="QR Code">
        <br>
        <a href="Teacher_dashboard.php" class="button"> Back to Dashboard</a>
    </div>
</body>
</html>
