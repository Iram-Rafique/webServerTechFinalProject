<?php
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('location:login.php');
    exit;
}
$page_css = "profile.css";
/* -------------------------
   LOGOUT
-------------------------- */
if (isset($_GET['logout'])) {
    session_destroy();
    header('location:login.php');
    exit;
}

/* -------------------------
   DELETE OWN ACCOUNT
-------------------------- */
if (isset($_GET['delete'])) {

    $check = mysqli_query($conn, "SELECT user_type FROM user_form WHERE id='$user_id'");
    $role = mysqli_fetch_assoc($check)['user_type'];

    //  owner cannot delete themselves
    if ($role !== 'owner') {
        mysqli_query($conn, "DELETE FROM user_form WHERE id='$user_id'") or die('query failed');
        session_destroy();
        header('location:register.php');
        exit;
    }
}

/* -------------------------
   FETCH USER DATA
-------------------------- */
$select = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'") or die('query failed');
$fetch = mysqli_fetch_assoc($select);
?>

<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>

<div class="profile-page">

    <div class="profile-card">

        <?php
        echo $fetch['image'] == ''
            ? '<img src="images/default-avatar.png" class="avatar">'
            : '<img src="uploaded_img/' . $fetch['image'] . '" class="avatar">';
        ?>

        <h2><?php echo htmlspecialchars($fetch['name']); ?></h2>

        <p class="role"><?php echo $fetch['user_type']; ?></p>

        <div class="profile-actions">
            <a href="update_profile.php" class="btn primary">Edit Profile</a>

            <?php if($fetch['user_type'] == 'user'){ ?>
                <a href="shop.php" class="btn secondary">Shop</a>
            <?php } ?>

            <?php if($fetch['user_type'] == 'admin' || $fetch['user_type'] == 'owner'){ ?>
                <a href="admin_users.php" class="btn secondary">Manage Users</a>
                <a href="admin_products.php" class="btn secondary">Manage Products</a>
            <?php } ?>

            <a href="profile.php?logout=<?php echo $user_id; ?>" class="btn danger">Logout</a>

            <?php if ($fetch['user_type'] !== 'owner') : ?>
                <a href="profile.php?delete=<?php echo $user_id; ?>" 
                   class="btn danger outline"
                   onclick="return confirm('Are you sure you want to delete your account?');">
                    Delete Account
                </a>
            <?php endif; ?>
        </div>

    </div>

</div>
<?php include 'templates/footer.php'; ?>