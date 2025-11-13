<html>
<head>
</head>
<body>
    <a href="Q1.php">GO back</a>
<table border>
    <tr><td>Name</td><td>Email</td><td>Gender</td>  <td>Phone</td>
        <td>Created At</td></tr>
<?php
    include "connect.php";
    $sql = "select * from user_info";
    $result = mysqli_query($conn,$sql);
    $num=mysqli_num_rows($result);
    if($num>0){
        while($row=mysqli_fetch_assoc($result)){
            echo "<tr>";
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>".$row['gender']."</td>";
            echo "<td>".$row['phone']."</td>";
            echo "<td>".$row['created_at']."</td>";
            echo "</tr>";
        }
    }
?>
</table>
</body>
</html>