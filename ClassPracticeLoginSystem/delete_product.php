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

/* DELETE PRODUCT */
if(isset($_GET['id'])){

    $id = $_GET['id'];

    mysqli_query($conn,"DELETE FROM product WHERE id='$id'") or die('Delete failed');

}

header("location:admin_products.php");
exit;