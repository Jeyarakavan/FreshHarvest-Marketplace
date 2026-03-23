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

// Process payment form submission
$paymentMessage = '';
$paymentSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['payment_method'])) {
        // Process payment based on method
        $payment_method = $_POST['payment_method'];
        $order_total = 515.00;
        
        if ($payment_method == 'card') {
            // Process card payment
            $card_number = str_replace(' ', '', $_POST['card_number']);
            $card_name = $_POST['card_name'];
            $expiry_date = $_POST['expiry_date'];
            $cvv = $_POST['cvv'];
            
           
            if (strlen($card_number) != 16 || !is_numeric($card_number)) {
                $paymentMessage = "Invalid card number";
            } elseif (empty($card_name)) {
                $paymentMessage = "Cardholder name is required";
            } elseif (strlen($expiry_date) != 5 || !preg_match('/^\d{2}\/\d{2}$/', $expiry_date)) {
                $paymentMessage = "Invalid expiry date (use MM/YY format)";
            } elseif (strlen($cvv) != 3 || !is_numeric($cvv)) {
                $paymentMessage = "Invalid CVV";
            } else {
                // Save payment to database
                $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount, card_last4, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $order_id = 'FH' . date('YmdHis');
                $card_last4 = substr($card_number, -4);
                $status = 'completed';
                
                $stmt->bind_param("ssdss", $order_id, $payment_method, $order_total, $card_last4, $status);
                
                if ($stmt->execute()) {
                    $paymentSuccess = true;
                    $paymentMessage = "Payment successful! Order #$order_id";
                } else {
                    $paymentMessage = "Payment processing failed: " . $conn->error;
                }
                $stmt->close();
            }
        } elseif ($payment_method == 'cod') {
            // Process cash on delivery
            $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $order_id = 'FH' . date('YmdHis');
            $status = 'pending';
            
            $stmt->bind_param("ssds", $order_id, $payment_method, $order_total, $status);
            
            if ($stmt->execute()) {
                $paymentSuccess = true;
                $paymentMessage = "Order confirmed! Order #$order_id (Cash on Delivery)";
            } else {
                $paymentMessage = "Order processing failed: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreshHarvest - Secure Payment</title>
    <link rel="stylesheet" href="css/style1.css">
    <script src="js/script.js"></script>
    <style>
        
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .payment-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-header img {
            height: 50px;
            margin-bottom: 15px;
        }

        .payment-header h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .payment-header p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .payment-grid {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .order-summary {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .payment-methods {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .item-name {
            color: #333;
        }

        .item-price {
            font-weight: 500;
        }

        .order-total {
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .grand-total {
            font-weight: 600;
            font-size: 18px;
            color: #2c3e50;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .delivery-address {
            margin-top: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .address-title {
            font-weight: 500;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        /* Payment Method Styles */
        .payment-option {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-option:hover {
            border-color: #3498db;
        }

        .payment-option.active {
            border-color: #3498db;
            background-color: #f8fafd;
        }

        .payment-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .payment-icon {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }

        .payment-title {
            font-weight: 500;
            font-size: 16px;
            color: #2c3e50;
        }

        .payment-description {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Card Form Styles */
        .card-form {
            margin-top: 15px;
            display: none;
        }

        .card-form.active {
            display: block;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
        }

        .card-row {
            display: flex;
            gap: 15px;
        }

        .card-row .form-group {
            flex: 1;
        }

        /* Card Preview */
        .card-preview {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            height: 160px;
            font-family: 'Courier New', monospace;
        }

        .card-type {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            filter: brightness(0) invert(1);
        }

        .card-number {
            font-size: 18px;
            letter-spacing: 1px;
            margin-top: 40px;
            margin-bottom: 20px;
            font-family: 'Courier New', monospace;
        }

        .card-details {
            display: flex;
            justify-content: space-between;
        }

        .card-name, .card-expiry {
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        /* Security Badges */
        .security-badges {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .security-badge {
            height: 30px;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .payment-grid {
                flex-direction: column;
            }
            
            .card-row {
                flex-direction: column;
                gap: 0;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .modal-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 22px;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .modal-message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #666;
            line-height: 1.5;
        }

        .modal-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        .modal-btn:hover {
            background-color: #2980b9;
        }
        input {
        background-color: #ffffff;
        border: none;
        padding: 12px 15px;
        margin: 8px 0;
        width: 100%;
        }
        main {padding-top:100px;}

        /* Message styles */
        .payment-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .payment-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .payment-error {
            background-color: #f8d7da;
            color: #721c24;
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
        <div class="payment-container">
            <div class="payment-header">
                <img src="images/secure-lock.png" alt="Secure Payment">
                <h1>Secure Checkout</h1>
                <p>Complete your purchase securely with FreshHarvest</p>
            </div>
            
            <?php if (!empty($paymentMessage)): ?>
                <div class="payment-message <?php echo $paymentSuccess ? 'payment-success' : 'payment-error'; ?>">
                    <?php echo $paymentMessage; ?>
                </div>
            <?php endif; ?>
            
            <div class="payment-grid">
                <div class="order-summary">
                    <h2 class="section-title">Your Order</h2>
                    
                    <div class="order-items">
                        <div class="order-item">
                            <span class="item-name">Organic Apples (2kg)</span>
                            <span class="item-price">240.00 LKR</span>
                        </div>
                        <div class="order-item">
                            <span class="item-name">Fresh Carrots (3kg)</span>
                            <span class="item-price">180.00 LKR</span>
                        </div>
                        <div class="order-item">
                            <span class="item-name">Ripe Bananas (1 dozen)</span>
                            <span class="item-price">45.00 LKR</span>
                        </div>
                    </div>
                    
                    <div class="order-total">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span>465.00 LKR</span>
                        </div>
                        <div class="total-row">
                            <span>Delivery Fee:</span>
                            <span>50.00 LKR</span>
                        </div>
                        <div class="total-row">
                            <span>Tax:</span>
                            <span>0.00 LKR</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total:</span>
                            <span>515.00 LKR</span>
                        </div>
                    </div>
                    
                    <div class="delivery-address">
                        <h3 class="address-title">Delivery Information</h3>
                        <div class="address-details">
                            <p><strong>Rakavan S.</strong></p>
                            <p>123 Flower Road</p>
                            <p>Colombo 07, Sri Lanka</p>
                            <p>+94 76 123 4567</p>
                        </div>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <h2 class="section-title">Payment Method</h2>
                    
                    <div class="payment-option active" id="cardOption">
                        <div class="payment-header">
                            <img src="images/credit-card-icon.png" alt="Credit Card" class="payment-icon">
                            <h3 class="payment-title">Credit/Debit Card</h3>
                        </div>
                        <p class="payment-description">Secure payment with Visa, Mastercard, or other cards</p>
                        
                        <div class="card-form active" id="cardForm">
                            <div class="card-preview">
                                <img src="images/mastercard.png" alt="Card Type" class="card-type">
                                <div class="card-number">•••• •••• •••• ••••</div>
                                <div class="card-details">
                                    <div class="card-name">CARDHOLDER NAME</div>
                                    <div class="card-expiry">MM/YY</div>
                                </div>
                            </div>
                            
                            <form id="paymentForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <input type="hidden" name="payment_method" value="card">
                                
                                <div class="form-group">
                                    <label for="cardNumber" class="form-label">Card Number</label>
                                    <input type="text" id="cardNumber" name="card_number" class="form-input" placeholder="1234 5678 9012 3456" maxlength="19" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cardName" class="form-label">Cardholder Name</label>
                                    <input type="text" id="cardName" name="card_name" class="form-input" placeholder="As shown on card" required>
                                </div>
                                
                                <div class="card-row">
                                    <div class="form-group">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" id="expiryDate" name="expiry_date" class="form-input" placeholder="MM/YY" maxlength="5" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cvv" class="form-label">Security Code</label>
                                        <input type="text" id="cvv" name="cvv" class="form-input" placeholder="CVV" maxlength="3" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Pay 515.00 LKR</button>
                            </form>
                            
                            <div class="security-badges">
                                <img src="images/ssl-secure.png" alt="SSL Secure" class="security-badge">
                                <img src="images/pci-compliant.png" alt="PCI Compliant" class="security-badge">
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-option" id="cashOption">
                        <div class="payment-header">
                            <img src="images/cash-icon.png" alt="Cash on Delivery" class="payment-icon">
                            <h3 class="payment-title">Cash on Delivery</h3>
                        </div>
                        <p class="payment-description">Pay with cash when your order arrives</p>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="payment_method" value="cod">
                            <button type="submit" class="btn btn-secondary" id="cashOnDeliveryBtn">Select Cash on Delivery</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Success Modal -->
        <div class="modal" id="successModal">
            <div class="modal-content">
                <img src="images/success-icon.png" alt="Success" class="modal-icon">
                <h3 class="modal-title">Payment Successful</h3>
                <p class="modal-message">Your order #FH20230001 has been confirmed. We've sent a receipt to your email.</p>
                <button class="modal-btn" id="successBtn">View Order Details</button>
            </div>
        </div>
        
        <!-- COD Confirmation Modal -->
        <div class="modal" id="codModal">
            <div class="modal-content">
                <img src="images/confirm-icon.png" alt="Confirm" class="modal-icon">
                <h3 class="modal-title">Order Confirmed</h3>
                <p class="modal-message">Your order #FH20230001 will be delivered within 2 business days. Please have 515.00 LKR ready for payment upon delivery.</p>
                <button class="modal-btn" id="codConfirmBtn">Continue Shopping</button>
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
        // Payment Method Selection
        document.getElementById('cardOption').addEventListener('click', function() {
            document.getElementById('cardOption').classList.add('active');
            document.getElementById('cashOption').classList.remove('active');
            document.getElementById('cardForm').style.display = 'block';
        });

        document.getElementById('cashOption').addEventListener('click', function() {
            document.getElementById('cashOption').classList.add('active');
            document.getElementById('cardOption').classList.remove('active');
            document.getElementById('cardForm').style.display = 'none';
        });

        // Card Number Formatting
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formatted = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = formatted;
            
            // Update card preview
            let preview = '•••• •••• •••• ••••';
            if (value.length > 0) {
                let visible = value.substring(0, 16);
                let masked = '•••• •••• •••• '.substring(0, 14 - Math.min(visible.length, 12));
                preview = visible + masked;
                preview = preview.replace(/(\d{4})/g, '$1 ').trim();
            }
            document.querySelector('.card-number').textContent = preview || '•••• •••• •••• ••••';
        });

        // Card Name Formatting
        document.getElementById('cardName').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            e.target.value = value;
            document.querySelector('.card-name').textContent = value || 'CARDHOLDER NAME';
        });

        // Expiry Date Formatting
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
            document.querySelector('.card-expiry').textContent = value || 'MM/YY';
        });

        // CVV Formatting
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 3);
        });

        // Form Submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            // Validate form
            let cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
            let cardName = document.getElementById('cardName').value;
            let expiryDate = document.getElementById('expiryDate').value;
            let cvv = document.getElementById('cvv').value;
            
            if (cardNumber.length !== 16 || isNaN(cardNumber)) {
                alert('Please enter a valid 16-digit card number');
                e.preventDefault();
                return;
            }
            
            if (!cardName) {
                alert('Please enter cardholder name');
                e.preventDefault();
                return;
            }
            
            if (!expiryDate || expiryDate.length !== 5 || !expiryDate.includes('/')) {
                alert('Please enter a valid expiry date (MM/YY)');
                e.preventDefault();
                return;
            }
            
            if (!cvv || cvv.length !== 3 || isNaN(cvv)) {
                alert('Please enter a valid 3-digit CVV');
                e.preventDefault();
                return;
            }
            
            // Show success modal
            document.getElementById('successModal').style.display = 'flex';
        });

        // Modal Buttons
        document.getElementById('successBtn').addEventListener('click', function() {
            document.getElementById('successModal').style.display = 'none';
            // Redirect to order confirmation page
            window.location.href = 'order_confirmation.php';
        });

        document.getElementById('codConfirmBtn').addEventListener('click', function() {
            document.getElementById('codModal').style.display = 'none';
            // Redirect to order confirmation page
            window.location.href = 'order_confirmation.php';
        });

        // Newsletter Subscription
        document.getElementById('newsletterBtn').addEventListener('click', function() {
            const emailInput = this.parentElement.querySelector('input[type="email"]');
            if(emailInput.value && emailInput.value.includes('@')) {
                alert('Thank you for subscribing to our newsletter!');
            } else {
                alert('Please enter a valid email address');
            }
        });

        // Show success modal if payment was successful
        <?php if ($paymentSuccess): ?>
            window.onload = function() {
                document.getElementById('<?php echo $_POST['payment_method'] == 'card' ? 'successModal' : 'codModal'; ?>').style.display = 'flex';
            };
        <?php endif; ?>
    </script>
</body>
</html>