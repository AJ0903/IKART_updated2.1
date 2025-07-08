<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php';

$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - IKART</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            padding: 20px;
        }
        h2, h3 {
            color: #0a1a40;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        img {
            width: 50px;
            height: auto;
        }
        a.logout {
            display: inline-block;
            margin-top: 20px;
            color: #fff;
            background-color: #0a1a40;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Welcome, Admin</h2>
    <h3>Product List</h3>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Image</th><th>Age Group</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td>$<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>
                <?php if (!empty($row['image'])) { ?>
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <?php } else { ?>
                    No Image
                <?php } ?>
            </td>
            <td><?php echo htmlspecialchars($row['age_group']); ?></td>
        </tr>
        <?php } ?>
    </table>

    <a href="admin_logout.php" class="logout">Logout</a>
</body>
</html>
