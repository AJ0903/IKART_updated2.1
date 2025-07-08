<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    die("Please log in to view your saved products.");
}
$user_id = intval($_SESSION['user_id']);

// Fetch saved products
$sql = "SELECT p.product_id, p.name, p.price, p.image 
        FROM products p 
        INNER JOIN buy_later b ON p.product_id = b.product_id 
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$buyLaterList = [];
while ($row = $result->fetch_assoc()) {
    $buyLaterList[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Buy Later - IKART</title>
<link href="https://fonts.googleapis.com/css2?family=Inria+Serif&display=swap" rel="stylesheet" />
<style>
html, body {
  font-family: 'Inria Serif', serif;
  background-color: #fcf6f6;
  color: #0a1a40;
  overflow-x: hidden;
  min-height: 100vh;
  line-height: 1.5;
}
a { color: #66bfff; text-decoration: none; }
a:hover { text-decoration: underline; }
h1 { margin-bottom: 1rem; }
.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
  padding: 20px;
}
.product-card {
  background: white;
  border: 1px solid #ccc;
  border-radius: 10px;
  text-align: center;
  padding: 15px;
}
.product-card img {
  width: 150px;
  height: 150px;
  object-fit: cover;
}
.back-link {
  display: inline-block;
  margin-bottom: 15px;
  color: lightblue;
  text-decoration: none;
}
</style>
</head>
<body>

<a href="product.php" class="back-link">← Back to Products</a>
<h1>🕒 Items Saved for Later</h1>

<main>
  <div class="product-grid">
    <?php if (empty($buyLaterList)) : ?>
      <p style='grid-column: span 4; text-align:center;'>No items saved for later.</p>
    <?php else : ?>
      <?php foreach ($buyLaterList as $item) : ?>
        <div class="product-card">
          <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
          <h3><?php echo htmlspecialchars($item['name']); ?></h3>
          <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
