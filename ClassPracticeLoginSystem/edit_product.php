<?php
include 'config.php';
$page_css = "editAdminProducts.css";

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('location:login.php');
    exit;
}

/* CHECK ROLE */
$user_id = mysqli_real_escape_string($conn, $user_id);
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'"));

if ($user['user_type'] != 'admin' && $user['user_type'] != 'owner') {
    header('location:profile.php');
    exit;
}

/* GET PRODUCT */
$id = mysqli_real_escape_string($conn, $_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE id='$id'"));

/* UPDATE PRODUCT */
if (isset($_POST['update_product'])) {

    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];

    /* IMAGE SETTINGS */
    $max_size = 2 * 1024 * 1024; // 2MB
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!empty($_FILES['image']['name'])) {

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $size = $_FILES['image']['size'];

        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            echo "<p class='error-msg'>Only JPG, JPEG, PNG allowed.</p>";
        } elseif ($size > $max_size) {
            echo "<p class='error-msg'>Image too large (max 2MB).</p>";
        } else {

            $new_name = time() . '_' . $image;

            move_uploaded_file($tmp, "products_img/" . $new_name);

            mysqli_query($conn, "UPDATE product
            SET description='$description', price='$price', image='$new_name'
            WHERE id='$id'");

            header("location:admin_products.php");
            exit;
        }
    } else {

        mysqli_query($conn, "UPDATE product
        SET description='$description', price='$price'
        WHERE id='$id'");

        header("location:admin_products.php");
        exit;
    }
}
?>
<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<div class="edit-product-wrapper">
    <h2 class="form-title">Edit Product</h2>

    <form method="post" enctype="multipart/form-data" class="product-form">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="description"
                value="<?php echo $product['description']; ?>" required>
        </div>

        <div class="form-group">
            <label>Price (£)</label>
            <input type="number" step="0.01" name="price"
                value="<?php echo $product['price']; ?>" required>
        </div>

        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit" name="update_product" class="submit-btn">
                Update Product
            </button>

            <!--  FIXED cancel -->
            <a href="admin_products.php" class="cancel-btn">
                Cancel
            </a>
        </div>

    </form>
</div>
<?php include 'templates/footer.php'; ?>