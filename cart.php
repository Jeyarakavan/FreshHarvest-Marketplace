<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

// Get cart items
$cartItems = [];
$subtotal = 0;
$totalItems = 0;

try {
    // Get cart items with product details
    $stmt = $pdo->prepare("
        SELECT c.*, p.image, p.description, p.unit 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();

    // Calculate subtotal and total items
    foreach ($cartItems as $item) {
        $subtotal += $item['product_price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }

    // Handle quantity updates
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_quantity'])) {
            foreach ($_POST['quantity'] as $cartId => $quantity) {
                if (is_numeric($quantity) && $quantity > 0) {
                    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$quantity, $cartId, $_SESSION['user_id']]);
                }
            }
            header("Location: cart.php");
            exit();
        }

        // Handle remove item
        if (isset($_POST['remove_item'])) {
            $cartId = $_POST['cart_id'];
            $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cartId, $_SESSION['user_id']]);
            header("Location: cart.php");
            exit();
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Delivery fee (could be dynamic based on location)
$deliveryFee = 50.00;
$total = $subtotal + $deliveryFee;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshHarvest - Your Cart</title>
  <link rel="stylesheet" href="css/style1.css">
  <script src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Cart Main Styles */
    main {padding-top: 50px;}
    
    .cart {
        background-color: #fff;
        padding: 5rem 2rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        margin-top: 8rem;
        border: 1px solid #e0e0e0;
    }

    .table__container {
        overflow-x: auto;
        padding: 50px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 50px;
    }

    .table th,
    .table td {
        padding: 1.2rem;
        border-bottom: 1px solid #433f3f;
        text-align: left;
        vertical-align: middle;
    }

    .table th {
        background-color: #f5e7ba;
        font-weight: 600;
        color: #000000;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.5px;
        font-weight: bold;
    }

    .table__img {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .table__title {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        font-weight: 600;
    }

    .table__description {
        font-size: 1rem;
        color: #7f8c8d;
    }

    .table__price,
    .subtotal {
        font-weight: bold;
        color: #000000;
        font-size: 1.2rem;
    }

    .quantity {
        width: 60px;
        padding: 0.6rem;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: bold;
    }

    .table__trash {
        cursor: pointer;
        color: #e74c3c;
        background: none;
        border: none;
        font-size: 1.3rem;
        transition: transform 0.2s;
    }

    .table__trash:hover {
        transform: scale(1.1);
    }

    /* Button Styles */
    .cart__actions {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .btn {
        background-color: #312c28;
        color: white;
        padding: 0.9rem 1.8rem;
        text-align: center;
        text-decoration: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(136, 133, 133, 0.1);
    }

    .btn:hover {
        background-color: #696464;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn--outline {
        background-color: transparent;
        border: 2px solid #4c514e;
        color: #020200;
    }

    .btn--outline:hover {
        background-color: #000000;
        color: white;
    }

    /* Special Checkout Button */
    .btn-checkout {
        background-color: #bbc31d;
        color: rgb(0, 0, 0);
        padding: 1.2rem;
        font-size: 1.2rem;
        width: 100%;
        margin-top: 1rem;
    }
    
    .btn-checkout:hover {
        background-color: #d3dd40;
    }
    
    .btn-update {
        background-color: #162971;
    }
    
    .btn-update:hover {
        background-color: #2980b9;
    }
    
    .btn-apply {
        background-color: #3d0f4e;
    }
    
    .btn-apply:hover {
        background-color: #762798;
    }
    
    .btn-location {
        background-color: #7e0f0b;
    }
    
    .btn-location:hover {
        background-color: #c61313;
    }

    /* Divider */
    .divider {
        margin: 2.5rem 0;
        text-align: center;
        color: #333;
        position: relative;
    }

    .divider::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background-color: #e0e0e0;
        z-index: -1;
    }

    .divider span {
        background-color: white;
        padding: 0 1.5rem;
        position: relative;
        font-weight: 600;
        color: #7f8c8d;
    }

    /* Cart Group Layout */
    .cart__group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
    }

    .section__title {
        font-size: 20px;
        margin-bottom: 10px;
        color: #2c3e50;
        font-weight: bold;
        position: relative;
        padding-bottom: 10px;
    }

    .section__title::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 155px;
        height: 5px;
        background-color: #2ecc71;
    }

    /* Form Styles */
    .form {
        display: grid;
        gap: 15px;
        background-color: #c5cbce;
        padding: 20px;
        border-radius: 8px;
    }

    .form__input {
        width: 100%;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1.1rem;
        transition: border-color 0.3s;
    }

    .form__input:focus {
        border-color: #2ecc71;
        outline: none;
        box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.2);
    }

    .form__group {
        display: grid;
        gap: 1.2rem;
        grid-template-columns: 1fr 1fr;
    }

    .form__btn {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    /* Cart Total */
    .cart__total {
        background-color: #ced3d7;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #fdfdfd;
    }

    .cart__total-table {
        width: 100%;
        margin-bottom: 2rem;
        border-collapse: separate;
        border-spacing: 0 1rem;
    }

    .cart__total-table tr:last-child td {
        padding-top: 1.5rem;
        border-top: 1px solid #e0e0e0;
    }

    .cart__total-title {
        font-size: 1.2rem;
        color: #000000;
    }

    .cart__total-price {
        font-size: 1.3rem;
        font-weight: bold;
        color: #2c3e50;
        text-align: right;
    }

    .cart__total-price.total {
        color: #010101;
        font-size: 1.5rem;
    }
    
    /* Empty Cart State */
    .empty-cart {
        text-align: center;
        padding: 4rem 0;
    }
    
    .empty-cart__icon {
        font-size: 5rem;
        color: #bdc3c7;
        margin-bottom: 1.5rem;
    }
    
    .empty-cart__message {
        font-size: 1.5rem;
        color: #7f8c8d;
        margin-bottom: 2rem;
    }
    
    /* Saved for Later Section */
    .saved-items {
        margin-top: 3rem;
        padding-top: 3rem;
        border-top: 1px solid #e0e0e0;
    }
    
    .saved-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    
    .saved-item__content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    /* Payment Methods */
    .payment-methods {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        justify-content: center;
    }
    
    .payment-method {
        width: 50px;
        height: 30px;
        object-fit: contain;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        padding: 0.3rem;
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .cart__group {
            grid-template-columns: 1fr;
        }
        
        .form__group {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .cart {
            padding: 3rem 1.5rem;
            margin-top: 6rem;
        }
        
        .table th,
        .table td {
            padding: 0.8rem;
        }
        
        .table__img {
            width: 60px;
            height: 60px;
        }
        
        .section__title {
            font-size: 1.5rem;
        }
        
        .cart__actions {
            flex-direction: column;
        }
    }

    input {
      background-color: #ffffff;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 100%;
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
          <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="Accounts.php"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="account.php"><img src="images/account.png" alt="Account" class="nav-image"></a></li>
          <?php endif; ?>
          <li>
            <a href="cart.php" class="cart-link">
              <img src="images/cart.png" alt="Shopping Cart" class="nav-image">
              <?php if(isset($_SESSION['user_id']) && $totalItems > 0): ?>
                <span id="cart-count" class="cart-count"><?php echo $totalItems; ?></span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
      </nav>
    </header>

    <main>
        <section class="cart section--lg container">
          <?php if(empty($cartItems)): ?>
            <div class="empty-cart">
              <div class="empty-cart__icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <h2 class="empty-cart__message">Your cart is empty</h2>
              <a href="products.php" class="btn btn--outline">
                <i class="fas fa-shopping-bag"></i> Continue Shopping
              </a>
            </div>
          <?php else: ?>
            <div class="table__container">
              <form method="POST" action="cart.php">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Product</th>
                      <th>Details</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Subtotal</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($cartItems as $item): ?>
                    <tr>
                      <td>
                        <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="table__img" />
                      </td>
                      <td>
                        <h3 class="table__title"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <p class="table__description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <p class="table__description">Price per <?php echo htmlspecialchars($item['unit']); ?></p>
                      </td>
                      <td>
                        <span class="table__price">LKR <?php echo number_format($item['product_price'], 2); ?></span>
                      </td>
                      <td>
                        <div class="quantity-control">
                          <input type="number" name="quantity[<?php echo $item['id']; ?>]" 
                                 value="<?php echo $item['quantity']; ?>" min="1" class="quantity" />
                        </div>
                      </td>
                      <td>
                        <span class="subtotal">LKR <?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></span>
                      </td>
                      <td>
                        <button type="submit" name="remove_item" class="table__trash" title="Remove item">
                          <i class="fas fa-trash-alt"></i>
                          <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>

                <div class="cart__actions">
                  <button type="submit" name="update_quantity" class="btn btn-update">
                    <i class="fas fa-sync-alt"></i> Update Cart
                  </button>
                  <a href="products.php" class="btn btn--outline">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                  </a>
                </div>
              </form>
            </div>

            <div class="divider"><span>OR</span></div>

            <div class="cart__group grid">
              <div>
                <div class="cart__shippinp">
                  <h3 class="section__title">Delivery Information</h3>
                  <form action="" class="form grid">
                    <input type="text" class="form__input" placeholder="City" required>
                    <div class="form__group grid">
                      <input type="text" class="form__input" placeholder="Area" required>
                      <input type="text" class="form__input" placeholder="Postal Code" required>
                    </div>
                    <div class="form__btn">
                      <button class="btn btn-location">
                        <i class="fas fa-map-marker-alt"></i> Update Location
                      </button>
                    </div>
                  </form>
                </div>
                <div class="cart__coupon">
                  <h3 class="section__title">Apply Promo Code</h3>
                  <form action="" class="coupon__form form grid">
                    <div class="form__group grid">
                      <input type="text" class="form__input" placeholder="Enter Promo Code">
                      <div class="form__btn">
                        <button class="btn btn-apply">
                          <i class="fas fa-tag"></i> Apply
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
                
                <!-- Saved for Later Section -->
                <div class="saved-items">
                  <h1 class="section__title">Saved for Later (2)</h1>
                  <div class="saved-item">
                    <div class="saved-item__content">
                      <img src="images/tomatoes.jpg" alt="Organic Tomatoes" class="table__img" />
                      <div>
                        <h4 class="table__title">Organic Tomatoes</h4>
                        <p class="table__description">LKR 707.00/kg</p>
                      </div>
                    </div>
                    <button class="btn btn--outline" style="padding: 0.5rem 1rem;">
                      <i class="fas fa-cart-plus"></i> Move to Cart
                    </button>
                  </div>
                </div>
              </div>

              <div class="cart__total">
                <h3 class="section__title">Order Summary</h3>
                <table class="cart__total-table">
                  <tr>
                    <td><span class="cart__total-title">Subtotal (<?php echo $totalItems; ?> items)</span></td>
                    <td><span class="cart__total-price">LKR <?php echo number_format($subtotal, 2); ?></span></td>
                  </tr>
                  <tr>
                    <td><span class="cart__total-title">Delivery Fee</span></td>
                    <td><span class="cart__total-price">LKR <?php echo number_format($deliveryFee, 2); ?></span></td>
                  </tr>
                  <tr>
                    <td><span class="cart__total-title">Discount</span></td>
                    <td><span class="cart__total-price">-LKR 0.00</span></td>
                  </tr>
                  <tr>
                    <td><span class="cart__total-title">Total</span></td>
                    <td><span class="cart__total-price total">LKR <?php echo number_format($total, 2); ?></span></td>
                  </tr>
                </table>
                
                <div class="payment-methods">
                  <img src="images/visa.png" alt="Visa" class="payment-method">
                  <img src="images/mastercard.png" alt="Mastercard" class="payment-method">
                  <img src="images/paypal.png" alt="PayPal" class="payment-method">
                  <img src="images/upi.png" alt="UPI" class="payment-method">
                </div>
                
                <a href="payment.php" class="btn btn-checkout">
                  <i class="fas fa-lock"></i> Proceed To Secure Checkout
                </a>
                
                <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem; color: #7f8c8d;">
                  <i class="fas fa-shield-alt"></i> Secure SSL Encryption
                </p>
              </div>
            </div>
          <?php endif; ?>
        </section>
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

      // Preloader
      var loader = document.getElementById("preloader");
      window.addEventListener("load", function() {
        loader.style.display = "none";
      });
    </script>
</body>
</html>