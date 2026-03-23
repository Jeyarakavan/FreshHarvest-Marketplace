<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['txtEmail']) && isset($_POST['txtPassword'])) {
    $email = trim($_POST['txtEmail']);
    $password = trim($_POST['txtPassword']);

    try {
        $stmt = $pdo->prepare("SELECT id, email, password, full_name, is_admin FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Debug output - remove in production
            error_log("Login attempt for: ".$email);
            error_log("Stored hash: ".$user['password']);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['is_admin'] = (int)$user['is_admin'];
                
                error_log("Login successful. Admin status: ".$_SESSION['is_admin']);
                
                if ($_SESSION['is_admin'] === 1) {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                error_log("Password verification failed for: ".$email);
            }
        }
        
        header("Location: account.php?error=invalid_credentials");
        exit();
        
    } catch (PDOException $e) {
        error_log("Database error during login: ".$e->getMessage());
        header("Location: account.php?error=database_error");
        exit();
    }
}
?>
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshHarvest - Account</title>
  <link rel="stylesheet" href="css/style1.css">
  <script src="js/script.js"></script>
  <style>
    @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

    main {padding-top: 400px;}

    body {
      background: #f6f5f7;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      font-family: 'Montserrat', sans-serif;
      height: 100vh;
      margin: -20px 0 50px;
      padding-top: 40px;
      align-items: center;
    }

    h1 {
      font-weight: bold;
      margin: 0;
    }

    h2 {
      text-align: center;
    }

    p {
      font-size: 14px;
      font-weight: 100;
      line-height: 20px;
      letter-spacing: 0.5px;
      margin: 20px 0 30px;
    }

    button {
      border-radius: 20px;
      border: 1px solid #228B22;
      background-color: #187318;
      color: #FFFFFF;
      font-size: 12px;
      font-weight: bold;
      padding: 12px 45px;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: transform 80ms ease-in;
      cursor: pointer;
    }
    button:hover {background-color: #3bab3b;}

    button:active {
      transform: scale(0.95);
    }

    button:focus {
      outline: none;
    }

    button.ghost {
      background-color: transparent;
      border-color: #efeaea;
    }

    form {
      background-color: #FFFFFF;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 50px;
      height: 100%;
      text-align: center;
    }

    input {
      background-color: #eee;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 100%;
    }

    .container {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
                  0 10px 10px rgba(0,0,0,0.22);
      position: relative;
      overflow: hidden;
      width: 768px;
      max-width: 100%;
      min-height: 480px;
      margin-top: 80px;
    }

    .form-container {
      position: absolute;
      top: 0;
      height: 100%;
      transition: all 0.6s ease-in-out;
    }

    .sign-in-container {
      left: 0;
      width: 50%;
      z-index: 2;
    }

    .container.right-panel-active .sign-in-container {
      transform: translateX(100%);
    }

    .sign-up-container {
      left: 0;
      width: 50%;
      opacity: 0;
      z-index: 1;
    }

    .container.right-panel-active .sign-up-container {
      transform: translateX(100%);
      opacity: 1;
      z-index: 5;
      animation: show 0.6s;
    }

    @keyframes show {
      0%, 49.99% {
        opacity: 0;
        z-index: 1;
      }
      
      50%, 100% {
        opacity: 1;
        z-index: 5;
      }
    }

    .overlay-container {
      position: absolute;
      top: 0;
      left: 50%;
      width: 50%;
      height: 100%;
      overflow: hidden;
      transition: transform 0.6s ease-in-out;
      z-index: 100;
    }

    .container.right-panel-active .overlay-container{
      transform: translateX(-100%);
    }

    .overlay {
      background: #FF416C;
      background: -webkit-linear-gradient(to right, #023020, #50C878);
      background: linear-gradient(to right, #084b33, #43d373);
      background-repeat: no-repeat;
      background-size: cover;
      background-position: 0 0;
      color: #FFFFFF;
      position: relative;
      left: -100%;
      height: 100%;
      width: 200%;
      transform: translateX(0);
      transition: transform 0.6s ease-in-out;
    }

    .container.right-panel-active .overlay {
      transform: translateX(50%);
    }

    .overlay-panel {
      position: absolute;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 0 40px;
      text-align: center;
      top: 0;
      height: 100%;
      width: 50%;
      transform: translateX(0);
      transition: transform 0.6s ease-in-out;
    }

    .overlay-left {
      transform: translateX(-20%);
    }

    .container.right-panel-active .overlay-left {
      transform: translateX(0);
    }

    .overlay-right {
      right: 0;
      transform: translateX(0);
    }

    .container.right-panel-active .overlay-right {
      transform: translateX(20%);
    }

    .social-container {
      margin: 20px 0;
    }

    .social-container a {
      border: 1px solid #DDDDDD;
      border-radius: 50%;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      margin: 0 5px;
      height: 40px;
      width: 40px;
    }

    .error-message {
      color: #ff0000;
      margin-bottom: 15px;
      font-size: 14px;
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
    <li><a href="products.php">Produce</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <?php if(isset($_SESSION['user_email'])): ?>
      <li><a href="Accounts.php"><?php echo htmlspecialchars($_SESSION['user_name']); ?></a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="account.php"><img src="images/account.png" alt="Account" class="nav-image"></a></li>
    <?php endif; ?>
    <li><a href="cart.php"><img src="images/cart.png" alt="Shopping Cart" class="nav-image"></a></li>
  </ul>
</nav>
  </header>

  <main>
  <div class="container" id="container">
    <div class="form-container sign-up-container">
      <form action="register.php" method="POST" onsubmit="return checkPassword()">
        <h1>Join FreshHarvest</h1>
        <?php if(isset($_GET['error'])): ?>
          <div class="error-message">
            <?php 
              if($_GET['error'] == 'email_exists') echo 'Email already exists!';
              if($_GET['error'] == 'password_mismatch') echo 'Passwords do not match!';
            ?>
          </div>
        <?php endif; ?>
        <input type="text" placeholder="Full Name" name="txtName" id="txtName" required />
        <input type="email" placeholder="Email" required name="txtEmail" id="txtEmail"/>
        <input type="password" placeholder="Password" id="txtPassword" name="txtPassword" required/>
        <input type="password" placeholder="Confirm Password" id="txtConfimPassword" name="txtConfimPassword" required/>
        <input type="text" placeholder="Delivery Address" name="txtAddress" id="txtAddress" required />
        <button type="submit">Register</button>
        <a href="index.php">Back to FreshHarvest</a>
      </form>
    </div>
    <div class="form-container sign-in-container">
      <form action="account.php" method="POST">
        <h1>Welcome Back</h1>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'invalid_credentials'): ?>
          <div class="error-message">
            Invalid email or password!
          </div>
        <?php endif; ?>
        <input type="email" name="txtEmail" placeholder="Email" required />
        <input type="password" name="txtPassword" placeholder="Password" required />
        <a href="#">Forgot your password?</a>
        <button type="submit">Log In</button>
        <a href="index.php">Back to FreshHarvest</a>
      </form>
    </div>
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>Welcome Back!</h1>
          <p>Access your account to shop fresh produce or manage your seller dashboard</p>
          <button class="ghost" id="signIn">Sign In</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>Fresh Produce Awaits!</h1>
          <p>Create an account to buy farm-fresh vegetables and fruits or sell your harvest</p>
          <button class="ghost" id="signUp">Join Now</button>
        </div>
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
    // Password validation function
    function checkPassword() {
      let pw = document.getElementById("txtPassword").value;
      let cpw = document.getElementById("txtConfimPassword").value;
      if(pw != cpw) {
        alert("Password and confirm password should match");
        return false;
      }
      return true;
    }

    // Form switching functionality
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
      container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
      container.classList.remove("right-panel-active");
    });

    // Newsletter subscription functionality
    document.getElementById('newsletterBtn').addEventListener('click', function() {
      const emailInput = this.parentElement.querySelector('input[type="email"]');
      if(emailInput.value && emailInput.value.includes('@')) {
        alert('Successfully Subscribed');
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