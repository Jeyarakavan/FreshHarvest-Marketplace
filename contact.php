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

// Process contact form submission
$formMessage = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Validate inputs
    $errors = [];
    if (empty($firstname)) $errors[] = "First name is required";
    if (empty($lastname)) $errors[] = "Last name is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";
    
    if (empty($errors)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO contact_messages (firstname, lastname, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $firstname, $lastname, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $formMessage = "<div class='success-message'>Thank you for your message! We'll get back to you soon.</div>";
            
            // Send email notification (optional)
            $to = "FreshHarvest@gmail.com";
            $emailSubject = "New Contact Form Submission: $subject";
            $emailBody = "You have received a new message from $firstname $lastname ($email).\n\nSubject: $subject\n\nMessage:\n$message";
            $headers = "From: $email";
            
            mail($to, $emailSubject, $emailBody, $headers);
        } else {
            $formMessage = "<div class='error-message'>Error: " . $conn->error . "</div>";
        }
        $stmt->close();
    } else {
        $formMessage = "<div class='error-message'>" . implode("<br>", $errors) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshHarvest - Contact Us</title>
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

        body {
            background-color: #f9f9f9;
            color: #333;
            
        }
  
    /* Main Content Styles */
    main {
      padding-top: 200px;
    }

    .contact-container {
      display: flex;
      max-width: 1200px;
      margin: 0 auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .contact-form {
      flex: 1;
      padding: 40px;
      background-color: #e1e1de;
    }

    .contact-form h2 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: #000000;
    }

    .contact-form p {
      margin-bottom: 30px;
      color: #000000;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #000000;
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      transition: border-color 0.3s;
    }

    .form-control:focus {
      border-color: #36dfa1;
      outline: none;
    }

    textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }

    .submit-btn {
      background: linear-gradient(to right, #36dfa1, #90EE90);
      color: rgb(0, 0, 0);
      border: black;
      padding: 12px 30px;
      font-size: 16px;
      font-weight: 600;
      border-radius: 25px;
      cursor: pointer;
      transition: all 0.3s;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(30, 131, 94, 0.3);
    }

    /* Contact Info Styles */
    .contact-info {
      flex: 1;
      padding: 40px;
      background: #f5f5f5;
      position: relative;
    }

    .contact-info h3 {
      font-size: 1.8rem;
      margin-bottom: 20px;
      color: #333;
    }

    .info-item {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
    }

    .info-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(to right, #36dfa1, #90EE90);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: white;
      font-size: 18px;
    }

    .info-text h4 {
      font-size: 1.2rem;
      margin-bottom: 5px;
      color: #444;
    }

    .info-text p {
      color: #666;
      font-size: 1rem;
    }

    /* Map Styles */
    .map-container {
      margin-top: 30px;
      height: 300px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .map-container iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    .social-icons a:hover {
      transform: translateY(-3px);
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
      .contact-container {
        flex-direction: column;
      } 
    }

    main {
        padding-top: 200px;
    }

    h1 {
      font-size: 25px;
      color: #123104;
      margin-bottom: 20px;
    }

    p {
      font-size: 10px;
    }

    input {
      background-color: #ffffff;
      border: none;
      padding: 12px 15px;
      margin: 8px 0;
      width: 100%;
    }
    
    /* Message styles */
    .success-message {
      color: #2f7111;
      background-color: #d4edda;
      padding: 10px;
      border-radius: 5px;
      margin-top: 15px;
    }
    
    .error-message {
      color: #721c24;
      background-color: #f8d7da;
      padding: 10px;
      border-radius: 5px;
      margin-top: 15px;
    }

  </style>


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
    <div class="contact-container">
      <div class="contact-form">
        <h1>Get In Touch</h1>
        <p>Have questions or feedback? Fill out the form below and we'll get back to you as soon as possible.</p>
        
        <?php if (!empty($formMessage)) echo $formMessage; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
          <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="firstname" class="form-control" placeholder="Your first name" required>
          </div>
          
          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lastname" class="form-control" placeholder="Your last name" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Your email address" required>
          </div>
          
          <div class="form-group">
            <label for="subject">Subject</label>
            <select id="subject" name="subject" class="form-control" required>
              <option value="" disabled selected>Select a subject</option>
              <option value="general">General Inquiry</option>
              <option value="order">Order Status</option>
              <option value="product">Product Questions</option>
              <option value="returns">Returns & Exchanges</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="message">Your Message</label>
            <textarea id="message" name="message" class="form-control" placeholder="Write your message here..." required></textarea>
          </div>
          
          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>
      
      <div class="contact-info">
        <h1>Contact Information</h1>
        
        <div class="info-item">
          <div class="info-icon">
            <img src="images/Our Location.png" alt="Location" width="20">
          </div>
          <div class="info-text">
            <h4>Our Location</h4>
            <p>No. 32, KKS Road, Kopay, Jaffna, Sri Lanka</p>
          </div>
        </div>
        
        <div class="info-item">
          <div class="info-icon">
            <img src="images/Email.png" alt="Email" width="20">
          </div>
          <div class="info-text">
            <h4>Email Us</h4>
            <p>FreshHarvest@gmail.com</p>
          </div>
        </div>
        
        <div class="info-item">
          <div class="info-icon">
            <img src="images/Call.png" alt="Phone" width="20">
          </div>
          <div class="info-text">
            <h4>Call Us</h4>
            <p>+94 740045835</p>
          </div>
        </div>
        
        <div class="info-item">
          <div class="info-icon">
            <img src="images/Our Location.png" alt="Hours" width="20">
          </div>
          <div class="info-text">
            <h4>Working Hours</h4>
            <p>Mon to Sat: 9:00 AM to 4:00 PM</p>
          </div>
        </div>
        
        <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126743.58594348682!2d80.01541780663097!3d9.661433471063273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25630082c40f5%3A0x7b9d34c5e890238d!2sSLIIT%20Northern%20Campus%2C%20Jaffna!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
        </div>
        
        <div class="social-media">
          <h1>Follow Us</h1>
          <div class="social-icons">
            <a href="#"><img src="images/faceboook.png" alt="Facebook" width="20"></a>
            <a href="#"><img src="images/instagram.png" alt="Instagram" width="20"></a>
            <a href="#"><img src="images/twitter.png" alt="Twitter" width="20"></a>
            <a href="#"><img src="images/linkedin.png" alt="LinkedIn" width="20"></a>
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
        <form action="subscribe.php" method="post">
          <input type="email" name="email" placeholder="Your email id here" required>
          <button type="submit" id="newsletterBtn">Subscribe</button>
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
        alert('Successfully Subscribe');
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