<?php
include 'config.php';
// session_start();

if(isset($_POST['submit'])){
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, $_POST['password']);
   
   // Select the user from the database based on the email
   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      
      // Use password_verify to check the entered password against the hashed password
      if(password_verify($pass, $row['password'])){
         $_SESSION['user_id'] = $row['id'];  // Set session for user
         header('location:profile.php');     // Redirect to profile page
      } else {
         $message[] = 'Incorrect password!';  // Incorrect password
      }
   } else {
      $message[] = 'Incorrect email or password!';  // Incorrect email
   }

   
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
   
<div class="form-container">
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Login Now</h3>
      
      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>
      
      <input type="email" name="email" placeholder="Enter email" class="box" required>
      <input type="password" name="password" placeholder="Enter password" class="box" required>
      <input type="submit" name="submit" value="Login Now" class="btn">
      <p>Don't have an account? <a href="register.php">Register now</a></p>
   </form>



<!-- add for google////// -->
   <form action="" method="post" enctype="multipart/form-data">
    <h3>Way 2: login with Google Account</h3>

    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<div class="message">' . $msg . '</div>';
        }
    }
    ?>

    <button type="submit" name="google_login" class="btn">
        Login with Google
    </button>
</form>
</div>

</body>
</html>
