-- Database: register_task
-- Select register_task in phpMyAdmin before running this file.
-- CREATE TABLE IF NOT EXISTS will NOT replace or erase existing data.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    category_id INT NOT NULL,
    image VARCHAR(255) NULL,
    CONSTRAINT fk_products_category
      FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- IMPORTANT for an older existing products table:
-- If the image column does not exist, run this ONE TIME separately:
-- ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL;
-- Do not run the ALTER command if the column already exists.
