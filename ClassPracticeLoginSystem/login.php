<?php

//-------------------------------------------------
// LOAD CONFIGURATION
//-------------------------------------------------

include 'config.php';

$message = [];

// =====================
// CSS
// =============
$page_css = "login.css";
//-------------------------------------------------
//  NORMAL LOGIN (EMAIL + PASSWORD)
//-------------------------------------------------

if(isset($_POST['submit']))
{
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass  = mysqli_real_escape_string($conn, $_POST['password']);

   $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email='$email'") 
   or die('query failed');

   if(mysqli_num_rows($select) > 0){

      $row = mysqli_fetch_assoc($select);

      if(password_verify($pass, $row['password'])){

         $_SESSION['user_id'] = $row['id'];

         header('location:profile.php');
         exit();

      }else{

         $message[] = 'Incorrect password!';

      }

   }else{

      $message[] = 'Incorrect email or password!';

   }
}


//-------------------------------------------------
//  GOOGLE LOGIN URL
//-------------------------------------------------

$google_login_url = $google_client->createAuthUrl();

//-------------------------------------------------
//  FACEBOOK LOGIN URL
//-------------------------------------------------

$facebook_login_url = htmlspecialchars($facebook_login_url);

?>
<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<div class="login-container">

    <form class="login-form" method="post" autocomplete="off">

        <h2 class="login-title">Welcome Back</h2>

        <?php if(!empty($message)): ?>
            <?php foreach($message as $msg): ?>
                <div class="login-message"><?= $msg ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Enter email" class="login-input" required>
        <input type="password" name="password" placeholder="Enter password" class="login-input" required>

        <input type="submit" name="submit" value="Login" class="login-btn">

        <p class="login-text">
            Don't have an account? <a href="register.php">Register</a>
        </p>

    </form>

    <div class="social-login">
        <p>Or continue with</p>

        <a href="<?= $google_login_url ?>" class="social-btn google">Google</a>
        <a href="<?= $facebook_login_url ?>" class="social-btn facebook">Facebook</a>
    </div>

</div>
<script src="js/register.js"></script>
<?php include 'templates/footer.php'; ?>