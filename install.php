<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ecommerce_db';

// 1. Connect to MySQL
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// 2. Create database
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

// 3. Create tables
$tables = [

    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        email VARCHAR(255),
        password VARCHAR(255),
        role ENUM('admin', 'seller', 'customer'),
        profile_pic VARCHAR(255),
        contact_number VARCHAR(50),
        address TEXT
    )",

    "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        seller_id INT,
        name VARCHAR(255),
        description TEXT,
        price DECIMAL(10,2),
        stock INT,
        image VARCHAR(255)
    )",

    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT,
        product_id INT,
        quantity INT,
        total_price DECIMAL(10,2),
        order_date DATETIME,
        status VARCHAR(50)
    )",

    "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        message TEXT,
        is_read BOOLEAN DEFAULT 0,
        created_at DATETIME
    )",

    "CREATE TABLE IF NOT EXISTS transactions (
        id INT AUTO_INCREMENT PRIMARY KEY
        -- Add columns here when needed
    )"
];

// 4. Execute table creation
foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        echo "❌ Error creating table: " . $conn->error . "<br>";
    }
}

// 5. Insert Data
$dataSQL = file_get_contents(__DIR__ . "/install_data.sql");

if ($conn->multi_query($dataSQL)) {
    do {
        // Flush multi query results
    } while ($conn->more_results() && $conn->next_result());
    echo "✅ Database and data installed successfully.";
} else {
    echo "❌ Error inserting data: " . $conn->error;
}

$conn->close();
?>
