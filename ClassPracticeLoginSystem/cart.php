<?php
include 'config.php';
$page_css = "cart.css";

$user_id = $_SESSION['user_id'];

/* UPDATE QUANTITY */
if(isset($_POST['update_qty'])){
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['quantity'];

   if($qty <= 0){
      mysqli_query($conn,"DELETE FROM shopping_cart WHERE id='$cart_id'");
   } else {
      mysqli_query($conn,"UPDATE shopping_cart SET product_quantity='$qty' WHERE id='$cart_id'");
   }
}

/* REMOVE ITEM */
if(isset($_POST['remove_item'])){
   $cart_id = $_POST['cart_id'];
   mysqli_query($conn,"DELETE FROM shopping_cart WHERE id='$cart_id'");
}
?>
<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<h2>Your Cart</h2>

<div class="cart-container">

<?php 
$total = 0;
$cart = mysqli_query($conn,"SELECT * FROM shopping_cart WHERE user_id='$user_id'");

while($row = mysqli_fetch_assoc($cart)){ 
   $subtotal = $row['product_price'] * $row['product_quantity'];
   $total += $subtotal;
?>

<div class="cart-item">

   <div>
      <h3>Name: <?php echo $row['product_name']; ?></h3>
      <p>Price: £<?php echo $row['product_price']; ?></p>
   </div>

   <!-- Quantity Controls -->
   <form method="post" class="qty-form">
      <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">

      <button name="update_qty" value="1" onclick="this.form.quantity.stepDown()">−</button>

      <input type="number" name="quantity" value="<?php echo $row['product_quantity']; ?>" min="1">

      <button name="update_qty" value="1" onclick="this.form.quantity.stepUp()">+</button>
   </form>
<div class="cart-actions">
   <p><strong>subtotal: £<?php echo $subtotal; ?></strong></p>
<!-- remove -->
   <form method="post">
      <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
      <button name="remove_item" class="remove-btn">Remove</button>
   </form>
</div>

</div>

<?php } ?>

</div>

<!-- TOTAL -->
<h3 class="cart-total">Total: £<?php echo $total; ?></h3>
<?php include 'templates/footer.php'; ?>