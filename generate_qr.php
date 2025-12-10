<?php
session_start();
include 'db_connect.php';
require __DIR__ . '/vendor/autoload.php';
include "config.php";
use Endroid\QrCode\Builder\Builder;

// Only teacher can access
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'teacher') {
    echo "Unauthorized Access";
    exit;
}

$teacher_id = $_SESSION['user_id'];
$success = "";
$qrImage = "";

// Generate attendance QR code
if (isset($_POST['generate'])) {
    $class_id = intval($_POST['class_id']);
    $session_date = $_POST['session_date'];
    $subject_id = 1;
    // Ensure unique token
    do {
        $token = bin2hex(random_bytes(8));
        $check = $conn->prepare("SELECT id FROM sessions WHERE token = ?");
        $check->bind_param("s", $token);
        $check->execute();
        $result = $check->get_result();
    } while ($result->num_rows > 0);
    // Insert session
    $stmt = $conn->prepare("INSERT INTO sessions (class_id, teacher_id, session_date, token) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $class_id, $teacher_id, $session_date, $token);

    if ($stmt->execute()) {
        $success = "Attendance QR code generated successfully!";
        // The QR code links to attendance.php with the token
        $attendance_link = "{$ip_address}:{$app_port}/attendance.php?token=$token&class=$class_id&date=$session_date&subject_id=$subject_id";

        // Generate QR code as base64 image
        $qr = Builder::create()
            ->data($attendance_link)
            ->size(300)
            ->margin(10)
            ->build();
        $qrImage = $qr->getDataUri();
    } else {
        $success = "Error generating attendance QR code!";
    }

    $stmt->close();
}

// Fetch classes securely
$stmt = $conn->prepare("SELECT * FROM class WHERE teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Attendance QR Code</title>
    <style>
        body {
            font-family: Arial;
            padding:30px; 
            background:#f5f5f5; 
        }
        form { 
            background:#fff; 
            padding:20px; 
            width:400px; 
            margin:auto; 
            border-radius:8px; 
            box-shadow:0 0 10px rgba(0,0,0,0.1);}
        input, select, button {
            width:100%; 
            padding:10px; 
            margin:10px 0; 
            border-radius:5px; 
            border:1px solid #ccc;
        }
        button { 
            background:#4CAF50; 
            color:white; 
            border:none; 
            cursor:pointer; 
        }
        .success { 
            color:green; 
            text-align:center; 
            margin-bottom:15px; 
        }
        .qr { 
            text-align:center; 
            margin-top:20px; 
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Generate Attendance QR Code</h2>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST">
    <label>Select Class:</label>
    <select name="class_id" required>
        <option value="">-- Select Class --</option>
        <option value="">4th semester</option>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
        <?php endwhile; ?>
    </select>
    <label>Select subject:</label>
    <select name="subject_id" required>
        <option value="">-- Select subject --</option>
        <option value="">SL</option>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['subject_name']) ?></option>
        <?php endwhile; ?>
    </select>
    <label>Session Date:</label>
    <input type="date" name="session_date" required>

    <button type="submit" name="generate">Generate QR Code</button>
</form>

<?php if ($qrImage): ?>
    <div class="qr">
        <p>Scan this QR code to mark attendance:</p>
        <img src="<?= $qrImage ?>" alt="Attendance QR Code">
    </div>
<?php endif; ?>

</body>
</html>
