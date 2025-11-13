    <?php
    $host="localhost";
    $username="root";
    $password="";
    $db="dav";
    $conn = mysqli_connect($host,$username,$password,$db);
    if(!$conn){echo die("<h1>database is not connected: ".mysqli_connect_error()."</h1><hr>");
    }
     ?>
