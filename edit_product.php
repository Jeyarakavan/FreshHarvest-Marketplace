<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get product details
$product = [];
if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
        $product = $stmt->fetch();
        
        if (!$product) {
            header("Location: account.php");
            exit();
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $isOrganic = isset($_POST['is_organic']) ? 1 : 0;
    $unit = $_POST['unit'];
    
    // Handle image update
    $imageName = $product['image'];
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "images/$imageName");
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE products SET 
            name = ?, description = ?, price = ?, image = ?, 
            category = ?, is_organic = ?, unit = ?
            WHERE id = ? AND farmer_id = ?
        ");
        $stmt->execute([
            $name, $description, $price, $imageName, 
            $category, $isOrganic, $unit, 
            $_GET['id'], $_SESSION['user_id']
        ]);
        header("Location: account.php?success=product_updated");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - FreshHarvest</title>
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background: #36dfa1;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .current-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
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
          <li><a href="account.php"><img src="images/account.png" alt="Account" class="nav-image"></a></li>
          <li><a href="cart.php"><img src="images/cart.png" alt="Shopping Cart" class="nav-image"></a></li>
        </ul>
      </nav>
    </header>
    
    <main class="form-container">
        <h1>Edit Product</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Current Image</label>
                <img src="images/<?= htmlspecialchars($product['image']) ?>" class="current-image">
            </div>
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Price (LKR)</label>
                <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="form-group">
                <label>New Image (leave blank to keep current)</label>
                <input type="file" name="image">
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="fruits" <?= $product['category'] === 'fruits' ? 'selected' : '' ?>>Fruits</option>
                    <option value="vegetables" <?= $product['category'] === 'vegetables' ? 'selected' : '' ?>>Vegetables</option>
                    <option value="organic" <?= $product['category'] === 'organic' ? 'selected' : '' ?>>Organic</option>
                    <option value="seasonal" <?= $product['category'] === 'seasonal' ? 'selected' : '' ?>>Seasonal</option>
                    <option value="local" <?= $product['category'] === 'local' ? 'selected' : '' ?>>Local</option>
                </select>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_organic" <?= $product['is_organic'] ? 'checked' : '' ?>> Organic Product
                </label>
            </div>
            <div class="form-group">
                <label>Unit</label>
                <input type="text" name="unit" value="<?= htmlspecialchars($product['unit']) ?>" required>
            </div>
            <button type="submit" class="submit-btn">Update Product</button>
        </form>
    </main>

    <footer class="footer">
        <div class="footer-container">
          <div class="footer-section">
            <h3>FreshHarvest</h3>
            <p>Connecting farmers directly with consumers for the freshest produce delivery.</p>
            <div class="social-icons">
              <a href="#"><img src="images/faceboook.png" alt="Facebook"></a>
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
            <a href="account.php">Account</a>
            <a href="cart.php">Cart</a>
          </div><br>
          <p>Copyright © 2024 FreshHarvest</p>
        </div>
    </footer>

</body>
</html>