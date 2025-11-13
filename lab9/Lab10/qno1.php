<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
    <form method="POST">
        Name: <input type="text" name="Name"> <br>
        Email: <input type="email" name="Email"> <br>
        Gender: <input type="radio" name="Gender" value="Male">Male
        <input type="radio" name="Gender" value="Female">Female
        <input type="radio" name="Gender" value="Other">Other <br>
        Phone: <input type="number" name="Phone"> <br>
        <input type="submit" name="Submit">
        <a href="view.php">View data</a>
    </form>
    <?php
    include 'connect.php';
    if(isset($_POST['Submit']))
    {
        $Name=$_POST['Name'];
        $Email=$_POST['Email'];
        $Gender=$_POST['Gender'];
        $Phone=$_POST['Phone'];
         $sql = "insert into user_info(Name,Email, Gender,Phone)VALUES('$Name','$Email','$Gender',$Phone);";
         $result = mysqli_query($conn,$sql);
    if($result){
         echo " sucessfully inserted";
      
    }else{
         echo "not sucessfull: ".mysqli_error($conn);}}
    ?></body></html>
