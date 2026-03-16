<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
    header('location:login.php');
    exit;
}

/* CHECK ROLE */
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_form WHERE id='$user_id'"));

if($user['user_type'] != 'admin' && $user['user_type'] != 'owner'){
    header('location:profile.php');
    exit;
}

$id = $_GET['id'];

/* GET PRODUCT */
$product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM product WHERE id='$id'"));


/* UPDATE PRODUCT */
if(isset($_POST['update_product'])){

    $description = $_POST['description'];
    $price = $_POST['price'];

    /* IMAGE UPDATE */
    if(!empty($_FILES['image']['name'])){

        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp,"products_img/".$image);

        mysqli_query($conn,"UPDATE product
        SET description='$description', price='$price', image='$image'
        WHERE id='$id'");

    }else{

        mysqli_query($conn,"UPDATE product
        SET description='$description', price='$price'
        WHERE id='$id'");
    }

    header("location:admin_products.php");
}
?>

<h2>Edit Product</h2>

<form method="post" enctype="multipart/form-data">

<label>Product Name</label>
<input type="text" name="description" value="<?php echo $product['description']; ?>" required>

<label>Price</label>
<input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>

<label>Image</label>
<input type="file" name="image">

<br><br>

<input type="submit" name="update_product" value="Update Product">

</form>