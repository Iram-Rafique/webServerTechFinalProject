<?php
include 'config.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('location:login.php');
    exit;
}

/* -------------------------
   GET CURRENT USER
-------------------------- */
$select = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'");
$fetch = mysqli_fetch_assoc($select);

/* -------------------------
   BLOCK NORMAL USERS
-------------------------- */
if ($fetch['user_type'] !== 'admin' && $fetch['user_type'] !== 'owner') {
    header('location:profile.php');
    exit;
}

/* -------------------------
   DELETE SELECTED USERS
-------------------------- */
if (isset($_POST['delete_selected'])) {

    if (isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {

        foreach ($_POST['user_ids'] as $delete_id) {

            // Get role of selected user
            $check = mysqli_query($conn, "SELECT user_type FROM user_form WHERE id='$delete_id'");
            $role = mysqli_fetch_assoc($check)['user_type'];

            // ❌ Prevent deleting owner
            if ($role === 'owner') {
                continue;
            }

            mysqli_query($conn, "DELETE FROM user_form WHERE id='$delete_id'");
        }

        header('location:admin_users.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

    <h2>Manage Users</h2>

    <form method="post">

        <div class="table-responsive">

            <table class="table table-bordered">

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Select</th>
                </tr>

                <?php
                $result = mysqli_query($conn, "SELECT * FROM user_form");

                if ($result->num_rows > 0) {

                    while ($row = mysqli_fetch_assoc($result)) {
                ?>

                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td>
                        <input type="checkbox" name="user_ids[]" value="<?php echo $row['id']; ?>">
                    </td>
                </tr>

                <?php
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                ?>

            </table>

        </div>

        <input type="submit" name="delete_selected" value="Delete Selected Users"
               class="btn btn-danger"
               onclick="return confirm('Are you sure?')">

        <!-- Only owner can add admin -->
        <?php if ($fetch['user_type'] === 'owner') { ?>
            <a href="add_admin.php" class="btn btn-primary" style="margin-left:10px;">
                Add Admin User
            </a>
        <?php } ?>

    </form>

</div>

</body>
</html>