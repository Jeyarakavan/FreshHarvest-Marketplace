<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $isOrganic = isset($_POST['is_organic']) ? 1 : 0;
    $unit = $_POST['unit'];
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = "images/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (name, description, price, image, category, is_organic, unit, farmer_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name, 
            $description, 
            $price, 
            $imageName, 
            $category, 
            $isOrganic, 
            $unit, 
            $_SESSION['user_id']
        ]);
        
        header("Location: account.php?success=product_added");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - FreshHarvest</title>
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/script.js"></script>
    <style>
         main {padding-top: 150px;}
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input {
            width: auto;
            margin-right: 10px;
        }
        
        .btn-submit {
            background-color: #36dfa1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
    </style>
</head>
        <div id="preloader"></div>
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
    
    <main>
        <div class="form-container">
            <h1>Add New Product</h1>
            <form method="POST" action="add_product.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (LKR)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="fruits">Fruits</option>
                        <option value="vegetables">Vegetables</option>
                        <option value="organic">Organic</option>
                        <option value="seasonal">Seasonal</option>
                        <option value="local">Local</option>
                    </select>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="is_organic" name="is_organic" value="1">
                    <label for="is_organic">Organic Product</label>
                </div>
                
                <div class="form-group">
                    <label for="unit">Unit</label>
                    <input type="text" id="unit" name="unit" value="kg" required>
                </div>
                
                <button type="submit" class="btn-submit">Add Product</button>
            </form>
        </div>
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