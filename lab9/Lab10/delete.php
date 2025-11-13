<?php
include 'connect.php';
if(isset($_GET['deleteid']))
{
    $ID=$_GET['deleteid'];
    $sql="DELETE FROM student_data WHERE ID = $ID";
    $result=mysqli_query($conn,$sql);
    if($result)
    {
        echo"Record deleted successfully";
        header('location:index.php');
    }
    else{
        echo "Couldn't delete record";
    }
}
?>