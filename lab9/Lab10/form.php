<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        a{text-decoration: none; color:black; background-color:white; padding:5px;}
        
    </style>
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
    if(!empty($_SESSION['Name'])){
        $Name=$_SESSION['Name'];
        $sql="SELECT * FROM logintable WHERE Name='$Name'";
        $result=mysqli_query($conn,$sql);
        $row=mysqli_fetch_assoc($result);
    }
    else{
        header('location:login.php');
        exit;
    }
    ?>
    <h1>Welcome <?php echo $row['Name'];?></h1>
</body>
</html>
