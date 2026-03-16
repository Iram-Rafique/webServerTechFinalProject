<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('location:login.php');
    exit;
}

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
   (Owner cannot delete themselves)
-------------------------- */
if (isset($_GET['delete'])) {
    if ($user_id) {

        // Fetch role
        $check = mysqli_query($conn, "SELECT user_type FROM user_form WHERE id='$user_id'");
        $role = mysqli_fetch_assoc($check)['user_type'];

        if ($role !== 'owner') {
            mysqli_query($conn, "DELETE FROM user_form WHERE id='$user_id'") or die('query failed');
            session_destroy();
            header('location:register.php');
            exit;
        }
    }
}

/* -------------------------
   DELETE SELECTED USERS (Admin + Owner)
   Admin cannot delete owner
-------------------------- */
if (isset($_POST['delete_selected'])) {

    if (isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {

        foreach ($_POST['user_ids'] as $delete_id) {

            // Check role of user being deleted
            $check = mysqli_query($conn, "SELECT user_type FROM user_form WHERE id='$delete_id'");
            $role = mysqli_fetch_assoc($check)['user_type'];

            // Prevent deleting owner
            if ($role === 'owner') {
                continue;
            }

            mysqli_query($conn, "DELETE FROM user_form WHERE id='$delete_id'");
        }

        header('location:profile.php');
        exit;
    }
}

/* -------------------------
   DISPLAY USERS TABLE
-------------------------- */
function displayUsers($conn, $fetch)
{
    $sql = "SELECT * FROM user_form";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        echo "<h3>Table of Users</h3>";
        echo "<form method='post'>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Select</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["user_type"]) . "</td>";
            echo "<td><input type='checkbox' name='user_ids[]' value='" . htmlspecialchars($row["id"]) . "'></td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<input type='submit' name='delete_selected' value='Delete Selected Users' class='btn btn-danger' onclick=\"return confirm('Are you sure?')\">";

        // Only owner can add admin
        if ($fetch['user_type'] === 'owner') {
            echo "<a href='add_admin.php' class='btn btn-primary' style='margin-left:10px;'>Add Admin User</a>";
        }

        echo "</form>";
    } else {
        echo "0 results";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">

    <div class="profile">
        <?php
        $select = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'") or die('query failed');
        $fetch = mysqli_fetch_assoc($select);

        echo $fetch['image'] == ''
            ? '<img src="images/default-avatar.png">'
            : '<img src="uploaded_img/' . $fetch['image'] . '">';
        ?>

        <h3><?php echo $fetch['name']; ?></h3>

        <a href="update_profile.php" class="btn">Update Profile</a>
      <?php if($fetch['user_type'] == 'user'){ ?>
   <a href="shop.php" class="btn">Shop</a>
<?php } ?>

<?php if($fetch['user_type'] == 'admin' || $fetch['user_type'] == 'owner'){ ?>
   <a href="admin_products.php" class="btn">Manage Products</a>
<?php } ?>
        <a href="profile.php?logout=<?php echo $user_id; ?>" class="delete-btn">Logout</a>

        <!-- Hide delete button for owner -->
        <?php if ($fetch['user_type'] !== 'owner') : ?>
            <a href="profile.php?delete=<?php echo $user_id; ?>" class="delete-btn"
               onclick="return confirm('Are you sure you want to delete your account?');">
                Delete Account
            </a>
        <?php endif; ?>

        <p>New <a href="login.php">Login</a> or <a href="register.php">Register</a></p>
    </div>

    <!-- ADMIN PANEL -->
    <?php
    if ($fetch['user_type'] === 'owner' || $fetch['user_type'] === 'admin') {
        displayUsers($conn, $fetch);
    }
    ?>

</div>

</body>
</html>