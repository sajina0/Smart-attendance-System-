<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
      <nav>
        <a href="Q2.php">Home</a>
        <a href="login.php">Login</a>
        <a href="sign.php">Sign Up</a>
        <a href="logout.php">Logout</a>
    </nav>
    <?php
        include 'connect.php';
        $_SESSION = [];
        session_unset();
        session_destroy();?>
</body></html>
