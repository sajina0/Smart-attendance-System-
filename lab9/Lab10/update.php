<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        a{
            text-decoration:none;
            color:black;
        }
    </style>
</head>
<body>
    <?php
    include 'connect.php';
        $ID=$_GET['updateid'];
        $sql = "SELECT * FROM student_data where ID=$ID";
        $result = mysqli_query($conn,$sql);
        $row=mysqli_fetch_assoc($result);
        $Name=$row['Name'];
        $Age=$row['Age'];
        $GPA=$row['GPA'];
        $Batch=$row['Batch'];
        $Faculty=$row['Faculty'];
    if(isset($_POST['Submit']))
    {
        $Name=$_POST['Name'];
        $Age=$_POST['Age'];
        $GPA=$_POST['GPA'];
        $Batch=$_POST['Batch'];
        $Faculty=$_POST['Faculty'];
    $sql="UPDATE student_data SET ID=$ID, Name='$Name', Age=$Age, GPA=$GPA, Batch=$Batch, Faculty='$Faculty' WHERE ID=$ID";
    $result=mysqli_query($conn,$sql);
    if($result)
    {
        echo"Record updated successfully";
        header('location:index.php');
    }
    else{
        echo "Couldn't update record";
    }
}
?>
         <form method="POST">
        Name: <input type="text" name="Name" value="<?php echo $Name;?>"> <br>
        Age: <input type="number" name="Age" value="<?php echo $Age;?>"> <br>
        GPA: <input type="number" name="GPA" value="<?php echo $GPA;?>"> <br>
        Batch: <input type="number" name="Batch" value="<?php echo $Batch;?>"> <br>
        Faculty: <input type="text" name="Faculty" value="<?php echo $Faculty;?>"><br>
        <input type="submit" name="Submit">
    </form>
</table>
</body>
</html>

