<?php
include 'config.php';

if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    // Use password_hash for secure password hashing
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;
    $code = rand(999999, 111111);

    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

    if(mysqli_num_rows($select) > 0) {
        $message[] = 'user already exists';
    } else {
        if($pass != $cpass) {
            $message[] = 'confirm password not matched!';
        } elseif($image_size > 2000000) {
            $message[] = 'image size is too large!';
        } else {
            $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image, code) 
            VALUES ('$name', '$email', '$hashed_pass', '$image', '$code')") or die('query failed');
            
            if($insert) {
                move_uploaded_file($image_tmp_name, $image_folder);
                // handel email verification
                $subject = "Email Verification Code";
                $message1 = "Your verification code is $code";
                $sender = "From: ghadamkhair2023@gmail.com";
                
                if(mail($email, $subject, $message1, $sender)) {
                    $message[] = 'Now please check your email, enter the OTP to verify and complete your registration!';
                } else {
                    $message[] = 'Failed while sending code!';
                }
            } else {
                $message[] = 'registration failed!';
            }
        }
    }
}

// If user clicks verification code submit button
if(isset($_POST['check'])) {
    $OTP = mysqli_real_escape_string($conn, $_POST['OTP']);
    
    $check_code = "SELECT * FROM `user_form` WHERE code = '$OTP'";
    $code_res = mysqli_query($conn, $check_code);
    
    if(mysqli_num_rows($code_res) > 0) {
        $message[] = 'OTP is correct!';
        header('location: login.php');
    } else {
        $message[] = "wrong OTP - $OTP, try again!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
 <!-- custom css file link  -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Step 1: Register now</h3>
        <?php
        if(isset($message)) {
            foreach($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="name" placeholder="Enter username" class="box" required>
        <input type="email" name="email" placeholder="Enter email" class="box" required>
        <input type="password" name="password" placeholder="Enter password" class="box" required>
        <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
        <input type="submit" name="submit" value="Submit now to receive email" class="btn">
    </form>

    <form action="" method="post" enctype="multipart/form-data">
        <h3>Step 2: Enter OTP to verify your email</h3>
        <?php
        if(isset($message)) {
            foreach($message as $msg) {
                echo '<div class="message">'.$msg.'</div>';
            }
        }
        ?>
        <input type="text" name="OTP" placeholder="Enter OTP" class="box" required>
        <input type="submit" name="check" value="Register now" class="btn">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form>
</div>
</body>
</html> 