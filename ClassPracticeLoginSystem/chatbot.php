<?php
include 'config.php';

// Get message safely
$message = strtolower(trim($_POST['message'] ?? ''));
$message = mysqli_real_escape_string($conn, $message);

/* =========================
   1. GREETING ✅ NEW
========================= */
if ($message == 'hi' || $message == 'hello') {

    echo "
    👋 Hello!
    <br><br>
    🤖 How can I help you?
    <br><br>
    🔍 Try:
    <br>• iphone
    <br>• price iphone
    <br>• pixel
    ";
}


/* =========================
   2. HELP
========================= */
elseif ($message == '' || $message == 'help') {

    echo "
    🤖 Try:
    <br>• iphone
    <br>• price iphone
    <br>• samsung
    ";
}


/* =========================
   3. ASK PRICE
========================= */
elseif (strpos($message, 'price') !== false) {

    $product_name = trim(str_replace('price', '', $message));

    $result = mysqli_query($conn, "
        SELECT description, price 
        FROM product 
        WHERE description LIKE '%$product_name%' 
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($result)) {

        echo "💰 {$row['description']} costs £{$row['price']}";

    } else {
        echo "😢 Product not found";
    }
}


/* =========================
   4. SEARCH PRODUCTS
========================= */
else {

    $result = mysqli_query($conn, "
        SELECT description, price, image 
        FROM product 
        WHERE description LIKE '%$message%' 
        LIMIT 5
    ");

    if (mysqli_num_rows($result)) {

        echo "🔍 Results:<br><br>";

        while ($row = mysqli_fetch_assoc($result)) {

            echo "
            <div>
                📱 {$row['description']} - £{$row['price']}<br>
                <img src='images/{$row['image']}' width='80'><br><br>
            </div>
            ";
        }

    } else {
        echo "😢 No products found";
    }
}
?>