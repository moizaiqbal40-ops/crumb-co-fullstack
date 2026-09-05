<h1 align="center">🍰 Crumb & Co — Full-Stack Bakery Website</h1>

<p align="center">
  A full-stack bakery ordering experience built with <strong>PHP, MySQL, vanilla JavaScript, HTML & CSS</strong>.
</p>

<p align="center">
  <em>Browse the menu · Build a custom cake · Checkout · Store orders · Manage orders</em>
</p>

---

## 📌 Overview

**Crumb & Co** is a portfolio-focused full-stack bakery website designed to demonstrate how a frontend connects to a real backend and relational database — without relying on a frontend framework.

The application includes a database-driven menu, live search and filtering, a custom cake builder, shopping basket, checkout flow, order persistence, and an admin dashboard for managing incoming orders.

Unlike a static ecommerce mockup, products and orders are stored in **MySQL**, while PHP handles server-side data access and order creation.

## ✨ Features

- 🧁 **Database-driven menu** — products are loaded from MySQL rather than hardcoded into the UI
- 🔎 **Live product search** — search pastries, cakes, cookies, and flavors
- 🏷️ **Category filters** — Cakes, Pastries, Cookies, or All
- 🎂 **Build Your Own Cake** — choose base flavor, decoration, and a custom message
- 🧺 **Shopping basket** — add products and custom cakes before checkout
- 🛒 **Real checkout flow** — customer details, delivery address, order summary, and payment-method selection
- 💾 **Persistent orders** — checkout requests are saved to MySQL with their individual order items
- 📋 **Admin dashboard** — view incoming orders and update their status
- 🔌 **REST-style API endpoints** — PHP endpoints expose product data and accept order creation requests
- 🎡 **Crumb Wheel** — interactive random treat picker
- 📱 **Responsive UI** — designed for desktop and smaller screens

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP |
| Database | MySQL |
| Database Access | PDO + prepared statements |
| Web Server | Apache |
| Local Environment | XAMPP |
| API | PHP REST-style JSON endpoints |

> **Why no framework?** This project intentionally uses vanilla JavaScript and procedural PHP so the core frontend, backend, API, and database logic remain visible and easy to explain in a technical interview.

## 🏗️ Architecture

```text
Browser
   │
   ├── HTML / CSS / JavaScript
   │
   ▼
PHP Application
   │
   ├── index.php          → database-driven storefront
   ├── checkout.php       → checkout + order submission
   ├── api/products.php   → GET products as JSON
   └── api/create_order.php → POST + persist orders
   │
   ▼
PDO
   │
   ▼
MySQL
   ├── products
   ├── orders
   └── order_items

Admin
   │
   └── admin/orders.php → view + update order status
```

## 📁 Project Structure

```text
crumb-co-fullstack/
├── index.php
├── checkout.php
│
├── admin/
│   └── orders.php
│
├── api/
│   ├── products.php
│   └── create_order.php
│
├── config/
│   └── db.php
│
├── assets/
│   ├── style.css
│   ├── script.js
│   └── images/
│       └── product images
│
├── database/
│   ├── schema.sql
│   └── migration_add_checkout_fields.sql
│
├── .gitignore
└── README.md
```

## 🔄 How the Full-Stack Flow Works

### 1. Products → MySQL → Frontend

The storefront queries the `products` table through PHP. Product information such as name, category, price, description, availability, and image path is retrieved from the database and passed to the frontend.

This means menu changes can be made through the database without rewriting the product list in JavaScript.

### 2. Shopping Basket → Checkout

Users can add regular menu items or a custom cake to the basket. The checkout page collects the customer's name, phone number, delivery address, and selected payment method.

### 3. Checkout → API → Database

`api/create_order.php` accepts the order payload, validates required customer information, starts a database transaction, inserts the order, inserts its order items, and returns the generated order ID as JSON.

### 4. Admin → Order Management

`admin/orders.php` provides a simple dashboard where stored orders can be viewed and their status can be updated through the order lifecycle.

## 🔌 API Endpoints

### `GET /api/products.php`

Returns available products as JSON.

**Purpose:**
- Retrieve database-backed menu data
- Provide a reusable read endpoint
- Keep product information separate from frontend hardcoding

### `POST /api/create_order.php`

