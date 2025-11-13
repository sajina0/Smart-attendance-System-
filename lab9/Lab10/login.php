<?php
include 'connect.php';
?>

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

    <h2>Log In</h2>
    <form action="" method="POST">
        Username: <input type="text" name="Name" required> <br>
        Password: <input type="password" name="Password" required>
        <input type="submit" name="Submit" value="Login">
    </form>

    <?php
    if (isset($_SESSION['lockout'])) {
        $elapsed = time() - $_SESSION['lockout'];
        $remaining = 20 - $elapsed;

        if ($remaining > 0) {
            echo "Too many failed attempts. Try again in $remaining seconds.";
            exit;
        } else {
            unset($_SESSION['lockout']);
            $_SESSION['Chances'] = 3;
        }
    }

    if (!isset($_SESSION['Chances'])) {
        $_SESSION['Chances'] = 3;
    }

    if (isset($_POST['Submit'])) {
        $Name = $_POST['Name'];
        $Password = md5($_POST['Password']);

        $sql = "SELECT * FROM logintable WHERE Name='$Name' AND Password='$Password'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $_SESSION['login'] = true;
            $_SESSION['Name'] = $Name;
            $_SESSION['Chances'] = 3;
            header('Location: Q2.php');
            exit;
        } else {
            $_SESSION['Chances']--;

            if ($_SESSION['Chances'] <= 0) {
                $_SESSION['lockout'] = time();
                echo "Too many failed attempts. Locked out for 2 minutes.";
            } else {
                echo "Invalid login. {$_SESSION['Chances']} attempts left.";
            }
        }
    }
    ?>
</body>
</html>
