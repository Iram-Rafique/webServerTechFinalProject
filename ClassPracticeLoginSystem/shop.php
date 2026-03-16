<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
   header('location:login.php');
   exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_form WHERE id='$user_id'"));

if($user['user_type'] != 'user'){
   header('location:profile.php');
   exit;
}


/* ADD TO CART */
if(isset($_POST['add_to_cart'])){

   $product_id = $_POST['product_id'];
   $qty = $_POST['quantity'];

   $product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM product WHERE id='$product_id'"));

   $name = $product['description'];
   $price = $product['price'];

   mysqli_query($conn,"INSERT INTO shopping_cart
   (user_id,product_id,product_name,product_price,product_quantity)
   VALUES('$user_id','$product_id','$name','$price','$qty')");
}


/* DISPLAY PRODUCTS */

$products = mysqli_query($conn,"SELECT * FROM product");

?>
<?php include 'navbar.php'; ?>
<h2>Shop</h2>

<div class="products">

<?php while($row = mysqli_fetch_assoc($products)){ ?>

<div class="product">

<img src="products_img/<?php echo $row['image']; ?>" width="150">

<h3><?php echo $row['description']; ?></h3>

<p>£<?php echo $row['price']; ?></p>

<form method="post">

<input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">

<input type="number" name="quantity" value="1" min="1">

<input type="submit" name="add_to_cart" value="Add to Cart">

</form>

</div>

<?php } ?>

</div>


<h2>Your Cart</h2>

<?php

$cart = mysqli_query($conn,"SELECT * FROM shopping_cart WHERE user_id='$user_id'");

while($row = mysqli_fetch_assoc($cart)){

   echo "
   <p>
   {$row['product_name']} |
   £{$row['product_price']} |
   Qty: {$row['product_quantity']}
   </p>
   ";
}
?>