Creates a new order from checkout data.

**Handles:**
- Regular products
- Custom cakes
- Customer information
- Delivery address
- Payment-method selection
- Order totals
- Order items

The endpoint uses a database transaction so the order and its related items are persisted together.

## 🗄️ Database Design

The project uses three main relational tables:

```text
products
   │
   │  product_id
   ▼
order_items ──────► orders
```

- **`products`** — menu items and product metadata
- **`orders`** — customer/order-level information and status
- **`order_items`** — individual products or custom cakes belonging to an order

Foreign-key relationships connect order items to their parent order and, where applicable, the original product.

## 🚀 Run Locally with XAMPP

### Prerequisites

- XAMPP
- Apache
- MySQL
- A web browser

### Setup

1. Install and open **XAMPP**.
2. Start **Apache** and **MySQL**.
3. Copy the repository into your XAMPP `htdocs` directory:

```text
C:\xampp\htdocs\crumb-co-fullstack
```

4. Open **phpMyAdmin**:

```text
http://localhost/phpmyadmin
```

5. Import:

```text
database/schema.sql
```

6. Check `config/db.php` and make sure its credentials match your local MySQL setup.
7. Open the application:

```text
http://localhost/crumb-co-fullstack/index.php
```

8. Open the admin dashboard:

```text
http://localhost/crumb-co-fullstack/admin/orders.php
```

No `npm install`, bundler, or frontend build step is required.

## 🧪 Test the Complete Flow

1. Open the storefront.
2. Search or filter the menu.
3. Add one or more products to the basket.
4. Build a custom cake and add it as well.
5. Open checkout.
6. Enter the required customer and delivery information.
7. Select a payment method.
8. Place the order.
9. Confirm that an order ID is returned.
10. Open the admin dashboard.
11. Verify the new order and update its status.

## 📸 Screenshots

<!--
Add project screenshots here later.
Recommended: 4 screenshots showing the storefront, custom cake builder,
checkout flow, and admin dashboard.

Example:
<p align="center">
  <img src="screenshots/home.png" width="48%" alt="Crumb & Co homepage" />
  <img src="screenshots/menu.png" width="48%" alt="Crumb & Co menu" />
</p>

<p align="center">
  <img src="screenshots/checkout.png" width="48%" alt="Crumb & Co checkout" />
  <img src="screenshots/admin.png" width="48%" alt="Crumb & Co admin dashboard" />
</p>
-->

## 🔐 Security & Engineering Notes

- Database access is handled through **PDO**.
- Order insertion uses **prepared statements**.
- Input is validated before an order is stored.
- Order creation uses a **database transaction** so related records are committed together.
- The database schema uses **foreign keys** to maintain relational integrity.

## ⚠️ Current Limitations

This is a portfolio project rather than a production ecommerce system.

- **Payment gateways are not actually integrated.** JazzCash, Easypaisa, and Card are selectable demo payment methods; no real transaction is processed.
- **Admin authentication is not implemented.** The dashboard is intentionally simple for demonstration purposes.
- **Bake Schedule and Crumb Wheel data are currently static.** They could be moved into database tables in a future iteration.
- **XAMPP is intended for local development.** A PHP/MySQL hosting environment would be required for a public deployment.

## 🚧 Future Improvements

- Add admin authentication and role-based access
- Integrate a real payment provider
- Add server-side CSRF protection and stronger input validation
- Add customer accounts and order history
- Add inventory/availability management
- Move bake schedules and wheel options into MySQL
- Add automated tests for API and checkout flows
- Deploy to a PHP/MySQL hosting environment with production configuration

## 🎯 What This Project Demonstrates

This project demonstrates practical understanding of:

- Frontend development without relying on a framework
- PHP server-side programming
- REST-style API design
- MySQL relational database design
- CRUD-style data handling
- PDO and prepared statements
- Database transactions
- Foreign-key relationships
- Form validation
- Client/server data flow
- Responsive UI implementation
- Building a complete feature from UI → API → database → admin workflow

## 👩‍💻 Portfolio Note

**Crumb & Co** was built to demonstrate full-stack fundamentals through a complete, explainable application rather than a purely visual frontend. The strongest part of the project is the connection between the customer-facing experience, PHP backend, JSON API, relational MySQL database, and admin order workflow.
