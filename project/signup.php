<?php
session_start();
include 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname   = trim($_POST['fullname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $role       = trim($_POST['role'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $batch      = trim($_POST['batch'] ?? '');

    // ----------- VALIDATION -----------
    if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($role) || empty($department)) {
        $error = "All fields are required (batch only for students).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $allowed_domains = ['gmail.com'];
        $email_domain = strtolower(substr(strrchr($email, "@"), 1));
        if (!in_array($email_domain, $allowed_domains)) {
            $error = "Only Gmail addresses are allowed (example: name@gmail.com).";
        }
    }

    if (!$error && strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    }

    // Check for existing email or username
    if (!$error) {
        $stmtCheck = $conn->prepare("SELECT user_id FROM users WHERE email=? OR username=?");
        $stmtCheck->bind_param("ss", $email, $username);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        if ($resultCheck->num_rows > 0) {
            $error = "Email or username already registered.";
        }
        $stmtCheck->close();
    }

    // ----------- INSERT USER -----------
    if (!$error) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $batch_value = ($role === 'student') ? (int)$batch : NULL;

        $stmt = $conn->prepare("INSERT INTO users (fullname, email, username, password, role, department, batch) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fullname, $email, $username, $hashed_password, $role, $department, $batch_value);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Generate verification token
            $token = bin2hex(random_bytes(16));
            $conn->query("UPDATE users SET verfication_token='$token' WHERE user_id=$user_id");

            // Add to student or teacher table
            if ($role === 'student') {
                $conn->query("INSERT INTO student (user_id) VALUES ($user_id)");
            } elseif ($role === 'teacher') {
                $conn->query("INSERT INTO teacher (user_id) VALUES ($user_id)");
            }

            // --------- Email Verification (optional for testing) ---------
            $verification_link = "http://localhost/project/verify.php?token=" . $token;
            $subject = "Verify Your Email - Smart Attendance";
            $message = "Hi $fullname,\n\nPlease click the link below to verify your email:\n$verification_link\n\nThank you!";
            $headers = "From: no-reply@smartattendance.com";
            mail($email, $subject, $message, $headers);
          

            $success = "Account created successfully! Please login after verification.";
            header("Location: login.php");
            exit;

        } else {
            $error = "Error creating account: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up | Smart Attendance</title>
<style>
    body {
        background-color: #FAFAFA;
        color: #212121;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    form {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 350px;
    }
    input, select {
        width: 100%;
        padding: 10px;
        margin: 8px 0 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    label {
        font-weight: bold;
    }
    button {
        width: 100%;
        padding: 10px;
        background:#8E24AA;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover {
        background-color: #BA68C8;
    }
    .error { color: red; margin-bottom: 10px; }
</style>
</head>
<body>
    <form method="POST" action="">
        <h2 style="text-align:center;">Create Account</h2>

        <?php if($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <label>Full Name:</label>
        <input type="text" name="fullname" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Role:</label>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
            <option value="admin">Admin</option>
        </select>

        <label>Department:</label>
        <select name="department" required>
            <option value="">Select dept</option>
            <option value="BCA">BCA</option>
            <option value="Bcscit">Bcscit</option>
            <option value="Faculty">Faculty</option>
        </select>
       <label>Batch (Students Only)</label>
       <input type="number" id="batch" name="batch" min="1" max="8" disabled>
       <script>
    const roleSelect = document.querySelector("select[name='role']");
    const semesterInput = document.getElementById("batch");

    roleSelect.addEventListener("change", function() {
        if (this.value === "student") {
            semesterInput.disabled = false;
        } else {
            semesterInput.disabled = true;
            semesterInput.value = "";
        }
    });
</script>

        <label>
            <input type="checkbox" required> I agree to the Terms of Service
        </label>

        <button type="submit">Sign Up</button>

        <p style="text-align:center; margin-top:15px;">Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
