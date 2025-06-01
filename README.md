# 🛒 Ecommerce PHP System

This is a simple eCommerce web application built in PHP with basic roles for Admin, Seller, and Customer.

## 📁 Folder Structure

Ecommerce/
├── assets/
├── config/
├── includes/
├── public/
│ ├── admin/
│ └── seller/
├── uploads/
├── install.php
├── install_data.sql
└── README.md

---

## 🛠️ Installation Guide

Follow these steps to install and run the project on your local machine:

### ✅ Requirements

- PHP 7.4 or later
- MySQL
- Web server (e.g., Apache via XAMPP, WAMP, etc.)

---

### 📦 1. Extract the Files

Download and extract the `Ecommerce.zip` into your local server directory:
- XAMPP: `C:\xampp\htdocs\Ecommerce`
- WAMP: `C:\wamp64\www\Ecommerce`

---

### 🗄️ 2. Set Up the Database

1. Start Apache and MySQL via XAMPP/WAMP.
2. Open your browser and navigate to: http://localhost/Ecommerce/install.php


3. This will:
- Create the `ecommerce_db` database
- Set up all required tables
- Import sample data (users, products, orders, etc.)

---

### 🔐 3. Login Credentials

You can log in using these sample accounts:

| Role     | Email              | Password   |
|----------|--------------------|------------|
| Admin    | geykson@gmail.com  | (hashed in DB, reset manually if needed) |
| Seller   | seller@gmail.com   | (same as above) |
| Customer | customer@gmail.com | (same)     |

> ⚠️ You may use phpMyAdmin to reset any password manually.

---

### 📁 4. Directory Notes

- `config/db.php`: Contains database connection settings.
- `public/`: Contains all routes (login, register, dashboards).
- `uploads/`: Stores user profile pictures and product images.

---

### 🧪 5. Testing

Once installed, access:

- `http://localhost/Ecommerce/public/login.php` – Login page
- `http://localhost/Ecommerce/public/customer_dashboard.php` – Customer Dashboard
- `http://localhost/Ecommerce/public/admin_dashboard.php` – Admin Dashboard
- `http://localhost/Ecommerce/public/seller_dashboard.php` – Seller Dashboard

---

### ❓ Troubleshooting

- Make sure `install_data.sql` is in the root folder.
- Confirm PHP and MySQL are running.
- If any tables are missing, re-run `install.php`.

---



