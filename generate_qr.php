<?php
session_start();
include 'db_connect.php';
require __DIR__ . '/vendor/autoload.php';
use Endroid\QrCode\Builder\Builder;

// Only teachers can access
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'teacher') {
    die("Unauthorized Access");
}

$user_id = $_SESSION['user_id'];

// Get teacher_id from user_id
$teacher_query = $conn->prepare("SELECT teacher_id FROM teacher WHERE user_id = ?");
$teacher_query->bind_param("i", $user_id);
$teacher_query->execute();
$teacher_result = $teacher_query->get_result();

if ($teacher_result->num_rows === 0) {
    die("Teacher account not found. Please contact administrator.");
}

$teacher_row = $teacher_result->fetch_assoc();
$teacher_id = $teacher_row['teacher_id'];
$teacher_query->close();
$success = "";
$error = "";
$qrImage = "";
$session_id = null;

// Fetch classes assigned to this teacher
$class_stmt = $conn->prepare("SELECT class_id, class_name FROM classes WHERE teacher_id = ? ORDER BY class_name");
$class_stmt->bind_param("i", $teacher_id);
$class_stmt->execute();
$classes = $class_stmt->get_result();

// Fetch subjects for these classes
$subject_stmt = $conn->prepare("
    SELECT s.subject_id, s.sub_name, s.class_id 
    FROM subject s
    INNER JOIN classes c ON s.class_id = c.class_id
    WHERE c.teacher_id = ?
    ORDER BY s.sub_name
");
$subject_stmt->bind_param("i", $teacher_id);
$subject_stmt->execute();
$subjects = $subject_stmt->get_result();

$all_subjects_arr = [];
while ($row = $subjects->fetch_assoc()) {
    $all_subjects_arr[] = $row;
}
$subject_stmt->close();

// Handle QR generation
if (isset($_POST['generate'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request. Please try again.";
    } else {
        $class_id = intval($_POST['class_id']);
        $subject_id = intval($_POST['subject_id']);
        $session_date = $_POST['session_date'];

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $session_date)) {
            $error = "Invalid date format.";
        } elseif (strtotime($session_date) < strtotime(date('Y-m-d'))) {
            $error = "Session date cannot be in the past.";
        } elseif (!$class_id || !$subject_id) {
            $error = "Please select a Class and a Subject.";
        } else {
            // Validate class belongs to teacher
            $check = $conn->prepare("SELECT class_id FROM classes WHERE class_id = ? AND teacher_id = ?");
            $check->bind_param("ii", $class_id, $teacher_id);
            $check->execute();

            if ($check->get_result()->num_rows === 0) {
                $error = "Invalid class selection.";
            } else {
                // Validate subject belongs to class
                $check_sub = $conn->prepare("SELECT subject_id FROM subject WHERE subject_id = ? AND class_id = ?");
                $check_sub->bind_param("ii", $subject_id, $class_id);
                $check_sub->execute();

                if ($check_sub->get_result()->num_rows === 0) {
                    $error = "Invalid subject selection for the selected class.";
                } else {
                    // Check if session already exists
                    $check_sess = $conn->prepare(
                        "SELECT session_id, token FROM session 
                         WHERE class_id = ? AND subject_id = ? AND session_date = ? AND teacher_id = ? AND role = 'teacher'"
                    );
                    $check_sess->bind_param("iisi", $class_id, $subject_id, $session_date, $teacher_id);
                    $check_sess->execute();
                    $existing = $check_sess->get_result();

                    if ($existing->num_rows > 0) {
                        $row = $existing->fetch_assoc();
                        $token = $row['token'];
                        $session_id = $row['session_id'];
                        $success = "QR code generated successfully";

$attendance_link = "http://192.168.137.1/project/attendance.php?token=$token&class=$class_id&date=$session_date&subject_id=$subject_id";


                        $qr = Builder::create()
                            ->data($attendance_link)
                            ->size(300)
                            ->margin(10)
                            ->build();
                        $qrImage = $qr->getDataUri();
                    } else {
                        // Generate new token
                        do {
                            $token = bin2hex(random_bytes(16));
                            $chk = $conn->prepare("SELECT session_id FROM session WHERE token = ?");
                            $chk->bind_param("s", $token);
                            $chk->execute();
                        } while ($chk->get_result()->num_rows > 0);

                        $stmt = $conn->prepare(
                            "INSERT INTO session (class_id, teacher_id, student_id, subject_id, session_date, token, role) 
                             VALUES (?, ?, NULL, ?, ?, ?, 'teacher')"
                        );
                        $stmt->bind_param("iiiss", $class_id, $teacher_id, $subject_id, $session_date, $token);

                        if ($stmt->execute()) {
                            $session_id = $stmt->insert_id;
                            $success = "Attendance QR code generated successfully!";

$attendance_link = "http://192.168.137.1/project/attendance.php?token=" . urlencode($token);

                            $qr = Builder::create()
                                ->data($attendance_link)
                                ->size(300)
                                ->margin(10)
                                ->build();
                            $qrImage = $qr->getDataUri();
                        } else {
                            $error = "Error generating attendance QR code!";
                        }
                        $stmt->close();
                    }}
                $check_sub->close();
            }
            $check->close();
        }}}
// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Attendance QR Code</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 30px; 
            background: #F3E5F5; 
            color: #6B7280; 
        }
        form { 
            background: #FFF; 
            padding: 25px; 
            width: 450px; 
            margin: auto; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(106,27,154,0.15); 
            border-left: 6px solid #6A1B9A; 
        }
        input, select { 
            width: 100%; 
            padding: 12px; 
            margin: 12px 0; 
            border-radius: 6px; 
            border: 1px solid #ccc; 
        }
        input:focus, select:focus { 
            border-color: #6A1B9A; 
            outline: none; 
            box-shadow: 0 0 5px #BA68C8; }
        button { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border-radius: 
            6px; border: none; 
            cursor: pointer; 
            background: #6A1B9A; 
            color: white; 
            font-size: 15px; 
            font-weight: 600; 
        }
        button:hover { 
            background: #8E24AA; 
        }
        .success { 
            color: #4CAF50; 
            text-align: center; 
            margin-bottom: 15px; 
            font-weight: 600; 
        }
        .error { 
            color: #f56565; 
            text-align: center; 
            margin-bottom: 15px; 
            font-weight: 600; 
        }
        .qr { 
            text-align: center; 
            margin-top: 20px; 
        }
        a { 
            text-decoration: none; 
            display: block; 
            text-align: center; 
            margin-top: 10px; 
            color:#6A1B9A; 
        }
        a:hover { 
            color:#8E24AA; 
            text-decoration: underline; 
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Generate Attendance QR Code</h2>

<?php if ($success): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <label>Class</label>
    <select name="class_id" required>
        <option value="">-- Select Class --</option>
        <?php while ($c = $classes->fetch_assoc()): ?>
            <option value="<?= $c['class_id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Subject</label>
    <select name="subject_id" required>
        <option value="">-- Select Subject --</option>
        <!-- Options populated dynamically by JS -->
    </select>

    <label>Session Date</label>
    <input type="date" name="session_date" required min="<?= date('Y-m-d') ?>">

    <button type="submit" name="generate">Generate QR Code</button>
    <a href="teacher_dashboard.php">← Back to Dashboard</a>
</form>

<?php if ($qrImage): ?>
    <div class="qr">
        <p>Scan this QR code to mark attendance:</p>
        <img src="<?= $qrImage ?>" alt="Attendance QR Code">
    </div>
<?php endif; ?>

<script>
const allSubjects = <?= json_encode($all_subjects_arr) ?>;
const classSelect = document.querySelector('select[name="class_id"]');
const subjectSelect = document.querySelector('select[name="subject_id"]');

classSelect.addEventListener('change', function() {
    const selectedClass = parseInt(this.value);
    subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
    allSubjects.forEach(s => {
        if (s.class_id == selectedClass) {
            const opt = document.createElement('option');
            opt.value = s.subject_id;
            opt.text = s.sub_name;
            subjectSelect.appendChild(opt);
        }
    });
});
</script>

</body>
</html>
