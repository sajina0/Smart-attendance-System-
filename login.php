<?php
session_start();
include 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                // Check email verification only for teacher and student
                if (($row['role'] === 'teacher' || $row['role'] === 'student') && $row['is_verified'] != 1) {
                    $error = "Please verify your email before login.";
                } else {
                    // Set session variables
                    $_SESSION['user_id']  = $row['user_id'];
                    $_SESSION['role']     = $row['role'];
                    $_SESSION['fullname'] = $row['fullname'];

                    // Redirect based on role
                    switch ($row['role']) {
                        case 'admin':
                            header("Location: admin_dashboard.php");
                            break;
                        case 'teacher':
                            header("Location: teacher_dashboard.php");
                            break;
                        case 'student':
                            header("Location: student_dashboard.php");
                            break;
                        default:
                            // fallback just in case
                            header("Location: login.php");
                    }
                    exit;
                }

            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
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
    .box{ 
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        width: 380px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .box h2 {
        text-align: center;
        margin-bottom: 25px; 
        font-size: 28px; 
        color: #6A1B9A;
    }
    .apple group { 
        margin-bottom: 20px;
    }
    .apple label { 
        display: block;
        margin-bottom: 8px;
        font-weight: 500; 
        color: #333; 
    }
    .apple input { 
        width: 100%; 
        padding: 12px;
        border: 1px solid #ccc; 
        border-radius: 10px; 
        background: #fafafa; 
        font-size: 16px;
        margin-bottom:15px;
    }
    .btn { 
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
    .btn:hover { 
        background: #8E24AA;
    }
    .footer {
        text-align: center; 
        margin-top: 15px; 
    }
    .footer a {
        color: #6A1B9A; 
        text-decoration: none; 
        font-weight: 500;
    }
    .error {
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
    <div class="box">
        <h2>Login to Your Account</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form  method="post">
            <div class="apple">
                <label for="email">Email</label>                   
                <input type="email" id="email" name="email" placeholder="Enter your email" required>    
            </div>
            <div class="apple">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
        </form>
    </div>
</body>
</html>
