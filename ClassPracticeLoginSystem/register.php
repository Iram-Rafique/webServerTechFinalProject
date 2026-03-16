<?php
session_start();
include 'config.php';

$message = []; // Initialize messages

// --------------------
// Step 1: Registration
// --------------------
if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    if($pass != $cpass){
        $message[] = 'Confirm password not matched!';
    } else {
        $check = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email='$email'") or die('Query failed');
        if(mysqli_num_rows($check) > 0){
            $message[] = 'User already exists!';
        } else {
            $image = $_FILES['image']['name'];
            $image_size = $_FILES['image']['size'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $code = rand(111111, 999999); // OTP

            if($image_size > 2000000){
                $message[] = 'Image size is too large!';
            } else {
                // Store data temporarily in session
                $_SESSION['reg_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash($pass, PASSWORD_DEFAULT),
                    'image' => $image,
                    'tmp_image' => $image_tmp,
                    'code' => $code,
                    'time' => time()
                ];


                // Send OTP email (using your tested Gmail setup)
                $to = $email;
                $subject = "Email Verification Code";
                $body = "Your verification code is $code";
                $headers = "From: ladybird840@gmail.com";

                if(mail($to, $subject, $body, $headers)){
                    $_SESSION['otp_sent'] = true; // show OTP form
                    $message[] = 'OTP sent! Enter it to complete registration.';
                } else {
                    $message[] = 'Failed while sending code!';
                }
            }
        }
    }
}




// --------------------
// Resend OTP
// --------------------
if(isset($_POST['resend'])){

    if(isset($_SESSION['reg_data'])){

        // check 60 seconds
        if(time() - $_SESSION['reg_data']['time'] < 60){
            $message[] = "Please wait 1 minute before requesting a new OTP.";
        }else{

            $code = rand(111111,999999);

            $_SESSION['reg_data']['code'] = $code;
            $_SESSION['reg_data']['time'] = time();

            $email = $_SESSION['reg_data']['email'];

            $to = $email;
            $subject = "Resend Verification Code";
            $body = "Your new verification code is $code";
            $headers = "From: ladybird840@gmail.com";

            if(mail($to,$subject,$body,$headers)){
                $message[] = "New OTP sent to your email.";
            }else{
                $message[] = "Failed to resend OTP.";
            }

        }

    }

}

// --------------------
// Step 2: OTP Verification
// --------------------
if(isset($_POST['check'])){
    $otp = mysqli_real_escape_string($conn, $_POST['OTP']);

    if(isset($_SESSION['reg_data'])){
        // Optional: OTP expires after 10 minutes
        if(time() - $_SESSION['reg_data']['time'] > 600){
            unset($_SESSION['reg_data'], $_SESSION['otp_sent']);
            $message[] = 'OTP expired. Please register again.';
        } elseif($otp == $_SESSION['reg_data']['code']){
            $data = $_SESSION['reg_data'];
            $image_folder = 'uploaded_img/'.$data['image'];

            // Insert into DB
            $insert = mysqli_query($conn, "INSERT INTO `user_form` (name,email,password,image,code) 
                VALUES ('{$data['name']}','{$data['email']}','{$data['password']}','{$data['image']}','{$data['code']}')") or die('Query failed');

            if($insert){
                move_uploaded_file($data['tmp_image'], $image_folder);
                unset($_SESSION['reg_data'], $_SESSION['otp_sent']);
                $message[] = 'Registration successful! You can now log in.';
                header('location: login.php');
                exit();
            } else {
                $message[] = 'Registration failed!';
            }
        } else {
            $message[] = 'Wrong OTP, try again!';
        }
    } else {
        $message[] = 'No registration data found. Please register first.';
    }
}

// --------------------
// Form visibility
// --------------------
$showRegister = !isset($_SESSION['otp_sent']);
$showOTP = isset($_SESSION['otp_sent']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
<div class="form-container">

    <!-- Registration Form -->
    <form action="" method="post" enctype="multipart/form-data" style="display: <?= $showRegister ? 'block' : 'none' ?>">
        <h3>Step 1: Register now</h3>
        <?php if($showRegister && !empty($message)){
            foreach($message as $msg){
                echo '<div class="message">'.$msg.'</div>';
            }
        } ?>
        <input type="text" name="name" placeholder="Enter username" class="box" required>
        <input type="email" name="email" placeholder="Enter email" class="box" required>
        <input type="password" name="password" placeholder="Enter password" class="box" required>
        <input type="password" name="cpassword" placeholder="Confirm password" class="box" required>
        <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
        <input type="submit" name="submit" value="Submit now to receive email" class="btn">
    </form>

    <!-- OTP Form -->
    <!-- <form action="" method="post" style="display: <?= $showOTP ? 'block' : 'none' ?>">
        <h3>Step 2: Enter OTP to verify your email</h3>
        <?php if($showOTP && !empty($message)){
            foreach($message as $msg){
                echo '<div class="message">'.$msg.'</div>';
            }
        } ?>
        <input type="text" name="OTP" placeholder="Enter OTP" class="box" required>
        <input type="submit" name="check" value="Register now" class="btn">
        <input type="submit" name="resend" value="Resend OTP" class="btn">
        <p>Already have an account? <a href="login.php">Login now</a></p>
    </form> -->
    <!-- OTP Form -->
<form action="" method="post" style="display: <?= $showOTP ? 'block' : 'none' ?>">
    <h3>Step 2: Enter OTP to verify your email</h3>

    <?php if($showOTP && !empty($message)){
        foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
        }
    } ?>

    <input type="text" name="OTP" placeholder="Enter OTP" class="box">

    <input type="submit" name="check" value="Register now" class="btn">

    <!-- Timer -->
    <p id="timer" style="color:red;font-weight:bold;"></p>

    <!-- Resend button hidden first -->
<input type="submit" name="resend" id="resendBtn" value="Resend OTP" class="btn" style="display:none;">

    <p>Already have an account? <a href="login.php">Login now</a></p>
</form>

</div>


<script>

let countdown = 60; // 1 minute
let timerDisplay = document.getElementById("timer");
let resendBtn = document.getElementById("resendBtn");

if(timerDisplay){

    resendBtn.style.display = "none";

    let timer = setInterval(function(){

        let minutes = Math.floor(countdown / 60);
        let seconds = countdown % 60;

        timerDisplay.innerHTML = "Resend OTP in " + minutes + ":" + (seconds < 10 ? "0" + seconds : seconds);

        countdown--;

        if(countdown < 0){
            clearInterval(timer);
            timerDisplay.innerHTML = "OTP expired. You can resend now.";
            resendBtn.style.display = "block";
        }

    },1000);
}

</script>
</body>
</html>
