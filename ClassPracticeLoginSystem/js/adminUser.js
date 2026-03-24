window.addEventListener("load", () => {
    setTimeout(() => {
        document.querySelectorAll("input[type='text'], input[type='email'], input[type='password']").forEach(input => {
            input.value = "";
        });
    }, 100);
});

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
                let currentUserId = CURRENT_USER_ID;
                let currentUserType = CURRENT_USER_TYPE;
                console.log(currentUserType);

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
document.getElementById("addUserForm").addEventListener("submit", function (e) {
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
        body: JSON.stringify({ ids: [id] })
    })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadUsers();
        });
}

// INITIAL LOAD
loadUsers();


