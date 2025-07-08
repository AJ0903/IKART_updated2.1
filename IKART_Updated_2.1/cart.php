<?php 
session_start();
include('db_connect.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to view your cart.");
}

$user_id = intval($_SESSION['user_id']);

// Fetch Cart Items from DB
$cart_items = [];
$sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.image
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

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$tax_percent = 5;
$tax = $subtotal * $tax_percent / 100;
$discount_percent = 0;
$discount_amount = 0;
$total = $subtotal + $tax - $discount_amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>IKART - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Inria+Serif&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="Ikart.css" />
  <style>
    html, body {
      font-family: 'Inria Serif', serif;
      background-color: #fcf6f6;
      color: #0a1a40;
      overflow-x: hidden;
      min-height: 100vh;
      line-height: 1.5;
    }

    table {
      width: 90%;
      margin: 1rem auto;
      border-collapse: collapse;
    }

    table th, table td {
      padding: 12px 15px;
      border: 1px solid #ccc;
      text-align: center;
    }

    .summary {
      width: 90%;
      margin: 1rem auto;
      font-size: 1.1rem;
    }

    .checkout-button {
      display: block;
      width: fit-content;
      margin: 1rem auto 2rem auto;
      padding: 12px 20px;
      background-color: #28a745;
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
    }

    .checkout-button:hover {
      background-color: #218838;
    }

    button {
      font-family: 'Inria Serif', serif;
      padding: 8px 14px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin: 0.5rem;
    }

    button.remove {
      background-color: #dc3545;
    }

    button.remove:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>

<h1 style="text-align: center;">Your Cart</h1>

<div style="text-align: center;">
  <a href="product.php"><button>Continue Shopping</button></a>
</div>

<?php if (empty($cart_items)): ?>
  <p style="text-align:center;">Your cart is empty. <a href="product.php">Go to products</a></p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price Each</th>
        <th>Total Price</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cart_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= intval($item['quantity']) ?></td>
          <td>$<?= number_format($item['price'], 2) ?></td>
          <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
          <td>
            <form method="POST" action="remove_from_cart.php" onsubmit="return confirm('Remove this item?');">
              <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
              <button type="submit" class="remove">Remove</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="summary">
    <p>Subtotal: $<?= number_format($subtotal, 2) ?></p>
    <p>Tax (<?= $tax_percent ?>%): $<?= number_format($tax, 2) ?></p>
    <p>Discount: $<?= number_format($discount_amount, 2) ?> (<?= $discount_percent ?>%)</p>
    <hr>
    <p><strong>Total: $<?= number_format($total, 2) ?></strong></p>
  </div>

  <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
<?php endif; ?>

</body>
</html>