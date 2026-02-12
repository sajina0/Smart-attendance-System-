<?php
include 'db_connect.php'; // Database connection

if(isset($_POST['email'])){
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $token = bin2hex(random_bytes(50)); // generate random token
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // token valid 1 hour

        // Save token in database
        $stmt = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset email
        $resetLink = "http://yourwebsite.com/reset_password.php?token=".$token;
        $subject = "Password Reset Request";
        $message = "Click this link to reset your password: $resetLink";
        $headers = "From: no-reply@yourwebsite.com";

        mail($email, $subject, $message, $headers);

        echo "Check your email for the reset link!";
    } else {
        echo "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<style>
body{
    font-family: Arial, sans-serif;
    background: #6A1B9A;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.container{
    background: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 350px;
    text-align: center;
}
h2{
    margin-bottom: 20px;
    color: #333;
}
input[type="email"]{
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}
input[type="submit"]{
    width: 100%;
    padding: 10px;
    background: #6A1B9A;
    border: none;
    color: white;
    font-size: 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}
input[type="submit"]:hover{
    background: #6A1B9A;
}
.message{
    margin-top: 15px;
    font-size: 14px;
    color: green;
}
.error{
    margin-top: 15px;
    font-size: 14px;
    color: red;
}
</style>
</head>
<body>
<div class="container">
<!-- HTML form -->
<form method="POST">
    Enter your email: <input type="email" name="email" required>
    <input type="submit" value="Reset Password">
</form>
</div>
