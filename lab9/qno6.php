<!DOCTYPE html>
<html lang="en">
<head>
   
</head>
<body>
    <form action="" method="post">
        <h2>Enter 2 numbers</h2>
        <input type="number" name="num1">
        <input type="number" name="num2">
        <input type="submit" name="Submit">
    </form>
    <?php
    if(isset($_POST['Submit']))
    {
        $num1=$_POST["num1"];
        $num2=$_POST["num2"];
        echo "<br>Sum:$num1+$num1=".$num1+$num2;
        echo "<br>Differece:$num1-$num2=".$num1-$num2;
        echo "<br>Multiplication:$num1*$num2=".$num1*$num2;
        echo "<br>Division:$num1/$num2=".$num1/$num2;
        echo "<br>Modulus:$num1%$num2=".$num1%$num2;
        echo "<br>Power:$num1^$num2=".pow($num1,$num2);
    }
    ?>
</body>
</html>
