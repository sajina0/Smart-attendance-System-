<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST" enctype="multipart/form-data">
        <fieldset>
            <legend>Registration</legend>
            <fieldset>
                <legend>Personal Info</legend>
                Firstname:
                <input type="text" name="Firstname">
                Lastname:
                <input type="text" name="Lastname">
                Age:
                <input type="number" name="Age"> <br> <br>
                Email:
                <input type="email" name="Email"> <br> <br>
                Phone:
                <input type="tel" name="Phone"> <br> <br>
                Address:
                <select name="Address" id="">
                    <option value="KTM" name="Address">KTM</option>
                    <option value="BKT" name="Address">BKT</option>
                </select> <br> <br>
                Gender:
                <input type="radio" name="Gender" value="Male">Male <input type="radio" name="Gender" value="Female">Female <input type="radio" value="Other" name="Gender">Other
            </fieldset>
            <fieldset>
                <legend>Credential</legend>
                Username: <input type="text" name="User"><br> <br>
                Password: <input type="password" name="Password">
            </fieldset>
            <fieldset>
                <legend>Interest</legend>
                Hobby: <input type="checkbox" name="Hobby[]" value="Cricket">Cricket <input type="checkbox" name="Hobby[]" value="Music">Music
                <br> <br>
                Faculty: <input type="radio" name="Faculty" value="Engineering">Engineering <input type="radio" name="Faculty" value="BCA">BCA
            </fieldset>
            <br>
            College: <input type="text" name="College"> <br> <br>
            Picture: <input type="file" name="Picture"> <br> <br>
            Document <input type="file" name="Document"> <br> <br>
            Message: <textarea name="Message" cols="20" rows="8">Enter your message</textarea>
            <br><input type="submit" name="Submit"> <input type="reset" name="Reset"> <button>ok</button>
        </fieldset>
    </form>
    <?php
        if(isset($_POST['Submit']))
        {
            $Firstname=$_POST['Firstname'];
            $Lastname=$_POST['Lastname'];
            $Age=$_POST['Age'];
            $Email=$_POST['Email'];
            $Phone=$_POST['Phone'];
            $Address=$_POST['Address'];
            $Gender=$_POST['Gender'];
            $User=$_POST['User'];
            $Password=$_POST['Password'];
            $Hobbies=implode(",",$_POST['Hobby']);
            $Faculty=$_POST['Faculty'];
            $College=$_POST['College'];
            $Picture=$_FILES['Picture']['tmp_name'];
            $pic= "pic/".$Picture;
            move_uploaded_file($Picture, $pic);
            $Document=$_FILES['Document']['tmp_name'];
            $doc="doc/".$Document;
            move_uploaded_file($Document,$doc);
            $Message=$_POST['Message'];
            
            echo"<br><br>";
        echo "Name: $Firstname $Lastname<br>";
        echo "Age: $Age<br>";
        echo "Email: $Email<br>";
        echo "Phone: $Phone<br>";
        echo "Address: $Address<br>";
        echo "Gender: $Gender<br>";
        echo "Username: $User<br>";
        echo "Hobbies: $Hobbies<br>";
        echo "Faculty: $Faculty<br>";
        echo "College: $College<br>";
        echo "Message: $Message<br>";
        echo "Picture: <img src='$pic' alt='Uploaded Picture'> <br>";
        echo "Document uploaded: <a href='$doc'>Document</a><br>";
        }
    ?>
</body>
</html>
