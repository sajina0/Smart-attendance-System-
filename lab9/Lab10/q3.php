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
    <form method="POST">
        Name: <input type="text" name="Name"> <br>
        Age: <input type="number" name="Age"> <br>
        GPA: <input type="number" name="GPA"> <br>
        Batch: <input type="number" name="Batch"> <br>
        Faculty: <input type="text" name="Faculty"><br>
        <input type="submit" name="Submit">
    </form>
    <?php
    include 'connect.php';
    if(isset($_POST['Submit']))
    {
        $Name=$_POST['Name'];
        $Age=$_POST['Age'];
        $GPA=$_POST['GPA'];
        $Batch=$_POST['Batch'];
        $Faculty=$_POST['Faculty'];
         $sql = "insert into student_data(Name,Age, GPA,Batch,Faculty)VALUES('$Name', '$Age', '$GPA', '$Batch', '$Faculty')";
         $result = mysqli_query($conn,$sql);
    if($result){
         echo " sucessfully inserted";
      
    }else{
         echo "not sucessfull: ".mysqli_error($conn);
    }
    }
    ?>
    <br>    <br>     <br>    <br> <hr>
    <table border cellpadding=5>
    <tr>
        <td>ID</td>
        <td>Name</td>
        <td>Age</td>
        <td>GPA</td>
        <td>Batch</td>
        <td>Faculty</td>
        <td>Operation</td>
    </tr>
<?php
    include "connect.php";
    $sql = "select * from student_data";
    $result = mysqli_query($conn,$sql);
    $num=mysqli_num_rows($result);
    if($num>0){
        while($row=mysqli_fetch_assoc($result)){
            $id=$row['ID'];
            echo "<tr>";
            echo "<td>".$row['ID']."</td>";
            echo "<td>".$row['Name']."</td>";
            echo "<td>".$row['Age']."</td>";
            echo "<td>".$row['GPA']."</td>";
            echo "<td>".$row['Batch']."</td>";
            echo "<td>".$row['Faculty']."</td>";
            echo "<td><button><a href='delete.php?deleteid=$id'>Delete</a></button></td>
            <td><button><a href='update.php?updateid=$id'>Update</a></button></td>";
            echo "</tr>";
        }
    }
?>
</table>
</body>
</html>
