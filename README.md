<div align="center">

# 🍰 Crumb & Co

### A full-stack bakery ordering experience built from the UI to the database.

</div>

## 💡 What It Is

A bakery website where the frontend actually talks to a PHP backend and MySQL database. Users can browse products, build a custom cake, checkout, and create a real stored order instead of interacting with a static mockup.

## 🛠️ Tech Stack

- **PHP** · MySQL
- **HTML5 · CSS3 · Vanilla JavaScript**
- **PDO + prepared statements**
- **Apache / XAMPP**

## ⚙️ How It Works

```text
Browser
   ↓
PHP + JavaScript
   ↓
JSON API
   ↓
PDO
   ↓
MySQL
```

Products come from the database, while checkout sends validated order data to the backend, which stores the order and its items in a transaction. A simple admin page is included for order management.

## 🚀 Run Locally

1. Start **Apache + MySQL** in XAMPP.
2. Put the project inside `htdocs`.
3. Import `database/schema.sql` through phpMyAdmin.
4. Open:

```text
http://localhost/crumb-co-fullstack/index.php
```

## ✨ What I Learned / Challenges

The main challenge was connecting a polished frontend to real backend logic — especially making checkout, validation, transactions, and relational order data work together cleanly.
