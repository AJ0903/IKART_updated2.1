<?php
// thankyou.php

// Optional: Retrieve any dynamic data here, e.g. order ID, delivery time.
// $orderId = $_GET['order_id'] ?? null;
// $eta = '10–15 minutes';
$eta = '10–15 minutes';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Thank You - IKART</title>
  <link href="https://fonts.googleapis.com/css2?family=Inria+Serif&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inria Serif', serif;
      background: #f9f9f9;
      color: #0a1a40;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      text-align: center;
      padding: 20px;
    }
    .thank-you-container {
      background: white;
      padding: 40px 60px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(10, 26, 64, 0.15);
      max-width: 400px;
    }
    h1 {
      margin-bottom: 20px;
      font-size: 2.2em;
    }
    p {
      font-size: 1.2em;
    }
    a {
      margin-top: 30px;
      display: inline-block;
      text-decoration: none;
      color: #007bff;
      font-weight: 600;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="thank-you-container">
    <h1>Thank You for Your Order!</h1>
    <p>We will deliver in <?php echo htmlspecialchars($eta); ?>.</p>
    <a href="index.php">Back to Home</a>
  </div>
</body>
</html>
