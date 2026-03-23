<?php
session_start();
require_once 'db.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get cart count if logged in
$cartCount = 0;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cartCount = $result['count'];
}

// Get products from database
$products = [];
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

try {
    $query = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if (!empty($searchTerm)) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }
    
    if (!empty($category)) {
        $query .= " AND category = ?";
        $params[] = $category;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshHarvest - Farm Fresh Produce</title>
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/script.js"></script>
    <style>
    .like-button {
        background-color: transparent;
        color: black;
        border: none;
        font-size: 20px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .like-button:hover {
        transform: scale(1.2);
        color:black;
    }

    .search-container {
        text-align: center;
        padding: 30px 0;
        background-color: #36dfa1;
    }

    .search-container input[type=text], 
    .search-container button, 
    .search-container select {
        padding: 10px;
        margin: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .search-container button {
        background-color: rgb(255, 102, 0);
        color: rgb(0, 0, 0);
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-container button:hover {
        background-color: rgb(250, 148, 59);
        color: black;
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

    main {padding-top: 155px;}

    input {
      background-color: #eee;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 100%;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .grid-item {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .grid-item:hover {
        transform: translateY(-5px);
    }

    .grid-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .product-info {
        padding: 15px;
        text-align: center;
    }

    .product-info h2 {
        font-size: 1.8rem;
        margin-bottom: 10px;
        color: #333;
    }

    .product-info p {
        font-size: 1.2rem;
        margin-bottom: 5px;
        color: #666;
    }

    .product-price {
        font-size: 1.6rem;
        color: #228B22;
        margin-bottom: 15px;
        font-weight: bold;
    }

    .add-to-cart {
        background: #228B22;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s;
        width: 100%;
    }

    .add-to-cart:hover {
        background: #1a6f1a;
    }

    .add-to-cart:disabled {
        background: #cccccc;
        cursor: not-allowed;
    }

    .cart-link {
        position: relative;
        display: inline-block;
    }

    .cart-count {
        background: #ff4757; 
        color: white; 
        border-radius: 50%; 
        padding: 2px 6px; 
        font-size: 12px; 
        position: relative; 
        top: -10px; 
        right: 10px;
        transition: all 0.3s ease;
    }

    .pulse {
        animation: pulse 0.5s;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        padding: 2rem;
        border-radius: 10px;
        text-align: center;
        max-width: 400px;
        width: 90%;
    }

    .modal h3 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .modal p {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
        color: #666;
    }

    .modal-buttons {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .modal-btn {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        border: none;
        transition: all 0.3s;
    }

    .modal-btn.login {
        background: #228B22;
        color: white;
    }

    .modal-btn.continue {
        background: #ccc;
        color: #333;
    }

    .modal-btn:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .farmer-badge {
        background-color: #ff5e00;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: bold;
        display: inline-block;
        margin-left: 5px;
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
          <?php if($isLoggedIn): ?>
            <li><a href="Accounts.php"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="account.php"><img src="images/account.png" alt="Account" class="nav-image"></a></li>
          <?php endif; ?>
          <li>
              <a href="cart.php" class="cart-link">
                  <img src="images/cart.png" alt="Shopping Cart" class="nav-image">
                  <?php if($isLoggedIn && $cartCount > 0): ?>
                      <span id="cart-count" class="cart-count"><?php echo $cartCount; ?></span>
                  <?php endif; ?>
              </a>
          </li>
        </ul>
      </nav>
    </header>
    
    <main>
        <div class="search-container">
            <form method="GET" action="products.php">
                <input type="text" id="searchInput" name="search" placeholder="Search products" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <select id="categorySelect" name="category">
                    <option value="">All Categories</option>
                    <option value="fruits" <?php echo ($category == 'fruits') ? 'selected' : ''; ?>>Fruits</option>
                    <option value="vegetables" <?php echo ($category == 'vegetables') ? 'selected' : ''; ?>>Vegetables</option>
                    <option value="organic" <?php echo ($category == 'organic') ? 'selected' : ''; ?>>Organic</option>
                    <option value="seasonal" <?php echo ($category == 'seasonal') ? 'selected' : ''; ?>>Seasonal</option>
                    <option value="local" <?php echo ($category == 'local') ? 'selected' : ''; ?>>Local Farms</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="product" id="product">
            <section id="products" class="grid-container">
                <?php foreach ($products as $product): ?>
                <div class="grid-item">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-info">
                        <h2><?php echo htmlspecialchars($product['name']); ?>
                            <?php if($product['is_organic']): ?>
                                <span class="organic-badge">ORGANIC</span>
                            <?php endif; ?>
                            <?php if($product['farmer_id']): ?>
                                <span class="farmer-badge">LOCAL FARMER</span>
                            <?php endif; ?>
                        </h2>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="product-price">LKR <?php echo number_format($product['price'], 2); ?>/<?php echo htmlspecialchars($product['unit']); ?></p>
                        <button class="add-to-cart" 
                                onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, this)">
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </section>
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
    
    <script>
        // Newsletter subscription functionality
        document.getElementById('newsletterBtn').addEventListener('click', function() {
            const emailInput = this.parentElement.querySelector('input[type="email"]');
            if(emailInput.value && emailInput.value.includes('@')) {
                this.textContent = 'Subscribed!';
                this.style.backgroundColor = '#4CAF50';
                setTimeout(() => {
                    emailInput.value = '';
                    this.textContent = 'Subscribe';
                    this.style.backgroundColor = '#187318';
                }, 2000);
            } else {
                alert('Please enter a valid email address');
            }
        });

        // Add to cart functionality
        function addToCart(productId, productName, productPrice, button) {
            <?php if($isLoggedIn): ?>
                // Show loading state
                button.disabled = true;
                button.textContent = 'Adding...';
                
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        productId: productId,
                        productName: productName,
                        productPrice: productPrice,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        button.textContent = 'Added!';
                        
                        // Update cart count display
                        let cartCount = document.getElementById('cart-count');
                        if (!cartCount) {
                            cartCount = document.createElement('span');
                            cartCount.id = 'cart-count';
                            cartCount.className = 'cart-count';
                            document.querySelector('.cart-link').appendChild(cartCount);
                        }
                        cartCount.textContent = data.cartCount;
                        cartCount.style.display = 'inline-block';
                        cartCount.classList.add('pulse');
                        setTimeout(() => cartCount.classList.remove('pulse'), 500);
                        
                        setTimeout(() => {
                            button.textContent = 'Add to Cart';
                            button.disabled = false;
                        }, 1500);
                    } else {
                        alert('Error: ' + data.message);
                        button.textContent = 'Add to Cart';
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add to cart');
                    button.textContent = 'Add to Cart';
                    button.disabled = false;
                });
            <?php else: ?>
                // Show login modal
                const modal = document.createElement('div');
                modal.className = 'modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <h3>Login Required</h3>
                        <p>You need to login to add items to your cart.</p>
                        <div class="modal-buttons">
                            <button class="modal-btn login" onclick="window.location.href='account.php'">Go to Login</button>
                            <button class="modal-btn continue" onclick="this.closest('.modal').remove()">Continue Shopping</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            <?php endif; ?>
        }

        // Preloader
        var loader = document.getElementById("preloader");
        window.addEventListener("load", function() {
            loader.style.display = "none";
        });
    </script>
</body>
</html>