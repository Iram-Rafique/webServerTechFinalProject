<?php

//-------------------------------------------------
// 1. LOAD CONFIGURATION
//-------------------------------------------------

include 'config.php';

$message = [];


//-------------------------------------------------
// 2. NORMAL LOGIN (EMAIL + PASSWORD)
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
// 3. GOOGLE LOGIN URL
//-------------------------------------------------

$google_login_url = $google_client->createAuthUrl();

//-------------------------------------------------
// 4. FACEBOOK LOGIN URL
//-------------------------------------------------

$facebook_login_url = htmlspecialchars($facebook_login_url);

?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="form-container">

<!-- EMAIL LOGIN -->
<form method="post">

<h3>Login Now</h3>

<?php
if(!empty($message)){
   foreach($message as $msg){
      echo '<div class="message">'.$msg.'</div>';
   }
}
?>

<input type="email" name="email" placeholder="Enter email" class="box" required>
<input type="text" name="password" placeholder="Enter password" class="box" required>

<input type="submit" name="submit" value="Login Now" class="btn">

<p>Don't have an account? <a href="register.php">Register now</a></p>

</form>


<!-- GOOGLE LOGIN -->
<h3>Login with Google</h3>

<a href="<?= $google_login_url ?>" class="btn">
Login with Google
</a>
<!-- FACEBOOK LOGIN -->
<h3>Login with Facebook</h3>

<a href="<?= $facebook_login_url ?>" class="btn">
Login with Facebook
</a>
</div>

</body>
</html>