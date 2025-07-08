<?php
session_start();
include('db_connect.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    die("Please log in.");
}
$user_id = intval($_SESSION['user_id']);

// Fetch Cart Items
$cart_items = [];
$sql = "SELECT c.product_id, c.quantity, c.status, p.name, p.price, p.image
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Upload prescription
$upload_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["prescription"])) {
    $target_dir = "uploads/";
    $filename = basename($_FILES["prescription"]["name"]);
    $target_file = $target_dir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
        $insert = $conn->prepare("INSERT INTO prescriptions (user_id, file_name) VALUES (?, ?)");
        $insert->bind_param("is", $user_id, $target_file);
        $insert->execute();
        $upload_message = "Prescription uploaded successfully.";
    } else {
        $upload_message = "Upload failed.";
    }
}

// Calculate bill
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.05;
$total = $subtotal + $tax;
?>

<!DOCTYPE html>
<html>
<head>
  <title>IKART - Your Cart Summary</title>
  <link rel="stylesheet" href="Ikart.css">
  <style>
    body { font-family: 'Inria Serif', serif; padding: 2rem; background: #fcf6f6; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    .summary, .upload, .tracking { margin-bottom: 2rem; }
    .message { color: green; margin-bottom: 1rem; }
  </style>
</head>
<body>

<h1>Your Cart</h1>

<?php if (empty($cart_items)): ?>
  <p>Your cart is empty. <a href="product.php">Go shop</a></p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
        <th>Delivery Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cart_items as $item): ?>
      <tr>
        <td><?= htmlspecialchars($item['name']) ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>$<?= number_format($item['price'], 2) ?></td>
        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        <td><?= ucfirst($item['status']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="summary">
    <p><strong>Subtotal:</strong> $<?= number_format($subtotal, 2) ?></p>
    <p><strong>Tax (5%):</strong> $<?= number_format($tax, 2) ?></p>
    <p><strong>Total:</strong> $<?= number_format($total, 2) ?></p>
  </div>

  <div class="upload">
    <h2>Upload Prescription</h2>
    <?php if ($upload_message): ?>
      <p class="message"><?= $upload_message ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="prescription" required />
      <button type="submit">Upload</button>
    </form>
  </div>
<?php endif; ?>
<!-- Back buttons at bottom -->
<a href="cart.php">
  <button>Back to Cart</button>
</a>
<a href="product.php">
  <button>Continue Shopping</button>
</a>


</body>
</html>
