<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    die("Please log in to proceed.");
}

$user_id = intval($_SESSION['user_id']);
$success_message = "";

// Fetch cart items
$cart_items = [];
$total = 0;

$sql = "SELECT c.product_id, c.quantity, p.name, p.price
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing cart fetch: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);

    if (!empty($name) && !empty($address) && !empty($cart_items)) {
        // Insert into orders
        $order_sql = "INSERT INTO orders (user_id, customer_name, address, total_amount) VALUES (?, ?, ?, ?)";
        $order_stmt = $conn->prepare($order_sql);
        if (!$order_stmt) {
            die("Prepare failed for orders: " . $conn->error);
        }
        $order_stmt->bind_param("issd", $user_id, $name, $address, $total);
        $order_stmt->execute();
        $order_id = $order_stmt->insert_id;

        // Insert into order_items
        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_sql);
        if (!$item_stmt) {
            die("Prepare failed for order_items: " . $conn->error);
        }
        foreach ($cart_items as $item) {
            $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $item_stmt->execute();
        }

        // Clear cart
        $clear_sql = "DELETE FROM cart WHERE user_id = ?";
        $clear_stmt = $conn->prepare($clear_sql);
        if (!$clear_stmt) {
            die("Prepare failed for cart clear: " . $conn->error);
        }
        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();

        $success_message = "✅ Thank you, your order has been placed successfully!";
        $cart_items = []; // Prevent re-showing cart
    } else {
        $success_message = "❌ Please fill in all fields and ensure your cart is not empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Checkout - IKART</title>
  <style>
    body {
      font-family: 'Inria Serif', serif;
      background: #fefefe;
      color: #333;
      padding: 20px;
    }
    h1 {
      color: #081943;
      text-align: center;
    }
    .summary, form {
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
      background: #eef2ff;
      border-radius: 10px;
    }
    label, input, textarea {
      display: block;
      width: 100%;
      margin-bottom: 10px;
    }
    input, textarea {
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    input[type=submit] {
      background: #28a745;
      color: white;
      border: none;
      cursor: pointer;
    }
    input[type=submit]:hover {
      background: #218838;
    }
    .message {
      text-align: center;
      font-weight: bold;
      margin-top: 20px;
      color: green;
    }
    .message.error {
      color: red;
    }
  </style>
</head>
<body>

<h1>Checkout</h1>

<?php if (!empty($success_message)): ?>
  <p class="message <?= substr($success_message, 0, 1) === '❌' ? 'error' : '' ?>">
    <?= $success_message ?>
  </p>
<?php endif; ?>

<?php if (empty($success_message) && !empty($cart_items)): ?>
  <div class="summary">
    <h3>Cart Summary:</h3>
    <ul>
      <?php foreach ($cart_items as $item): ?>
        <li><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?>)</li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Total: $<?= number_format($total, 2) ?></strong></p>
  </div>

  <form method="POST">
    <h3>Shipping Details:</h3>
    <label for="name">Full Name:</label>
    <input type="text" name="name" required>

    <label for="address">Address:</label>
    <textarea name="address" rows="4" required></textarea>

    <input type="submit" value="Place Order">
  </form>
<?php elseif (empty($success_message)): ?>
  <p style="text-align:center;">Your cart is empty. <a href="product.php">Go to products</a></p>
<?php endif; ?>

</body>
</html>