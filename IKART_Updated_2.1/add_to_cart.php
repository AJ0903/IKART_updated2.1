<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    die("Please log in to add items to your cart.");
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $user_id = intval($_SESSION['user_id']);
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));

    // Check if product already in cart
    $check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $update->bind_param("iii", $quantity, $user_id, $product_id);
        $update->execute();
    } else {
        // Insert new cart item
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
    }

    header("Location: cart.php");
    exit();
} else {
    die("Invalid input.");
}
?>
