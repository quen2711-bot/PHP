CREATE DATABASE tiem_tra_sua
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE tiem_tra_sua;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255) NULL,
    description TEXT NULL,
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(15) NOT NULL,
    customer_address VARCHAR(255) NOT NULL,
    note TEXT NULL,
    total_amount INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_customer_phone (customer_phone)
);

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO products (name, price, image, description, is_active) VALUES
('Trà sữa truyền thống', 25000, NULL, 'Trà sữa thơm ngon', 1),
('Trà sữa trân châu đường đen', 30000, NULL, 'Trân châu đường đen', 1),
('Trà đào cam sả', 28000, NULL, 'Trà đào tươi mát', 1),
('Matcha latte', 35000, NULL, 'Matcha béo thơm', 1);

INSERT INTO users (username, password) VALUES
('admin', '123456');