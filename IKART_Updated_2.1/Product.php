<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    die("Please log in to view and save products.");
}
$user_id = intval($_SESSION['user_id']);

// Handle Save for Later (still managed here)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && ($_POST['action'] ?? '') === 'save_for_later') {
    $pid = intval($_POST['product_id']);

    // Prevent duplicate saves
    $stmt = $conn->prepare("SELECT id FROM buy_later WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $pid);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt_insert = $conn->prepare("INSERT INTO buy_later (user_id, product_id) VALUES (?, ?)");
        $stmt_insert->bind_param("ii", $user_id, $pid);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt->close();
    header("Location: product.php");
    exit;
}

// Fetch products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>IKART - Products</title>
<link href="https://fonts.googleapis.com/css2?family=Inria+Serif&display=swap" rel="stylesheet" />
<style>
body { font-family: 'Inria Serif', serif; background:#0a1a40; color:white; padding:20px; }
.product { background:#132a5a; padding:15px; margin-bottom:20px; border-radius:8px; }
form { margin-top:10px; display:inline-block; margin-right:10px; }
input[type=submit], button { background:#007bff; border:none; padding:10px 15px; color:#fff; border-radius:5px; cursor:pointer; }
input[type=submit]:hover, button:hover { background:#0056b3; }
a { color:#66bfff; text-decoration:none; }
a:hover { text-decoration:underline; }
img.product-image { max-width: 120px; height: auto; display: block; margin-top: 10px; }
</style>
</head>
<body>

<!-- Back buttons -->
<a href="cart.php"><button>Back to Cart</button></a>
<a href="product.php"><button>Continue Shopping</button></a>

<h1>Products</h1>
<a href="cart.php">🛒 View Cart</a> |
<a href="buylater.php">🕒 View Saved for Later</a>

<div>
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='product'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "<p>Price: $" . number_format($row['price'], 2) . "</p>";
        echo "<p>Age Group: " . htmlspecialchars($row['age_group']) . "</p>";
        if (!empty($row['image'])) {
            echo "<img class='product-image' src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
        }

        // ✅ Add to Cart form (now sends to add_to_cart.php)
        echo "<form method='POST' action='add_to_cart.php'>";
        echo "<input type='hidden' name='product_id' value='" . $row['product_id'] . "'>";
        echo "Quantity: <input type='number' name='quantity' value='1' min='1' style='width: 50px; margin-left: 5px;'>";
        echo "<input type='submit' value='Add to Cart' style='margin-left: 10px;'>";
        echo "</form>";

        // 💾 Save for Later form (handled here)
        echo "<form method='POST'>";
        echo "<input type='hidden' name='product_id' value='" . $row['product_id'] . "'>";
        echo "<input type='hidden' name='action' value='save_for_later'>";
        echo "<input type='submit' value='💾 Save for Later'>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "<p>No products found.</p>";
}
?>
</div>

</body>
</html>
