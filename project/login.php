<?php
session_start();

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$db = "smart_attendance";

$conn = mysqli_connect($host, $username, $password, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, fullname, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['fullname'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'teacher') {
                    header('Location: Teacher_dashboard.php');
                    exit;
                } elseif ($user['role'] === 'student') {
                    header('Location: student_dashboard.php');
                    exit;
                } else {
                    $error = "Access denied. Only students and teachers can log in.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email.";
        }


        $stmt->close();
    }
}

// Already logged-in users redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'teacher') {
        header('Location: Teacher_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'student') {
        header('Location: student_dashboard.php');
        exit;
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: landing.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Attendance System</title>
    <style>
    * { 
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Verdana, sans-serif; 
    }
    body {
        background: linear-gradient(135deg, #E1BEE7, #BA68C8);
        display: flex; 
        flex-direction: column; 
        min-height: 100vh; 
        justify-content: center;
        align-items: center;
        color: #212121;
    }
    .form-container { 
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        width: 380px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .form-container h2 {
        text-align: center;
        margin-bottom: 25px; 
        font-size: 28px; 
        color: #6A1B9A;
    }
    .form-group { 
        margin-bottom: 20px;
    }
    .form-group label { 
        display: block;
        margin-bottom: 8px;
        font-weight: 500; 
        color: #333; 
    }
    .form-group input { 
        width: 100%; 
        padding: 12px;
        border: 1px solid #ccc; 
        border-radius: 10px; 
        background: #fafafa; 
        font-size: 16px; 
    }
    .form-btn { 
        width: 100%; 
        padding: 12px; 
        border: none; 
        border-radius: 10px; 
        background: #BA68C8; 
        color: #fff; 
        font-size: 16px; 
        font-weight: 600; 
        cursor: pointer; 
        transition: 0.3s ease;
    }
    .form-btn:hover { 
        background: #8E24AA;
    }
    .form-footer {
        text-align: center; 
        margin-top: 15px; 
    }
    .form-footer a {
        color: #6A1B9A; 
        text-decoration: none; 
        font-weight: 500;
    }
    .error-msg {
        background: #ffebee;
        color: #c62828;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: 500;
    }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login to Your Account</h2>
        <?php if (!empty($error)): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form  method="post">
            <div class="form-group">
                <label for="email">Email</label>                   
                <input type="email" id="email" name="email" placeholder="Enter your email" required>    
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="form-btn">Login</button>
            <div class="form-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </form>
    </div>
</body>
</html>
