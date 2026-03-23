-- FreshHarvest Database Schema
-- Create the database
CREATE DATABASE IF NOT EXISTS freshharvest_db;
USE freshharvest_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    is_organic TINYINT(1) DEFAULT 0,
    unit VARCHAR(50) DEFAULT 'kg',
    farmer_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    card_last4 VARCHAR(4),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subscribers table
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    subscription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (full_name, email, password, is_admin) VALUES
('Administrator', 'admin@freshharvest.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample products
INSERT INTO products (name, description, price, image, category, is_organic, unit, farmer_id) VALUES
('Fresh Apples', 'Crisp, juicy red apples harvested from local orchards', 3.99, 'apple.avif', 'Fruits', 1, 'kg', 1),
('Organic Bananas', 'Sweet and ripe bananas, perfect for snacking', 2.49, 'bananas.avif', 'Fruits', 1, 'kg', 1),
('Baby Spinach', 'Tender baby spinach leaves, rich in nutrients', 4.99, 'Baby Spinach.avif', 'Vegetables', 1, 'bunch', 1),
('Carrots', 'Fresh orange carrots, crunchy and sweet', 1.99, 'carrots.avif', 'Vegetables', 0, 'kg', 1),
('Oranges', 'Juicy navel oranges, packed with vitamin C', 3.49, 'Oranges.avif', 'Fruits', 0, 'kg', 1),
('Potatoes', 'Versatile russet potatoes, great for baking or frying', 2.99, 'Potatoes.avif', 'Vegetables', 0, 'kg', 1),
('Strawberries', 'Sweet summer strawberries, perfect for desserts', 5.99, 'Strawberries.avif', 'Fruits', 1, 'pint', 1)
ON DUPLICATE KEY UPDATE name=name;