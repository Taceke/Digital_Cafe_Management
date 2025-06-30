CREATE DATABASE IF NOT EXISTS cafeteria_pos;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'cashier') NOT NULL
);
-- CREATE TABLE IF NOT EXISTS sales_report (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     customer VARCHAR(255) NOT NULL DEFAULT 'None',
--     qty INT NOT NULL,
--     order_date DATETIME NOT NULL,
--     salesperson VARCHAR(255) NOT NULL,
--     payment_method VARCHAR(50) NOT NULL,
--     discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
--     total_amount DECIMAL(10,2) NOT NULL,
--    ALTER TABLE sales_report ,
-- ADD COLUMN company_id INT NOT NULL DEFAULT 1,
-- ADD COLUMN charge_amount DECIMAL(5,2) NOT NULL DEFAULT 0.00,
-- ADD COLUMN vat_charge DECIMAL(5,2) NOT NULL DEFAULT 0.00,
-- ADD CONSTRAINT fk_company_info FOREIGN KEY (company_id) REFERENCES company_info(id);
-- );

CREATE TABLE `sales_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` varchar(255) NOT NULL DEFAULT 'None',
  `qty` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `salesperson` varchar(255) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `S_carge` decimal(5,2) DEFAULT NULL,
  `vat_charge` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_company` (`company_id`),
  CONSTRAINT `fk_company` FOREIGN KEY (`company_id`) REFERENCES `company_info` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci



CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);
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
CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    report_type ENUM('daily', 'monthly', 'annually') NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    salesperson VARCHAR(255) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);
------Add product COMMENT

CREATE TABLE product_added (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    ALTER TABLE product_added ADD COLUMN unit VARCHAR(50) NOT NULL DEFAULT 'unit';

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
CREATE TABLE user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
);

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

--///////////////////inventory.sql/




CREATE TABLE raw_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    min_stock DECIMAL(10,2) NOT NULL, -- For low stock alerts
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    item_condition ENUM('New', 'Needs Repair', 'Broken') NOT NULL, -- Renamed column
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- CREATE TABLE purchases (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     material_id INT,
--     quantity DECIMAL(10,2) NOT NULL,
--     cost DECIMAL(10,2) NOT NULL,
--     purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     FOREIGN KEY (material_id) REFERENCES raw_materials(id) ON DELETE CASCADE
-- );


-- CREATE TABLE product_material_usage (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     product_id INT NOT NULL,
--     material_id INT NOT NULL,
--     quantity_used DECIMAL(10,2) NOT NULL, -- Amount of raw material per product
--     FOREIGN KEY (product_id) REFERENCES product_added(id) ON DELETE CASCADE,
--     FOREIGN KEY (material_id) REFERENCES raw_materials(id) ON DELETE CASCADE
-- );
-- CREATE TABLE product_raw_materials (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     product_id INT,
--     material_id INT,
--     quantity_used FLOAT,  
--     unit VARCHAR(20),  -- Added unit column (kg, g, L, etc.)
--     FOREIGN KEY (product_id) REFERENCES product_added(id) ON DELETE CASCADE,
--     FOREIGN KEY (material_id) REFERENCES raw_materials(id) ON DELETE CASCADE
-- );
-- CREATE TABLE product_raw_materials (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     product_id INT NOT NULL,
--     material_id INT NOT NULL,
--     quantity_used FLOAT NOT NULL,
--     FOREIGN KEY (product_id) REFERENCES product_added(id),
--     FOREIGN KEY (material_id) REFERENCES raw_materials(id)
-- );


CREATE TABLE product_ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity_required FLOAT NOT NULL,
    unit VARCHAR(50) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES product_added(id),
    FOREIGN KEY (material_id) REFERENCES raw_materials(id)
);


