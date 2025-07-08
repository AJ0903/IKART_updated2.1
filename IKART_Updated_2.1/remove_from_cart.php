<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['product_id'])) {
    header("Location: cart.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$product_id = intval($_POST['product_id']);

$sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

header("Location: cart.php");
exit;
?>