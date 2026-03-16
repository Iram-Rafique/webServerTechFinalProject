<?php

//-------------------------------------------------
// 1. LOAD CONFIGURATION
//-------------------------------------------------

include 'config.php';


//-------------------------------------------------
// 2. DETECT LOGIN PROVIDER
//-------------------------------------------------

if(isset($_GET['state'])){
    $provider = "facebook";
}
elseif(isset($_GET['code'])){
    $provider = "google";
}
else{
    header("Location: login.php");
    exit();
}


//-------------------------------------------------
// 3. GOOGLE LOGIN
//-------------------------------------------------

if($provider === "google"){

    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);

    if(isset($token['error'])){
        die("Google authentication failed");
    }

    $google_client->setAccessToken($token['access_token']);

    $google_service = new Google_Service_Oauth2($google_client);

    $data = $google_service->userinfo->get();

    $name  = $data['given_name'] ?? '';
    $email = $data['email'] ?? '';
    $image = '';

    if(!empty($data['picture'])){

        $image_url = $data['picture'];
        $image_name = uniqid().'.jpg';

        file_put_contents(
            'uploaded_img/'.$image_name,
            file_get_contents($image_url)
        );

        $image = $image_name;
    }
}


//-------------------------------------------------
// 4. FACEBOOK LOGIN
//-------------------------------------------------

if($provider === "facebook"){
    if(isset($_GET['state'])){
        $_SESSION['FBRLH_state'] = $_GET['state'];
    }
    $accessToken = $facebook_helper->getAccessToken();

    if(!isset($accessToken)){
        die("Facebook authentication failed");
    }

    $response = $fb->get('/me?fields=id,name,email,picture', $accessToken);

    $user = $response->getGraphUser();

    $name  = $user['name'] ?? '';
    $email = $user['email'] ?? '';
    $image = '';

    if(isset($user['picture']['url'])){

        $image_url = $user['picture']['url'];
        $image_name = uniqid().'.jpg';

        file_put_contents(
            'uploaded_img/'.$image_name,
            file_get_contents($image_url)
        );

        $image = $image_name;
    }
}


//-------------------------------------------------
// 5. CHECK IF USER EXISTS
//-------------------------------------------------

$stmt = $conn->prepare("SELECT id FROM user_form WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $row = $result->fetch_assoc();
    $_SESSION['user_id'] = $row['id'];

}else{

    $stmt = $conn->prepare("INSERT INTO user_form (name,email,image) VALUES (?,?,?)");
    $stmt->bind_param("sss",$name,$email,$image);
    $stmt->execute();

    $_SESSION['user_id'] = $stmt->insert_id;
}


//-------------------------------------------------
// 6. REDIRECT USER
//-------------------------------------------------

header("Location: profile.php");
exit();

?>