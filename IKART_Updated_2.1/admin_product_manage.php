<?php
include 'db_connect.php';

// Fetch all products
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Product List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #2c3e50;
      color: white;
    }
    img {
      width: 100px;
      height: auto;
    }
  </style>
</head>
<body>

  <h2>📦 Product List (Admin View)</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price ($)</th>
        <th>Stock</th>
        <th>Age Group</th>
        <th>Description</th>
        <th>Image</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>";
          echo "<td>{$row['product_id']}</td>";
          echo "<td>{$row['name']}</td>";
          echo "<td>{$row['price']}</td>";
          echo "<td>{$row['stock']}</td>";
          echo "<td>{$row['age_group']}</td>";
          echo "<td>{$row['description']}</td>";
          echo "<td><img src='IMAGES/{$row['image']}' alt='{$row['name']}'></td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='7'>No products found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
  

</body>
</html>
