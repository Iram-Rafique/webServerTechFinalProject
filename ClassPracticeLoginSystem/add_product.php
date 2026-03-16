<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
   header('location:login.php');
   exit;
}

/* CHECK USER ROLE */
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_form WHERE id='$user_id'"));

if($user['user_type'] != 'admin' && $user['user_type'] != 'owner'){
   header('location:profile.php');
   exit;
}


/* ADD PRODUCT */
if(isset($_POST['add_product'])){

   $description = mysqli_real_escape_string($conn,$_POST['description']);
   $price = $_POST['price'];

   $image = $_FILES['image']['name'];
   $tmp_name = $_FILES['image']['tmp_name'];

   if(!empty($image)){

      move_uploaded_file($tmp_name,"products_img/".$image);

      mysqli_query($conn,"INSERT INTO product (description,image,price)
      VALUES ('$description','$image','$price')") or die('query failed');

      header('location:admin_products.php');
      exit;

   }else{

      echo "Please upload an image.";

   }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<h2>Add New Product</h2>

<form method="post" enctype="multipart/form-data">

<label>Product Name / Description</label><br>
<input type="text" name="description" required><br><br>

<label>Price</label><br>
<input type="number" step="0.01" name="price" required><br><br>

<label>Product Image</label><br>
<input type="file" name="image" required><br><br>

<input type="submit" name="add_product" value="Add Product">

</form>

<br>

<a href="admin_products.php">Back to Products</a>

</body>
</html>