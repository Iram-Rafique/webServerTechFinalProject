<?php
include 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode([]);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM user_form WHERE id='$user_id'");

if (!$result) {
    echo json_encode([]);
    exit;
}

$current = mysqli_fetch_assoc($result);

if (!$current) {
    echo json_encode([]);
    exit;
}

if ($current['user_type'] !== 'admin' && $current['user_type'] !== 'owner') {
    echo json_encode([]);
    exit;
}

$result = mysqli_query($conn, "SELECT id, name, email, user_type FROM user_form");

if (!$result) {
    echo json_encode([]);
    exit;
}

$users = [];

while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

echo json_encode($users);
exit;