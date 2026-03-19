<?php
include 'config.php';
$page_css =  "adminUser.css";
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('location:login.php');
    exit;
}

// Get current user
$result = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'");
$current = mysqli_fetch_assoc($result);

// Block normal users
if ($current['user_type'] !== 'admin' && $current['user_type'] !== 'owner') {
    header('location:profile.php');
    exit;
}
?>
<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<h2>Manage Users</h2>

<!-- ADD USER FORM -->
<form id="addUserForm">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="user_type">
        <option value="user">User</option>

        <!--  ONLY OWNER CAN SEE ADMIN OPTION -->
        <?php if ($current['user_type'] === 'owner') { ?>
            <option value="admin">Admin</option>
        <?php } ?>
    </select>

    <button type="submit">Add User</button>
</form>

<p id="message"></p>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Type</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody id="userTable"></tbody>
</table>

<script>
    const CURRENT_USER_ID = "<?php echo $user_id; ?>";
    const CURRENT_USER_TYPE = "<?php echo $current['user_type']; ?>";
</script>
<script src="js/adminUser.js"></script>
<?php include 'templates/footer.php'; ?>