<?php
include 'config.php';


header('Content-Type: application/json');

// -------------------------
// CHECK LOGIN
// -------------------------
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

// -------------------------
// GET CURRENT USER
// -------------------------
$result = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'");
$current = mysqli_fetch_assoc($result);

if (!$current) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

// -------------------------
// PERMISSION CHECK
// -------------------------
if ($current['user_type'] !== 'admin' && $current['user_type'] !== 'owner') {
    echo json_encode(["status" => "error", "message" => "No permission"]);
    exit;
}

// -------------------------
// GET INPUT (JSON)
// -------------------------
$data = json_decode(file_get_contents("php://input"), true);
$ids = $data['ids'] ?? [];

if (empty($ids) || !is_array($ids)) {
    echo json_encode(["status" => "error", "message" => "No users selected"]);
    exit;
}

// -------------------------
// DELETE LOGIC
// -------------------------
$deleted = 0;

foreach ($ids as $id) {

    $id = intval($id);

    // Get target user role
    $check = mysqli_query($conn, "SELECT user_type FROM user_form WHERE id='$id'");
    $target = mysqli_fetch_assoc($check);

    if (!$target) continue;

    $target_role = $target['user_type'];

    //  NEVER delete owner
    if ($target_role === 'owner') {
        continue;
    }

    //  ADMIN cannot delete admin
    if ($current['user_type'] === 'admin' && $target_role === 'admin') {
        continue;
    }

    // DELETE USER
    mysqli_query($conn, "DELETE FROM user_form WHERE id='$id'");
    $deleted++;
}

// -------------------------
// RESPONSE
// -------------------------
echo json_encode([
    "status" => "success",
    "message" => "$deleted user(s) deleted"
]);