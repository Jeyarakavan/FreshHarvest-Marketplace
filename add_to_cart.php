<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$productId = $data['productId'];
$productName = $data['productName'];
$productPrice = $data['productPrice'];
$quantity = $data['quantity'] ?? 1;
$userId = $_SESSION['user_id'];

try {
    // Check if product already exists in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existingItem = $stmt->fetch();

    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Insert new item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $productId, $productName, $productPrice, $quantity]);
    }

    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();

    echo json_encode(['success' => true, 'cartCount' => $result['count'] ?? 0]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>