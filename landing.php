<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$db = "smart_attendance";

$conn = mysqli_connect($host, $username, $password, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Email and Password are required.";
    } else {
        $sql = "SELECT id, fullname, role, password FROM user WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // ✅ If password is hashed, use password_verify
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                // ✅ Redirect based on role
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

// ✅ Already logged in user redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'teacher') {
        header('Location: Teacher_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'student') {
        header('Location: student_dashboard.php');
        exit;
    }
}

// ✅ Logout
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
    <title>QR Attendance System</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Verdana, sans-serif;
        }
         body{
            background: #FAFAFA;
            display: flex; 
            min-height: 100vh;
            flex-direction: column;
            color: #212121;
            padding-top: 100px;
        }
        .container{
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header{
           display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px ;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: 0 2px 10px ;
            background: #6A1B9A;
            color: white;
        }
         nav ul {
            display: flex;
            list-style: none;
        }
        nav ul li {
            margin-left: 30px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        nav ul li a:hover {
            color: #ffcc00;
        }
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 80px 0;
            background: linear-gradient( #6A1B9A, #8E24AA);
            color: white;
        }
        .photo-content {
            flex: 1;
            padding-right: 50px;
        }        
        .photo-content h2 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
        }
         .photo-content a {
            display: inline-block;
            padding: 12px 25px;
            background: #BA68C8; 
            color: #212121;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
        }
        .photo-content a:hover {
            background: #8E24AA; 
            color: white;
        }
        .photo-content p {
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .feature {
            background: white;
            padding: 80px 0;
            border-radius: 20px 20px 0 0;
        }
           .section-title {
            text-align: center;
            margin-bottom: 50px;
            color: #212121;
        }
        .section-title h2 {
            font-size: 36px;
            margin-bottom: 15px;
            color: #6A1B9A;
        }
        .section-title p {
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        .features { 
            background:white;
            padding:80px 20px;
            text-align:center;
        }
        .features h2 {
            color:#6A1B9A;
            margin-bottom:15px;
            font-size:32px;
        }
        .features p { 
            color:#666;
            max-width:600px;
            margin:0 auto 40px;
        }
        .feature-cards { 
            display:flex;
            justify-content:center;
            flex-wrap:wrap;
            gap:25px;
        }      
        .card {
            background:  #FAFAFA;
            border-radius: 15px;
            padding: 30px;
            width: 300px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }      
        .card:hover {
            border: 1px solid #BA68C8;
        }
        .card h3 {
            margin-bottom: 15px;
            color: #212121;
        }
        
        .card p {
            color: #666;
            line-height: 1.6;
        }
        .forms {
            display: flex;
            justify-content: center;
            padding: 80px 0;
        }
         .form-container {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #BA68C8;
            border-radius: 20px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
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
            color: #212121;
        }
        .form-group input {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 30px 0;
            margin-top: auto;
        }
          .form-footer a:hover {
            text-decoration: underline;
        }
        .form-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #BA68C8; 
            color: #212121;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }
        .form-btn:hover {
            background: #8E24AA;
            color:white
        }     
        .form-footer {
            text-align: center;
            margin-top: 20px;
        }      
        .form-footer a {
            color: #6A1B9A;
            text-decoration: none;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }
         .btn-login {
            background: white;
            color: #6A1B9A;
            border: 2px solid white;
        }
          .btn-login:hover {
           background: transparent;
           color: white;
        }
          .btn-signup {
           background: #BA68C8;
           color: #212121;
           border: 2px solid #BA68C8;
           margin-left: 15px;
        }
          .btn-signup:hover {
           background: transparent;
           color: #ffcc00;
        }
        .images {
            flex: 1;
            text-align: center;
        }
        .images img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <header>
        <div><h1>QR Attendance System</h1></div>
        <nav>
            <ul>
                <li><a href="">Home</a></li>
                <li><a href="">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
        </nav>
    </header>

    <div class="hero">
        <div class="photo-content">
            <h2>Modern Attendance Management with QR Technology</h2>
            <p>Mark attendance easily using QR codes — simple, fast, and reliable.</p>
            <a href="#Login">Get Started</a>
        </div>
        <div class="images">
            <img src="Attendance-Management-System.png" alt="">
        </div>
    </div>
    <div class="features" id="">
         <h2>Key Features</h2>
    <p>Our QR-based system offers everything you need for efficient attendance management.</p>
    <div class="feature-cards">
        <div class="card">
            <h3>QR Code Generation</h3>
            <p>Generate unique QR codes for each class and session with customizable parameters.</p>
        </div>
        <div class="card">
            <h3>Mobile Scanning</h3>
            <p>Students can scan QR codes using smartphones for quick attendance marking.</p>
        </div>
        <div class="card">
            <h3>Real-time Report</h3>
            <p>Generate detailed attendance reports instantly to monitor student performance.</p>
        </div>
    </div>
    </div>
    <footer>
        <div class="container">
            <p>© 2025 QR Attendance System</p>
        </div>
    </footer>
</body>
</html> 


