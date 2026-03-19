<?php
include 'config.php';


$user_id = $_SESSION['user_id'] ?? null;
$user_type = null;

if($user_id){
    $result = mysqli_query($conn,"SELECT user_type FROM user_form WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
    $user_type = $user['user_type'];
}
?>

<nav class="navbar">

<div class="nav-left">
    <a class="logo" href="index.php">MyShop</a>
</div>

<div class="nav-right">

<?php if(!$user_id){ ?>

    <a href="login.php">Login</a>
    <a class="btn-nav" href="register.php">Register</a>

<?php } ?>

<?php if($user_type == 'user'){ ?>

    <a href="profile.php">Profile</a>
    <a href="shop.php">Shop</a>
  <?php
$cart_count = 0;

if(isset($_SESSION['user_id'])){
   $uid = $_SESSION['user_id'];

   $result = mysqli_query($conn,"SELECT SUM(product_quantity) as total 
   FROM shopping_cart WHERE user_id='$uid'");

   $data = mysqli_fetch_assoc($result);
   $cart_count = $data['total'] ?? 0;
}
?>

<a href="cart.php" class="cart-icon">
   🛒 (<?php echo $cart_count; ?>)
</a>
    <a class="logout" href="profile.php?logout=<?php echo $user_id; ?>">Logout</a>

<?php } ?>

<?php if($user_type == 'admin' || $user_type == 'owner'){ ?>

    <a href="profile.php">Profile</a>
    <a href="admin_products.php">Products</a>
<a href="admin_users.php">Users</a>

    <a class="logout" href="profile.php?logout=<?php echo $user_id; ?>">Logout</a>

<?php } ?>

</div>

</nav>