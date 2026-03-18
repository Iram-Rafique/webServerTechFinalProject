<?php
include 'config.php';
$page_css = "shop.css";

$user_id = $_SESSION['user_id'] ?? null;

if(!$user_id){
   header('location:login.php');
   exit;
}

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_form WHERE id='$user_id'"));

if($user['user_type'] != 'user'){
   header('location:profile.php');
   exit;
}


/* ADD TO CART */
if(isset($_POST['add_to_cart'])){

   $product_id = $_POST['product_id'];
   $qty = $_POST['quantity'];

   // Check if already in cart
   $check = mysqli_query($conn,"SELECT * FROM shopping_cart 
   WHERE user_id='$user_id' AND product_id='$product_id'");

   if(mysqli_num_rows($check) > 0){

      // Update quantity instead
      mysqli_query($conn,"UPDATE shopping_cart 
      SET product_quantity = product_quantity + $qty 
      WHERE user_id='$user_id' AND product_id='$product_id'");

   } else {

      $product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM product WHERE id='$product_id'"));

      $name = $product['description'];
      $price = $product['price'];

      mysqli_query($conn,"INSERT INTO shopping_cart
      (user_id,product_id,product_name,product_price,product_quantity)
      VALUES('$user_id','$product_id','$name','$price','$qty')");
   }
}

/* DISPLAY PRODUCTS */

$products = mysqli_query($conn,"SELECT * FROM product");

?>
<?php include 'templates/header.php'; ?>
<?php include 'templates/navbar.php'; ?>
<h2>Shop</h2>

<div class="products">
<?php while($row = mysqli_fetch_assoc($products)){ ?>

<div class="product">
   <img src="products_img/<?php echo $row['image']; ?>" width="150">

   <h3><?php echo $row['description']; ?></h3>
   <p>£<?php echo $row['price']; ?></p>

   <form method="post">
      <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
      <input type="number" name="quantity" value="1" min="1">
      <input type="submit" name="add_to_cart" value="Add to Cart">
   </form>
</div>

<?php } ?>
</div>
<button id="chat-toggle">💬</button>
<div class="chatbot" id="chatbot">
    <div class="chat-header"> Chat</div>

    <div class="chat-body" id="chat-body"></div>

    <form id="chat-form">
        <input type="text" id="chat-input" placeholder="Ask something...">
        <button type="submit">Send</button>
    </form>
</div>
<script>
   const chatBtn = document.getElementById("chat-toggle");
const chatbot = document.getElementById("chatbot");

chatBtn.addEventListener("click", () => {
    if (chatbot.style.display === "none" || chatbot.style.display === "") {
        chatbot.style.display = "block";
    } else {
        chatbot.style.display = "none";
    }
});
document.getElementById("chat-form").addEventListener("submit", function(e){
    e.preventDefault();

    let input = document.getElementById("chat-input");
    let message = input.value;

    let chatBody = document.getElementById("chat-body");

    // Show user message
    chatBody.innerHTML += "<p><strong>You:</strong> " + message + "</p>";

    fetch("chatbot.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
    .then(res => res.text())
    .then(reply => {
        chatBody.innerHTML += "<p><strong>Bot:</strong> " + reply + "</p>";
        chatBody.scrollTop = chatBody.scrollHeight;
    });

    input.value = "";
});
</script>
<?php include 'templates/footer.php'; ?>