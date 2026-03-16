<?php
session_start();
include 'config.php';
$page_css = "register.css";
$message = [];

// Load messages from session (PRG pattern)
if(isset($_SESSION['message'])){
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Helper: clear registration session
function clearReg(){
    unset($_SESSION['reg_data'], $_SESSION['otp_sent']);
}

// --------------------
// STEP 1: REGISTER
// --------------------
if(isset($_POST['submit'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);

    if($pass !== $cpass){
        $_SESSION['message'] = ['Confirm password not matched!'];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $check = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){
        $_SESSION['message'] = ['User already exists!'];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];

    if($image_size > 2000000){
        $_SESSION['message'] = ['Image too large!'];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $code = rand(111111,999999);

    $_SESSION['reg_data'] = [
        'name' => $name,
        'email' => $email,
        'password' => password_hash($pass, PASSWORD_DEFAULT),
        'image' => $image,
        'tmp_image' => $image_tmp,
        'code' => $code,
        'created_at' => time(),
        'last_sent' => time()
    ];

    if(mail($email, "Verification Code", "Your OTP is: $code", "From: ladybird840@gmail.com")){
        $_SESSION['otp_sent'] = true;
        $_SESSION['message'] = ["OTP sent to your email."];
    } else {
        clearReg();
        $_SESSION['message'] = ["Failed to send OTP."];
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// --------------------
// STEP 2: VERIFY OTP
// --------------------
if(isset($_POST['check'])){

    if(!isset($_SESSION['reg_data'])){
        $_SESSION['message'] = ["Session expired. Register again."];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $otp = $_POST['OTP'];
    $data = $_SESSION['reg_data'];

    if(time() - $data['created_at'] > 600){
        clearReg();
        $_SESSION['message'] = ["OTP expired. Register again."];
    }
    elseif($otp == $data['code']){

        $image_folder = 'uploaded_img/'.$data['image'];

        $insert = mysqli_query($conn, "INSERT INTO `user_form` 
        (name,email,password,image,code) 
        VALUES 
        ('{$data['name']}','{$data['email']}','{$data['password']}','{$data['image']}','{$data['code']}')");

        if($insert){
            move_uploaded_file($data['tmp_image'], $image_folder);
            clearReg();
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['message'] = ["Database error."];
        }

    } else {
        $_SESSION['message'] = ["Wrong OTP."];
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// --------------------
// RESEND OTP
// --------------------
if(isset($_POST['resend'])){

    if(!isset($_SESSION['reg_data'])){
        $_SESSION['message'] = ["Session expired."];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    $data = $_SESSION['reg_data'];

    if(time() - $data['last_sent'] < 60){
        $_SESSION['message'] = ["Please wait before resending."];
    } else {

        $code = rand(111111,999999);

        $_SESSION['reg_data']['code'] = $code;
        $_SESSION['reg_data']['last_sent'] = time();

        if(mail($data['email'], "New OTP", "Your OTP is: $code", "From: ladybird840@gmail.com")){
            $_SESSION['message'] = ["OTP resent."];
        } else {
            $_SESSION['message'] = ["Failed to resend OTP."];
        }
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// --------------------
// CANCEL / RESET
// --------------------
if(isset($_POST['cancel'])){
    clearReg();
    // $_SESSION['message'] = ["Registration reset."];
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// --------------------
// FORM CONTROL
// --------------------
$showOTP = isset($_SESSION['otp_sent']) && isset($_SESSION['reg_data']);
$showRegister = !$showOTP;
?>


<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<div class="form-container">
<!-- ================= REGISTER FORM ================= -->
<form class="form register-form" method="post" enctype="multipart/form-data" autocomplete="off" style="display: <?= $showRegister ? 'block' : 'none' ?>">
    
    <h3 class="form-title">Step 1: Register</h3>

    <?php if($showRegister && !empty($message)): ?>
        <?php foreach($message as $msg): ?>
            <div class="message"><?= $msg ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <input type="text" name="name" placeholder="Enter username" class="input reg-input" required>
    <input type="email" name="email" placeholder="Enter email" class="input reg-input" required>
    <input type="password" name="password" placeholder="Enter password" class="input reg-input" required>
    <input type="password" name="cpassword" placeholder="Confirm password" class="input reg-input" required>

    <input type="file" name="image" class="input reg-file" accept="image/jpg, image/jpeg, image/png">

    <input type="submit" name="submit" value="Send OTP" class="btn btn-primary">
</form>


<!-- ================= OTP FORM ================= -->
<form class="form otp-form" method="post" autocomplete="off" style="display: <?= $showOTP ? 'block' : 'none' ?>">
    
    <h3 class="form-title">Step 2: Verify Email</h3>

    <?php if($showOTP && !empty($message)): ?>
        <?php foreach($message as $msg): ?>
            <div class="message"><?= $msg ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <input type="text" name="OTP" placeholder="Enter OTP" class="input otp-input">

  <!-- MAIN ACTION -->
<input type="submit" name="check" value="Verify & Register" class="btn btn-primary">

    <!-- TIMER -->
    <p id="timer" class="timer"></p>

   
    <!-- RESEND -->
<input type="submit" name="resend" id="resendBtn" value="Resend OTP" class="btn btn-outline" style="display:none;">

    <!-- BACK BUTTON -->
   <input type="submit" name="cancel" value="Back to Register" class="btn btn-secondary">

    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
</form>
</div>
<script src="js/register.js"></script>
<?php include 'templates/footer.php'; ?>