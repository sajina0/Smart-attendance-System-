<?php
include 'db_connect.php';

if(isset($_GET['token'])){
    $token = $_GET['token'];

    // Check if token exists and is not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token=? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        if(isset($_POST['password'])){
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Update password and clear token
            $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE reset_token=?");
            $stmt->bind_param("ss", $password, $token);
            $stmt->execute();

            echo "Password successfully reset!";
        }
    } else {
        die("Invalid or expired token.");
    }
} else {
    die("No token provided.");
}
?>

<!-- HTML form -->
<form method="POST">
    Enter new password: <input type="password" name="password" required>
    <input type="submit" value="Set New Password">
</form>
