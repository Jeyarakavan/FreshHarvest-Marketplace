<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freshharvest_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process newsletter subscription form
$subscriptionMessage = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $subscriptionMessage = "Invalid email format";
    } else {
        // Check if email already exists
        $checkSql = "SELECT * FROM subscribers WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $subscriptionMessage = "This email is already subscribed";
        } else {
            // Insert new subscriber
            $insertSql = "INSERT INTO subscribers (email, subscription_date) VALUES (?, NOW())";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $subscriptionMessage = "Thank you for subscribing!";
            } else {
                $subscriptionMessage = "Error: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshHarvest - About Us</title>
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/script.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            outline: none;
            border: none;
            text-decoration: none;
            transition: 0.2s linear;
        }

        html {
            font-size: 62.5%;
            scroll-behavior: smooth;
            scroll-padding-top: 6rem;
            overflow-x: hidden;
        }

        body {
            background-color: #f9f9f9;
            color: #333;
        }

        .about-section {
            padding: 5rem 9%;
            background-color: white;
        }

        .about-container {
            display: flex;
            align-items: center;
            gap: 4rem;
        }

        .about-image {
            flex: 1;
        }

        .about-image img {
            width: 100%;
            border-radius: 1rem;
            box-shadow: 5px 1px rgba(188, 187, 187, 0.1);
        }

        .about-content {
            flex: 1;
        }

        .about-content h1 {
            font-size: 3.5rem;
            color: #265a0d;
            margin-bottom: 2rem;
        }

        .about-content h2 {
            font-size: 2.5rem;
            color: #122b07;
            margin: 1.5rem 0;
        }

        .about-content p {
            font-size: 1.6rem;
            line-height: 1.6;
            color: #7e7a7a;
            margin-bottom: 1.5rem;
        }

        .mission-vision {
            display: flex;
            gap: 3rem;
            margin: 3rem 0;
        }

        .mission, .vision {
            flex: 1;
            padding: 2rem;
            background-color: #b2e8c8;
            border-radius: 1rem;
        }

        .mission h3, .vision h3 {
            font-size: 2rem;
            color: #1c4a09;
            margin-bottom: 1rem;
        }

        .btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 1rem 3rem;
            background: #2f7111;
            color: white;
            font-size: 1.6rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .btn:hover {
            background: #4e992b;
        }

        @media (max-width: 1000px) {
            .about-container {
                flex-direction: column;
            }
        }

        @media (max-width: 1000px) {
            .mission-vision {
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

        main {
            padding-top: 120px;
        }

        .subscription-message {
            color: #2f7111;
            font-size: 1.4rem;
            margin-top: 5px;
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
    <section class="about-section">
        <div class="about-container">

            <div class="about-image">
                <img src="images/farmers-market.jpg" alt="Fresh vegetables at our market">
            </div>
          
            <div class="about-content">
                <h1>About FreshHarvest</h1>
                <p>Welcome to FreshHarvest, your trusted source for the freshest, locally-sourced vegetables delivered straight from farm to table. Founded in 2010, we've been connecting health-conscious consumers with the finest seasonal produce for over a decade.</p>
                
                <div class="mission-vision">
                    <div class="mission">
                        <h3>Our Mission</h3>
                        <p>To provide fresh, nutritious, and sustainably-grown vegetables while supporting local farmers and promoting healthy eating habits in our community.</p>
                    </div>
                    <div class="vision">
                        <h3>Our Vision</h3>
                        <p>To revolutionize the way people access fresh produce by creating a transparent, farm-to-consumer supply chain that benefits both farmers and customers.</p>
                    </div>
                </div>

                <h2>Why Choose FreshHarvest?</h2>
                <p>We work directly with local farmers to bring you the freshest seasonal vegetables. Our produce is harvested at peak ripeness and delivered to you within 24 hours, ensuring maximum freshness and nutritional value.</p>
                <p>All our vegetables are grown using sustainable farming practices, with no harmful pesticides or chemicals. We're committed to environmental responsibility while delivering the highest quality products.</p>
                
                <a href="contact.php" class="btn">Contact Us</a>
            </div>
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
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="email" name="email" placeholder="Your email id here" required>
            <button type="submit" id="newsletterBtn">Subscribe</button>
            <?php if (!empty($subscriptionMessage)): ?>
                <div class="subscription-message"><?php echo $subscriptionMessage; ?></div>
            <?php endif; ?>
          </form>
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
        <p>Copyright &copy; <?php echo date("Y"); ?> FreshHarvest</p>
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
  </script>
</body>
</html>
<?php
$conn->close();
?>