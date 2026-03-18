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

//  LOAD USERS
function loadUsers() {
    fetch("fetch_users.php")
        .then(res => res.json())
        .then(users => {

            let table = document.getElementById("userTable");
            table.innerHTML = "";

            users.forEach(user => {

                let disableDelete = "";

// current logged in user id
let currentUserId = "<?php echo $user_id; ?>";
let currentUserType = "<?php echo $current['user_type']; ?>";

// Admin rules
if (currentUserType === "admin") {
    if (user.user_type === "admin" || user.user_type === "owner") {
        disableDelete = "disabled";
    }
}

// Owner cannot delete themselves
if (currentUserType === "owner") {
    if (user.id == currentUserId) {
        disableDelete = "disabled";
    }
}
       

                table.innerHTML += `
                    <tr id="row-${user.id}">
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.user_type}</td>
                        <td>
                            <button  class="delete-btn" onclick="deleteUser(${user.id})" ${disableDelete}>
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.log("ERROR:", err);
        });
}

//  ADD USER
document.getElementById("addUserForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    fetch("add_user_ajax.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        document.getElementById("message").innerText = data.message;

        if (data.status === "success") {
            loadUsers();
            this.reset();
        }
    });
});

// DELETE SINGLE USER
function deleteUser(id) {

    if (!confirm("Are you sure you want to delete this user?")) return;

    fetch("delete_users_ajax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ ids: [id] }) // keep backend same
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        loadUsers();
    });
}

// INITIAL LOAD
loadUsers();

</script>

<?php include 'templates/footer.php'; ?>