<?php
session_start();
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get cart count if logged in
$cartCount = 0;
if ($isLoggedIn) {
    require_once 'db.php';
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $cartCount = $result['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshHarvest - Farm to Table</title>
  <link rel="stylesheet" href="css/style1.css">
  <script src="js/script.js"></script>
  <style>
.d1 div{
    width: 525px;
    height: 295.8px;
}
.d1 div img{
    border-radius: 10px;
    height: inherit;
    width: inherit;
    display: block;
}
.d1 div h1{
    margin: 20px 0 10px 15px;
    font-size: 40px;
}
.d1 div li{
    margin: 0 0 0 25px;
}
.d1 div h2{
    margin: 12px 0 5px 0;
    font-size: 25px;
}
.d1 div p{
    margin: 5px 0 5px 0;
    font-size: 20px;
}
.d1 div button{
    border-radius: 10px;
    font-size: 25px;
    padding: 7px;
    background: #ffffff;
    border: 2px solid #36dfa1;
    font-weight: bold;
    cursor: pointer;
}
.d1 div button:hover{
    color: #ffffff;
    background: #36dfa1;
}
.farmer .content {
  display:flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  background-color: gainsboro;
  width: auto;
  height: auto;
}

.farmer .content h3 {
  font-size: 6rem;
  color: #666;
}

.farmer .content span {
  font-size: 3.5rem;
  color: #333;
  padding: 1rem 0;
  line-height: 1.5;
}

.farmer .content p {
  font-size: 1.5rem;
  color: #333;
  padding: 1rem 0;
  line-height: 1.5;
}

.flex-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position:center;
}

.flex-container .text {
  flex: 1;
  margin-right: 20px;
}

.slideshow-container {
  position: relative;
  width: 300px;
  height: 300px; 
}

.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slide.active {
  opacity: 1;
}

main {
  padding-top: 100px;
}

.todays-specials {
  padding: 4rem 9%;
  background-color: #f9f9f9;
}

.specials-heading {
  text-align: center;
  font-size: 4rem;
  color: #333;
  margin-bottom: 3rem;
}

.offers-banner {
  background: linear-gradient(to right, #f3b46d, #ff5e00);
  color: white;
  padding: 2rem;
  text-align: center;
  margin: 2rem 0;
  border-radius: 10px;
}

.offers-banner h2 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
}

.offers-banner p {
  font-size: 2rem;
  margin-bottom: 1.5rem;
}

.offer-btn {
  background: white;
  color: #ff5e00;
  padding: 1rem 2.5rem;
  font-size: 1.8rem;
  border-radius: 50px;
  font-weight: bold;
  display: inline-block;
}

.offer-btn:hover {
  background: #000000;
  color: white;
}

main {padding-top: 170px;}

.join-btn {
    display: inline-block;
    margin-top: 1rem;
    border-radius: 5rem;
    background: #0d440a;
    color: #fff;
    padding: 0.9rem 3.5rem;
    cursor: pointer;
    font-size: 1.7rem;
}
.join-btn:hover {
    background-color:#208b26 ; 
}

input {
      background-color: #ffffff;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 100%;
    }

.grid-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
  font-size: 1.6rem;
  color: #228B22;
  margin-bottom: 15px;
}

.add-to-cart {
  background: #228B22;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.4rem;
  transition: all 0.3s;
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
    <section class="home" id="home">
      <div class="content">
          <h3>Fresh Fruits & Vegetables</h3>
          <span>Harvested Daily, Delivered Fresh</span>
          <p>FreshHarvest connects you directly with local farmers,
             bringing the freshest seasonal produce straight to your doorstep. 
              Our farm-to-table approach ensures you get the highest quality fruits and vegetables at their nutritional peak. Experience the difference that fresh, locally-sourced food can make in your meals and your health.</p>
          <a href="products.php" class="btn">Shop Now</a>
      </div>
    </section>

    <div class="offers-banner">
      <h2>SPECIAL OFFERS!</h2>
      <p>Get 20% off on your first order with code: FRESH20</p>
      <a href="products.php" class="offer-btn">Claim Offer</a>
    </div>

    <section class="todays-specials">
      <h2 class="specials-heading">Today's Fresh Picks</h2>
      <div class="grid-container">
        <?php
        // Sample products data
        $products = [
          ['id' => 1, 'name' => 'Organic Apples', 'price' => 1097.00, 'image' => 'images/apple.avif'],
          ['id' => 2, 'name' => 'Fresh Carrots', 'price' => 544.00, 'image' => 'images/carrots.avif'],
          ['id' => 3, 'name' => 'Vine Tomatoes', 'price' => 1204.00, 'image' => 'images/tomatoes.jpg'],
          ['id' => 4, 'name' => 'Organic Bananas', 'price' => 288.00, 'image' => 'images/bananas.avif']
        ];
        
        foreach ($products as $product): ?>
        <div class="grid-item">
          <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
          <div class="product-info">
            <h2><?php echo $product['name']; ?></h2>
            <p>LKR <?php echo number_format($product['price'], 2); ?>/kg</p>
            <button class="add-to-cart" 
                    onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, this)">
              Add to Cart
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>
    

    <section class="farmer" id="farmer">
      <div class="content">
        <div class="flex-container">
          <div class="text">
            <h3>Are you a farmer?</h3>
            <p>Are you a local farmer looking to reach more customers with your fresh produce?</br> 
              FreshHarvest provides the perfect platform to connect directly with consumers who value farm-fresh quality.
               Our easy-to-use website allows you to register as a seller and start listing your harvest in no time. Join 
               our community of local growers and let us help you get fair prices for your hard work while delivering the 
               freshest produce to our customers.
              </p>
          </div>
          <div class="slideshow-container">
            <img src="images/farmer1.jpg" class="slide" alt="Farmer in field">
            <img src="images/farmer2.jpg" class="slide" alt="Fresh harvest">
            <img src="images/farmer3.jpg" class="slide" alt="Farmers market">
            <img src="images/farmer4.jpg" class="slide" alt="Organic farming">
          </div>
        </div>
        <a href="account.php" class="join-btn">Join Our Farmers</a>
      </div>
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
  let currentIndex = 0;
  const slides = document.querySelectorAll('.slide');

  function showSlide(index) {
      slides.forEach((slide, i) => {
          slide.classList.remove('active');
          if (i === index) {
              slide.classList.add('active');
          }
      });
  }

  function nextSlide() {
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
  }

  showSlide(currentIndex);
  setInterval(nextSlide, 3000); 

  var loader = document.getElementById("preloader");
  window.addEventListener("load", function() {
        loader.style.display = "none";
  });

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
  </script>
</body>
</html>