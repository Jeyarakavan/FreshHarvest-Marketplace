<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user details, products, and purchase history
$user = [];
$products = [];
$purchases = [];

try {
    // Get user info
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        throw new PDOException("User not found for ID: " . $_SESSION['user_id']);
    }

    // Get user's active products
    $stmt = $pdo->prepare("SELECT * FROM products WHERE farmer_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $products = $stmt->fetchAll();

    // Get user's purchase history from cart table
    $stmt = $pdo->prepare("SELECT id, created_at AS order_date, quantity, product_id, product_name, product_price
                           FROM cart
                           WHERE user_id = ?
                           ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $purchases = $stmt->fetchAll();

    // Handle product deletion (hard delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        
        if ($productId) {
            // Verify product exists and belongs to the user
            $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND farmer_id = ?");
            $stmt->execute([$productId, $_SESSION['user_id']]);
            
            if ($stmt->fetch()) {
                // Permanently delete product
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                
                header("Location: Accounts.php?success=product_deleted");
                exit();
            } else {
                header("Location: Accounts.php?error=product_not_found");
                exit();
            }
        } else {
            header("Location: Accounts.php?error=invalid_product_id");
            exit();
        }
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    die("An error occurred. Please try again later.");
}

// Helper function to get user name
function getUserName($pdo, $userId) {
    if (!$userId) return 'Unknown';
    
    try {
        $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 'Unknown';
    } catch (PDOException $e) {
        error_log("Error in getUserName: " . $e->getMessage());
        return 'Unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshHarvest - My Account</title>
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/script.js"></script>
    <style>
        main {padding-top: 100px;}
        .profile-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
            padding: 2rem 9%;
            margin-top: 2.2cm;
        }

        .sidebar {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 15px;
        }

        .sidebar-menu a {
            color: #333;
            text-decoration: none;
            font-size: 1.6rem;
            display: block;
            padding: 8px 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #36dfa1;
            color: white;
        }

        .profile-content {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .profile-header h2 {
            font-size: 2.5rem;
            color: #333;
        }

        .products-section, .purchases-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 1.4rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #36dfa1;
            color: white;
        }

        .btn-danger {
            background-color: #ff5e5e;
            color: white;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .organic-badge {
            background-color: #36dfa1;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-left: 5px;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #36dfa1;
        }

        .order-header {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .purchase-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .purchase-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }

        .purchase-details {
            flex-grow: 1;
        }

        .purchase-date {
            color: #666;
            font-size: 1.4rem;
        }

        .purchase-price {
            font-weight: bold;
            color: #36dfa1;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div id="preloader"></div>
    <header>
        <a href="" class="logo">
            <img src="images/logo.png" alt="FreshHarvest Logo" class="logo-img">
        </a>
    
        <a href="" class="logo">
            FreshHarvest - Farm to Table<span>.</span>
        </a>
    
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="Accounts.php"><img src="images/account.png" alt="Account" class="nav-image"></a></li>
                <li><a href="cart.php"><img src="images/cart.png" alt="Shopping Cart" class="nav-image"></a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <div class="profile-container">
            <div class="sidebar">
                <div class="user-profile">
                    <div style="text-align: center; margin-bottom: auto;">
                        <img src="images/user-avatar.jpg" alt="User" class="user-avatar">
                        <h3 style="margin-top: 10px; font-size: 1.8rem;"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    </div>
                </div>
                <ul class="sidebar-menu">
                    <li><a href="#" class="active">Dashboard</a></li>
                    <li><a href="#products">My Products</a></li>
                    <li><a href="add_product.php">Add Product</a></li>
                    <li><a href="#purchases">Purchase History</a></li>
                    <li><a href="index.php">Back to Home</a></li>
                </ul>
            </div>

            <div class="profile-content">
                <?php if(isset($_GET['success']) && $_GET['success'] == 'product_deleted'): ?>
                    <div class="success-message">
                        Product deleted successfully!
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['error'])): ?>
                    <div class="error-message">
                        <?php 
                        echo $_GET['error'] == 'product_not_found' ? 
                            'Product not found or you don\'t have permission to delete it!' : 
                            'Invalid product ID!';
                        ?>
                    </div>
                <?php endif; ?>

                <div class="products-section" id="products">
                    <div class="profile-header">
                        <h2>My Products</h2>
                        <a href="add_product.php" class="btn btn-primary">Add New Product</a>
                    </div>

                    <?php if(empty($products)): ?>
                        <p>No products found. <a href="add_product.php">Add your first product</a></p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="images/<?php echo htmlspecialchars($product['image'] ?? 'default-product.jpg'); ?>" 
                                             class="product-image" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($product['name']); ?>
                                        <?php if($product['is_organic']): ?>
                                            <span class="organic-badge">ORGANIC</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo ucfirst($product['category']); ?></td>
                                    <td>LKR <?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($product['unit']); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
                                            Edit
                                        </a>
                                        <form method="POST" action="Accounts.php" style="display: inline-block;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="purchases-section" id="purchases">
                    <h2 class="section-title">Purchase History</h2>
                    
                    <?php if(empty($purchases)): ?>
                        <p>No purchase history found.</p>
                    <?php else: ?>
                        <?php foreach($purchases as $purchase): ?>
                            <div class="order-header">
                                <div style="display: flex; justify-content: space-between;">
                                    <div>
                                        <h3>Order #<?php echo $purchase['id']; ?></h3>
                                        <p class="purchase-date"><?php echo date('F j, Y, g:i a', strtotime($purchase['order_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="purchase-item">
                                <img src="images/default-product.jpg" class="purchase-image" alt="<?php echo htmlspecialchars($purchase['product_name']); ?>">
                                <div class="purchase-details">
                                    <h4><?php echo htmlspecialchars($purchase['product_name']); ?></h4>
                                    <p>
                                        Quantity: <?php echo $purchase['quantity']; ?> × LKR <?php echo number_format($purchase['product_price'], 2); ?>
                                    </p>
                                </div>
                                <div class="purchase-price">
                                    LKR <?php echo number_format($purchase['product_price'] * $purchase['quantity'], 2); ?>
                                </div>
                            </div>
                            <div style="text-align: right; margin-bottom: 30px;">
                                <p><strong>Order Total: LKR <?php echo number_format($purchase['product_price'] * $purchase['quantity'], 2); ?></strong></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>FreshHarvest</h3>
                <p>Connecting farmers directly with consumers for the freshest produce delivery.</p>
                <div class="social-icons">
                    <a href="#"><img src="images/facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
                    <a href="#"><img src="images/twitter.png" alt="Twitter"></a>
                    <a href="#"><img src="images/youtube.png" alt="YouTube"></a>
                    <a href="#"><img src="images/whatsapp.png" alt="WhatsApp"></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="support.php">F.A.Q</a>
                <a href="support.php">Delivery Policy</a>
                <a href="support.php">Terms Of Service</a>
                <a href="support.php">Farmer Support</a>
            </div>
            <div class="footer-section newsletter">
                <h3>Seasonal Updates</h3>
                <input type="email" placeholder="Your email id here">
                <button id="newsletterBtn">Subscribe</button>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-nav">
                <a href="index.php">Home</a>
                <a href="products.php">Produce</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="Accounts.php">Account</a>
                <a href="cart.php">Cart</a>
            </div><br>
            <p>Copyright © 2024 FreshHarvest</p>
        </div>
    </footer>
      
    <script>
        // Newsletter subscription functionality
        document.getElementById('newsletterBtn').addEventListener('click', function() {
            const emailInput = this.parentElement.querySelector('input[type="email"]');
            if(emailInput.value && emailInput.value.includes('@')) {
                alert('Successfully subscribed!');
            } else {
                alert('Please enter a valid email address');
            }
        });

        // Preloader
        var loader = document.getElementById("preloader");
        window.addEventListener("load", function() {
            loader.style.display = "none";
        });
    </script>
</body>
</html>