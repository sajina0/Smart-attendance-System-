<!DOCTYPE html>
<html lang="en">
<head>
    
</head>
<body>
    <form action="" method="post">
        <h2>Please enter your name:</h2>
        <input type="text" name="Name">
        <input type="submit" name="Submit">
    </form>
    <?php
    if(isset($_POST['Submit']))
    {
        $Name=$_POST['Name'];
        echo "Hello $Name";
    }
    ?>
</body>
</html>
