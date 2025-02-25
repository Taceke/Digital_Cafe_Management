CREATE DATABASE IF NOT EXISTS cafeteria_pos;
USE cafeteria_pos;

CREATE DATABASE cafeteria_pos;

USE cafeteria_pos;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL
);

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$EXAMPLEHASH1234567890abcdef', 'admin'),
('cashier1', '$2y$10$EXAMPLEHASH0987654321abcdef', 'cashier');

-- Table for storing sales reports
CREATE TABLE IF NOT EXISTS sales_report (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer VARCHAR(255) NOT NULL DEFAULT 'None',
    qty INT NOT NULL,
    order_date DATETIME NOT NULL,
    salesperson VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_tax DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL
);

-- Insert sample data
INSERT INTO sales_report (customer, qty, order_date, salesperson, payment_method, discount, total_discount, total_tax, total_amount)
VALUES 
('None', 2, '2024-11-05 18:21:00', 'Admin', 'Cash', 0.00, 0.00, 0.00, 35.00),
('None', 2, '2024-10-27 10:58:00', 'Admin', 'Cash', 0.00, 0.00, 0.00, 110.00),
('None', 2, '2024-10-27 10:57:00', 'Admin', 'Cash', 0.00, 0.00, 0.00, 90.00),
('None', 2, '2024-10-25 17:33:00', 'Admin', 'Cash', 0.00, 0.00, 0.00, 60.00),
('None', 2, '2024-10-25 17:28:00', 'Admin', 'Cash', 0.00, 0.00, 0.00, 220.00);



CREATE DATABASE cafeteria_pos;
USE cafeteria_pos;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cashier_username VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cashier_username) REFERENCES users(username) ON DELETE CASCADE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Sample Data with Hashed Passwords
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$abcdefghijABCDEFGHIJ1234567890abcdEFGHIJ', 'admin'), --admin123
('cashier1', '$2y$10$klmnopqrstKLMNOPQRST1234567890klmnOPQRST', 'cashier'); --cashier123

----------------------------------------------
CREATE DATABASE cafeteria_pos;
USE cafeteria_pos;

-- -- Users Table (Admin & Cashiers)
-- CREATE TABLE users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     username VARCHAR(50) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,
--     role ENUM('admin', 'cashier') NOT NULL,
--     full_name VARCHAR(100) NOT NULL,
--     email VARCHAR(100) UNIQUE,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    salesperson VARCHAR(100) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Reports Table
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    report_type ENUM('daily', 'monthly', 'annually') NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value VARCHAR(255) NOT NULL
);

-- Sample Admin User
INSERT INTO users (username, password, role, full_name, email) VALUES
('admin', MD5('admin123'), 'admin', 'System Admin', 'admin@example.com');
-------------------------------------------------------- new it
--------------------------------------
CREATE TABLE charts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    total_orders INT NOT NULL DEFAULT 0,
    paid_orders INT NOT NULL DEFAULT 0,
    unpaid_orders INT NOT NULL DEFAULT 0,
    category VARCHAR(100) NOT NULL,
    products_in_store INT NOT NULL DEFAULT 0,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);


---dfghjkl;/jmk.,/
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    salesperson VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
------Add product COMMENT

CREATE TABLE product_added (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
);
--dfghjkl campnay Info
CREATE TABLE company_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    charge_amount DECIMAL(5,2) NOT NULL,
    vat_charge DECIMAL(5,2) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    currency VARCHAR(10) NOT NULL
);

-- Insert default company info (Modify as needed)
INSERT INTO company_info (company_name, charge_amount, vat_charge, address, phone, country, currency)
VALUES ('Default Company', 5.00, 15.00, '123 Main Street', '+1234567890', 'USA', 'USD');

-- CREATE TABLE FOR USER PROFILES --
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username INT UNIQUE NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
);
