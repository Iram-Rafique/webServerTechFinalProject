<?php

include 'config.php';
$page_css = "updateProfile.css";
$user_id = $_SESSION['user_id'];

if(isset($_POST['update_profile'])){

   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

   mysqli_query($conn, "UPDATE `user_form` 
      SET name = '$update_name', email = '$update_email' 
      WHERE id = '$user_id'") or die('query failed');

   // 🔥 GET CURRENT USER DATA
   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
   $fetch = mysqli_fetch_assoc($select);

   // 🔐 PASSWORD INPUTS (NO HASH YET)
   $current_pass = $_POST['update_pass'];
   $new_pass = $_POST['new_pass'];
   $confirm_pass = $_POST['confirm_pass'];

   // 🔥 CHECK IF USER WANTS TO CHANGE PASSWORD
   if(!empty($current_pass) || !empty($new_pass) || !empty($confirm_pass)){

      if(!password_verify($current_pass, $fetch['password'])){
         $message[] = 'Old password not matched!';
      }elseif($new_pass !== $confirm_pass){
         $message[] = 'Confirm password not matched!';
      }else{
         $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

         mysqli_query($conn, "UPDATE `user_form` 
            SET password = '$hashed_password' 
            WHERE id = '$user_id'") or die('query failed');

         $message[] = 'Password updated successfully!';
      }
   }

   // ================= IMAGE =================
   $update_image = $_FILES['update_image']['name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_folder = 'uploaded_img/'.$update_image;

   if(!empty($update_image)){
      if($update_image_size > 2000000){
         $message[] = 'Image is too large!';
      }else{
         mysqli_query($conn, "UPDATE `user_form` 
            SET image = '$update_image' 
            WHERE id = '$user_id'") or die('query failed');

         move_uploaded_file($update_image_tmp_name, $update_image_folder);

         $message[] = 'Image updated successfully!';
      }
   }

}
?>


   <?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<div class="update-page">
   <div class="update-card">

   <?php
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');

      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      } else {
         echo "User not found";
         exit;
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">

      <div class="profile-image">
         <?php
            if($fetch['image'] == ''){
               echo '<img src="images/default-avatar.png">';
            }else{
               echo '<img src="uploaded_img/'.$fetch['image'].'">';
            }
         ?>
      </div>

      <?php
      if(isset($message)){
         foreach($message as $msg){
            echo '<div class="message">'.$msg.'</div>';
         }
      }
      ?>

      <div class="form-section">
         <h3>Account Info</h3>

         <label>Username</label>
         <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">

         <label>Email</label>
         <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">

         <label>Profile Image</label>
         <input type="file" name="update_image" class="box">
      </div>

      <div class="form-section">
         <h3>Change Password</h3>

         <label>Current Password</label>
         <input type="password" name="update_pass" class="box">

         <label>New Password</label>
         <input type="password" name="new_pass" class="box">

         <label>Confirm Password</label>
         <input type="password" name="confirm_pass" class="box">
      </div>

      <input type="submit" value="Update Profile" name="update_profile" class="btn">
      <a href="profile.php" class="back-btn">Go back</a>

   </form>

   </div>
</div>
