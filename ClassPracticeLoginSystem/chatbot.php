<?php
include 'config.php';

// Get message safely
$message = strtolower(trim($_POST['message'] ?? ''));

/* =========================
   HELP
========================= */
if ($message == '' || $message == 'help') {
    echo "
    🤖 <strong>How can I help?</strong><br><br>
    Try:<br>
    • iphone<br>
    • price iphone<br>
    • samsung
    ";
}

/* =========================
   GREETINGS
========================= */
elseif (
    strpos($message, 'hi') !== false ||
    strpos($message, 'hello') !== false ||
    strpos($message, 'hey') !== false
) {
    echo "
    👋 Hello!<br><br>
    What are you looking for today?<br>
    Try:<br>
    • iphone<br>
    • price iphone<br>
    • samsung
    ";
}

/* =========================
   ASK PRICE
========================= */
elseif (strpos($message, 'price') !== false) {

    $product_name = trim(str_replace('price', '', $message));

    $stmt = $conn->prepare("
        SELECT description, price 
        FROM product 
        WHERE description LIKE ? 
        LIMIT 1
    ");

    $search = "%$product_name%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "💰 <strong>{$row['description']}</strong><br>Price: £{$row['price']}";
    } else {
        echo "😢 Product not found";
    }
}

/* =========================
   SEARCH PRODUCTS
========================= */
else {

    $stmt = $conn->prepare("
        SELECT description, price 
        FROM product 
        WHERE description LIKE ? 
        LIMIT 5
    ");

    $search = "%$message%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo "🔍 <strong>Results for '$message'</strong><br><br>";

        while ($row = $result->fetch_assoc()) {
            echo "• {$row['description']} - <strong>£{$row['price']}</strong><br>";
        }

    } else {
        echo "
        😢 No products found<br><br>
        Try:<br>
        • iphone<br>
        • samsung<br>
        • price iphone
        ";
    }
}
?>