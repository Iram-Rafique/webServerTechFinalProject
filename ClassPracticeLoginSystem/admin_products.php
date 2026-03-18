<?php
include 'config.php';
$page_css = "adminProducts.css";


$user_id = $_SESSION['user_id'] ?? null;

// 🔒 Redirect if not logged in
if (!$user_id) {
   header('location:login.php');
   exit;
}

// 🔒 Prevent SQL injection
$user_id = mysqli_real_escape_string($conn, $user_id);

// 🔍 Get user safely
$result = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);

// 🔒 Check role
if ($user['user_type'] != 'admin' && $user['user_type'] != 'owner') {
   header('location:profile.php');
   exit;
}

// 📦 Get products
$products = mysqli_query($conn, "SELECT * FROM product");
?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<h2>Product Management</h2>

<a href="add_product.php" class="add-product-btn">Add Product</a>

<?php if (mysqli_num_rows($products) == 0) { ?>
   <!-- 🧾 Empty state -->
   <p>No products found.</p>
<?php } else { ?>

<table>
   <tr>
      <th>ID</th>
      <th>Image</th>
      <th>Description</th>
      <th>Price</th>
      <th>Actions</th>
   </tr>

   <?php while ($row = mysqli_fetch_assoc($products)) { ?>

      <tr>

         <td><?php echo $row['id']; ?></td>

         <td>
            <img src="products_img/<?php echo $row['image']; ?>" width="60">
         </td>

         <td><?php echo $row['description']; ?></td>

         <td>£<?php echo $row['price']; ?></td>

         <td>
            <!-- ✏️ Edit (added confirmation optional) -->
            <a href="edit_product.php?id=<?php echo $row['id']; ?>"
               class="action-btn edit-btn"
               onclick="return confirm('Edit this product?')">
               Edit
            </a>

            <!-- 🗑 Delete -->
            <a href="delete_product.php?id=<?php echo $row['id']; ?>"
               class="action-btn delete-btn"
               onclick="return confirm('Delete this product?')">
               Delete
            </a>
         </td>

      </tr>

   <?php } ?>

</table>

<?php } ?>

<?php include 'templates/footer.php'; ?>