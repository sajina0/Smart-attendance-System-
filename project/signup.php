<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$db = "smart_attendance";

// Connect to database
$conn = mysqli_connect($host, $username, $password, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $semester = trim($_POST['semester'] ?? '');

    // Validation
    if (empty($fullname) || empty($email) || empty($username) || empty($password) || empty($role) || empty($department)) {
        $error = "All fields are required (except semester for teachers).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if email or username already exists
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email or username already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (fullname, email, username, password, role, department, semester) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $fullname, $email, $username, $hashed_password, $role, $department, $semester);

            if ($stmt->execute()) {
                $user_id =$stmt->insert_id;
                 if ($role === 'student') {
                    $conn->query("INSERT INTO student (user_id) VALUES ($user_id)");
                } elseif ($role === 'teacher') {
                    $conn->query("INSERT INTO teacher (user_id) VALUES ($user_id)");
                }
                header("Location: login.php");
                exit;
            } else {
                $error = "Error creating account. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
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
        </select>

        <label>Department:</label>
        <select name="department" required>
            <option value="">Select dept</option>
            <option value="BCA">BCA</option>
            <option value="Bcscit">Bcscit</option>
        </select>

        <label>Semester (for students):</label>
        <input type="number" name="semester" min="1">

        <label>
            <input type="checkbox" required> I agree to the Terms of Service
        </label>

        <button type="submit">Sign Up</button>

        <p style="text-align:center; margin-top:15px;">Already have an account? <a href="login.php">Login</a></p>
    </form>
</body>
</html>
