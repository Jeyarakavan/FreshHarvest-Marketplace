<?php
session_start();
require_once 'db.php';

// Enhanced admin verification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php?error=admin_access_denied");
    exit();
}

// Initialize variables
$users = [];
$products = [];
$success_message = '';
$error_message = '';

try {
    // Get all users (excluding current admin)
    $stmt = $pdo->prepare("SELECT id, full_name, email, is_admin, created_at FROM users WHERE id != ?");
    $stmt->execute([$_SESSION['user_id']]);
    $users = $stmt->fetchAll();

    // Get all products with farmer info
    $stmt = $pdo->prepare("SELECT p.*, u.full_name as farmer_name 
                          FROM products p 
                          JOIN users u ON p.farmer_id = u.id
                          ORDER BY p.created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll();

    // Handle product deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
        $productId = (int)$_POST['product_id'];
        
        // Verify product exists before deletion
        $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        
        if ($stmt->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $success_message = "Product deleted successfully!";
        } else {
            $error_message = "Product not found!";
        }
    }

    // Handle user deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
        $userId = (int)$_POST['user_id'];
        
        // Prevent admin from deleting themselves
        if ($userId == $_SESSION['user_id']) {
            $error_message = "You cannot delete your own admin account!";
        } else {
            // Verify user exists before deletion
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->fetch()) {
                // First delete user's products to maintain referential integrity
                $stmt = $pdo->prepare("DELETE FROM products WHERE farmer_id = ?");
                $stmt->execute([$userId]);
                
                // Then delete the user
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $success_message = "User deleted successfully!";
            } else {
                $error_message = "User not found!";
            }
        }
    }

} catch (PDOException $e) {
    error_log("Admin Dashboard Error: " . $e->getMessage());
    $error_message = "A database error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FreshHarvest</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
            margin-right: 8px;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .admin-title {
            color: #2c3e50;
            font-size: 28px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-admin {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-user {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="admin-header">
            <h1 class="admin-title">Admin Dashboard</h1>
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">View Site</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if($error_message): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>User Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $user['is_admin'] ? 'status-admin' : 'status-user'; ?>">
                                <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this user and all their products?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Product Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Farmer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($product['image'] ?? 'default-product.jpg'); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>LKR <?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($product['farmer_name']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="delete_product" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this product?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>