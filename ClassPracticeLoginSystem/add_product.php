<?php
include 'config.php';
$page_css = "editAdminProducts.css";
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
   header('location:login.php');
   exit;
}

/* CHECK USER ROLE */
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'"));

if ($user['user_type'] != 'admin' && $user['user_type'] != 'owner') {
   header('location:profile.php');
   exit;
}

/* ADD PRODUCT */
if (isset($_POST['add_product'])) {

   $description = mysqli_real_escape_string($conn, $_POST['description']);
   $price = $_POST['price'];

   $image = $_FILES['image']['name'];
   $tmp_name = $_FILES['image']['tmp_name'];
   $image_size = $_FILES['image']['size'];

   $max_size = 2 * 1024 * 1024; // ✅ 2MB

   // Get file extension
   $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
   $allowed_ext = ['jpg', 'jpeg', 'png'];

   if (empty($image)) {
      echo "Please upload an image.";
   } elseif (!in_array($image_ext, $allowed_ext)) {
      echo "Only JPG, JPEG, PNG files are allowed.";
   } elseif ($image_size > $max_size) {
      echo "Image is too large. Max size is 2MB.";
   } else {

      // Optional: make unique filename (prevents overwrite)
      $new_image_name = time() . '_' . $image;

      move_uploaded_file($tmp_name, "products_img/" . $new_image_name);

      mysqli_query($conn, "INSERT INTO product (description,image,price)
      VALUES ('$description','$new_image_name','$price')") or die('query failed');

      header('location:admin_products.php');
      exit;
   }
}
?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>



<div class="edit-product-wrapper">
    <h2 class="form-title">Add New Product</h2>

    <form method="post" enctype="multipart/form-data" class="product-form">

        <div class="form-group">
            <label>Product Name / Description</label>
            <input type="text" name="description" placeholder="Enter product name" required>
        </div>

        <div class="form-group">
            <label>Price (£)</label>
            <input type="number" step="0.01" name="price" placeholder="0.00" required>
        </div>

        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" required>
        </div>

        <div class="form-actions">
            <button type="submit" name="add_product" class="submit-btn">
                Add Product
            </button>

            <a href="admin_products.php" class="cancel-btn">
                Cancel
            </a>
        </div>

    </form>
</div>
<?php include 'templates/footer.php'; ?>