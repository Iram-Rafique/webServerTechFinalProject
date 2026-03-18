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
// GET & SANITIZE INPUT
// -------------------------
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password_raw = $_POST['password'] ?? '';
$user_type = $_POST['user_type'] ?? '';

// Escape for SQL
$name = mysqli_real_escape_string($conn, $name);
$email = mysqli_real_escape_string($conn, $email);
$user_type = mysqli_real_escape_string($conn, $user_type);

// -------------------------
// VALIDATION
// -------------------------
if (empty($name) || empty($email) || empty($password_raw) || empty($user_type)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email"]);
    exit;
}

// -------------------------
// CHECK DUPLICATE EMAIL
// -------------------------
$checkEmail = mysqli_query($conn, "SELECT id FROM user_form WHERE email='$email'");
if (mysqli_num_rows($checkEmail) > 0) {
    echo json_encode(["status" => "error", "message" => "Email already exists"]);
    exit;
}

// -------------------------
// ROLE VALIDATION
// -------------------------
$allowed_types = ['user', 'admin'];

if (!in_array($user_type, $allowed_types)) {
    echo json_encode(["status" => "error", "message" => "Invalid user type"]);
    exit;
}

// ❌ ADMIN cannot create ADMIN
if ($current['user_type'] === 'admin' && $user_type === 'admin') {
    echo json_encode([
        "status" => "error",
        "message" => "Admins cannot create admin"
    ]);
    exit;
}

// -------------------------
// HASH PASSWORD
// -------------------------
$password = password_hash($password_raw, PASSWORD_DEFAULT);

// -------------------------
// INSERT USER
// -------------------------
$query = "INSERT INTO user_form (name, email, password, user_type)
          VALUES ('$name', '$email', '$password', '$user_type')";

if (mysqli_query($conn, $query)) {

    $new_id = mysqli_insert_id($conn);

    echo json_encode([
        "status" => "success",
        "message" => "User added successfully",
        "user" => [
            "id" => $new_id,
            "name" => $name,
            "email" => $email,
            "user_type" => $user_type
        ]
    ]);

} else {
    echo json_encode([
        "status" => "error",
        "message" => "Database error"
    ]);
}