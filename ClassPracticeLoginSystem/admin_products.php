<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
   header('location:login.php');
   exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_form WHERE id='$user_id'"));

if($user['user_type'] != 'admin' && $user['user_type'] != 'owner'){
   header('location:profile.php');
   exit;
}

$products = mysqli_query($conn,"SELECT * FROM product");
?>

<h2>Product Management</h2>

<a href="add_product.php">Add Product</a>

<table border="1">

<tr>
<th>ID</th>
<th>Image</th>
<th>Description</th>
<th>Price</th>
<th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($products)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>
<img src="products_img/<?php echo $row['image']; ?>" width="60">
</td>

<td><?php echo $row['description']; ?></td>

<td>£<?php echo $row['price']; ?></td>

<td>
<a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a>
<a href="delete_product.php?id=<?php echo $row['id']; ?>" 
onclick="return confirm('Delete this product?')">
Delete
</a>
</td>

</tr>

<?php } ?>

</table>