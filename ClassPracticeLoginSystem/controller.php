<?php
include('config.php');

$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

if (!isset($token['error'])) {

    $google_client->setAccessToken($token['access_token']);

    $google_service = new Google_Service_Oauth2($google_client);

    $data = $google_service->userinfo->get();

    $name = $email = $image = '';

    if (!empty($data['given_name'])) {
        $name = $data['given_name'];
    }

    if (!empty($data['email'])) {
        $email = $data['email'];
    }

    if (!empty($data['picture'])) {
        $image_url = $data['picture'];
        // Generate a unique filename for the image
        $image_name = uniqid() . '.jpg'; // You can adjust the extension based on the image type
        // Download the image and save it locally
        file_put_contents('uploaded_img/' . $image_name, file_get_contents($image_url));
        // Now $image_name contains the name of the saved image
        $image = $image_name;
    }

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO `user_form` (name, email, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $image);
    $stmt->execute();

    // Getting the last inserted ID
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Storing the user ID in the session
    session_start();
    $_SESSION['user_id'] = $user_id;

    header('location:profile.php');
    exit();
} else {
    // Handle error
    echo "Error: " . $token['error'];
}
?>