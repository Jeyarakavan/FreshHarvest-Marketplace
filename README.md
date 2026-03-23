# FreshHarvest E-commerce Website

## Welcome to FreshHarvest!

FreshHarvest is a heartwarming platform that brings farmers and food lovers together. Imagine this: hardworking farmers growing the freshest vegetables, fruits, and produce right from their fields, and you - the customer - getting to buy directly from them. No middlemen, no long supply chains, just farm-fresh goodness delivered straight to your doorstep!

This PHP-based marketplace creates a direct connection between local farmers and consumers, making it easier than ever to support local agriculture while enjoying the best seasonal produce nature has to offer.

## What Makes FreshHarvest Special

- **Direct Farm-to-Table Connection**: Skip the supermarkets and buy straight from farmers who grow your food
- **Freshness Guaranteed**: Seasonal produce at its nutritional peak, harvested when it's ready
- **Support Local Farmers**: Help local agriculture thrive by purchasing directly from those who work the land
- **Easy Shopping Experience**: Browse, select, and order fresh produce with just a few clicks
- **Farmer Empowerment**: Farmers can easily list their products and manage their online storefront
- **Community Building**: Join a community that values fresh, local, and sustainable food

## Key Features

### For Customers
- **Browse Fresh Produce**: Explore a variety of seasonal fruits and vegetables
- **Search & Filter**: Find exactly what you're looking for with smart search and category filters
- **Shopping Cart**: Add items, adjust quantities, and manage your selections
- **Secure Checkout**: Safe and simple payment processing
- **Order Tracking**: Keep tabs on your fresh deliveries

### For Farmers
- **Product Management**: Easily add, update, and manage your produce listings
- **Personal Dashboard**: Track your products, sales, and customer interactions
- **Direct Sales**: Sell directly to consumers without platform fees or intermediaries
- **Community Connection**: Build relationships with customers who appreciate your hard work

### For Administrators
- **User Management**: Oversee farmer and customer accounts
- **Product Oversight**: Monitor and manage product listings
- **Platform Control**: Maintain the marketplace for everyone

## Technology Stack

- **Backend**: PHP 7+ with secure session management
- **Database**: MySQL for reliable data storage
- **Frontend**: HTML5, CSS3, and JavaScript for a smooth user experience
- **Server**: Apache (comes with XAMPP)
- **Security**: Password hashing, input validation, and SQL injection prevention

## Quick Start Guide

### Prerequisites
- XAMPP (includes Apache, MySQL, and PHP)
- A modern web browser

### Installation Steps

1. **Get the Files Ready**
   - Download or clone this project
   - Place the entire folder in `C:\xampp\htdocs\`

2. **Fire Up XAMPP**
   - Open XAMPP Control Panel
   - Start both Apache and MySQL services

3. **Set Up the Database**
   - Open your browser and go to `http://localhost/phpmyadmin`
   - Create a new database called `freshharvest_db`
   - Click on the new database, then go to the "Import" tab
   - Choose the `schema.sql` file from your project folder
   - Click "Go" to import the database structure and sample data

4. **Access Your Fresh Marketplace**
   - Open `http://localhost/FreshHarvest_E-commerce_Website/` in your browser
   - The platform is ready to use!

### Default Admin Access
- **Email**: admin@freshharvest.com
- **Password**: admin123
- (You can change this after logging in)

## Project Structure

```
FreshHarvest_E-commerce_Website/
├── index.php              # The welcoming homepage
├── products.php           # Browse all available produce
├── cart.php               # Your shopping cart
├── payment.php            # Secure checkout process
├── account.php            # User account management
├── Accounts.php           # Personal dashboard
├── login.php              # Sign in to your account
├── register.php           # Join the FreshHarvest community
├── add_product.php        # Farmers add new products
├── edit_product.php       # Update product listings
├── admin_dashboard.php    # Admin control center
├── about.php              # Learn about our mission
├── contact.php            # Get in touch
├── db.php                 # Database connection
├── reset_admin.php        # Admin password reset utility
├── css/
│   └── style1.css         # Beautiful, responsive styling
├── js/
│   └── script.js          # Interactive features
└── images/                # Fresh produce photos
```

## Security & Best Practices

- **Password Protection**: All passwords are securely hashed
- **Session Security**: Safe user sessions with proper validation
- **Data Sanitization**: Input validation to prevent security issues
- **SQL Injection Prevention**: Prepared statements keep your data safe
- **File Upload Security**: Safe image uploads for product photos

## Sample Data Included

The database comes pre-loaded with:
- An admin account to get you started
- Sample farmers and their fresh produce
- Example products like apples, bananas, spinach, carrots, and more
- Ready-to-use categories and pricing

## Contributing to FreshHarvest

We'd love your help to make this platform even better! Here's how you can contribute:

1. **Fork** this repository
2. **Create** a feature branch for your improvements
3. **Make** your changes with lots of testing
4. **Submit** a pull request with a clear description

## License & Usage

This project was created for educational purposes. If you're planning to use it commercially, please ensure you comply with all relevant local laws and regulations.

---

**Ready to support local farmers and enjoy the freshest produce? Let's get FreshHarvest running on your system!**