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
    <a href="cart.php">Cart</a>
    <a class="logout" href="profile.php?logout=<?php echo $user_id; ?>">Logout</a>

<?php } ?>

<?php if($user_type == 'admin' || $user_type == 'owner'){ ?>

    <a href="profile.php">Profile</a>
    <a href="admin_products.php">Products</a>

    <?php if($user_type == 'owner'){ ?>
        <a href="profile.php">Users</a>
    <?php } ?>

    <a class="logout" href="profile.php?logout=<?php echo $user_id; ?>">Logout</a>

<?php } ?>

</div>

</nav>