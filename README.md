# Crumb & Co — Full-Stack Bakery Website 

A full-stack bakery ordering website built with **PHP, MySQL, and vanilla JavaScript** — built as a portfolio project to demonstrate frontend, backend, and database skills together (no framework required, runs on plain XAMPP).

Live search, a "build your own cake" tool, a spin-the-wheel picker, a working shopping basket, and **real order storage in MySQL** with a small admin dashboard to view incoming orders.

## Tech Stack

- **Frontend:** HTML, CSS, vanilla JavaScript (no framework — shows core JS skills clearly to recruiters)
- **Backend:** PHP (procedural, PDO for database access)
- **Database:** MySQL
- **Server:** Apache (via XAMPP)

## Project Structure
```
crumb-co-site/
├── index.php              → main page (PHP fetches products from MySQL, injects into JS)
├── config/
│   └── db.php              → database connection (PDO)
├── api/
│   ├── products.php         → GET: returns all products as JSON
│   └── create_order.php     → POST: saves a new order + items to the database
├── admin/
│   └── orders.php            → simple dashboard to view all orders, update status
├── assets/
│   ├── style.css
│   ├── script.js
│   └── images/                → product photos
├── database/
│   └── schema.sql              → run this once to create tables + seed today's menu
└── README.md
```

## How the full-stack part works

1. **Products come from MySQL, not hardcoded JS.** `index.php` queries the `products` table and injects the result into the page as `window.SERVER_PRODUCTS`. `script.js` uses that if present — meaning you can now add/edit/remove menu items by editing the database (via phpMyAdmin), no code changes needed.
2. **A real checkout page.** Clicking "Checkout" in the basket takes you to `checkout.php` — a proper order page with an order summary, a delivery details form (name, phone, address), and a payment method selector (Cash on Delivery, JazzCash, Easypaisa, or a demo Card form). Submitting it calls `api/create_order.php`, which saves the order for real in MySQL and returns an order ID, shown on a confirmation screen.
3. **Admin dashboard** (`admin/orders.php`) lists every order placed — customer details, items, payment method, and total — and lets you update each order's status (pending → confirmed → delivered).

### If you already imported the database before this update

The `orders` table now has two new columns (`delivery_address`, `payment_method`) to support the checkout page. If you already ran the old `schema.sql` and don't want to start over, just import `database/migration_add_checkout_fields.sql` instead (Import tab in phpMyAdmin, same as before). If you're setting this up fresh, just use `schema.sql` — it already includes these columns.

## Setup with XAMPP

1. **Install XAMPP** (if not already) from apachefriends.org, and open the XAMPP Control Panel.
2. **Start Apache and MySQL** from the control panel (both need to be running).
3. **Copy this whole `crumb-co-site` folder** into your XAMPP's `htdocs` directory:
   - Windows: `C:\xampp\htdocs\crumb-co-site`
   - Mac: `/Applications/XAMPP/htdocs/crumb-co-site`
   - Linux: `/opt/lampp/htdocs/crumb-co-site`
4. **Import the database:**
   - Open `http://localhost/phpmyadmin` in your browser.
   - Click **Import** → choose the file `database/schema.sql` → click **Go**.
   - This creates the `crumb_co` database with 3 tables (`products`, `orders`, `order_items`) and seeds today's 9 menu items.
5. **Check the DB connection settings** in `config/db.php` — the defaults (`root` user, empty password) match XAMPP's default MySQL setup, so you usually don't need to change anything.
6. **Visit the site:** `http://localhost/crumb-co-site/index.php`
7. **View the admin dashboard:** `http://localhost/crumb-co-site/admin/orders.php`

That's it — no `npm install`, no build step, no framework setup. Just Apache + MySQL + PHP, exactly like XAMPP provides out of the box.

## Testing the full flow

1. Open the site, add a few items to the basket (or build a custom cake).
2. Click **Checkout** — this opens `checkout.php` with your order summary.
3. Fill in name, phone, and address, pick a payment method, and click **Place Order**.
4. You'll see a confirmation screen with a real order number.
5. Open `admin/orders.php` — your order should appear there immediately, with customer details, items, and total.
6. Try changing an order's status from the dropdown — it updates instantly.

## Editing the menu

You no longer need to touch any code to change what's on the menu:

- Open **phpMyAdmin** → `crumb_co` database → `products` table.
- Edit prices, descriptions, or add new rows directly.
- Refresh the site — changes show up immediately (server-rendered on every page load).

To add a new product's photo: put the image file in `assets/images/`, then set that row's `image` column to `assets/images/your-photo.jpg`.

## Notes for demoing this to recruiters

- This project intentionally uses **plain PHP + PDO** rather than a framework (Laravel, etc.) so every line of backend logic is visible and easy to explain in an interview — good for entry-level portfolio pieces.
- The `api/` folder follows a simple REST-ish pattern (`GET /api/products.php`, `POST /api/create_order.php`), which is a natural talking point if asked "have you built an API before?"
- The database uses a proper foreign-key relationship (`order_items.order_id → orders.id`, `order_items.product_id → products.id`) — worth mentioning if asked about relational database design.
- `config/db.php` uses PDO with prepared statements throughout (see `api/create_order.php`) — protects against SQL injection, a good point to raise if asked about security basics.

## Known limitations (be upfront about these if asked)

- Payment methods (JazzCash/Easypaisa/Card) are selectable at checkout, but no real payment gateway is wired up — orders are recorded as "pending" and marked paid manually via the admin dashboard. Mention this as the natural next step ("next I'd integrate JazzCash's actual API").
- No login/authentication for the admin dashboard — anyone with the URL can view it. Fine for a portfolio demo, but flag this as a "next step" if discussing production-readiness.
- The Bake Schedule and Crumb Wheel sections are still static (not database-driven) — mention this as a possible extension if asked what you'd add next.

## Putting a live demo on your portfolio

XAMPP is just for local development — recruiters can't visit `localhost`. To get a real, shareable link, you need PHP + MySQL hosting (static hosts like GitHub Pages, Netlify, or Vercel won't run PHP). Good free/cheap options:

- **InfinityFree** (infinityfree.net) — free PHP + MySQL hosting, good for portfolio demos.
- **000webhost** (000webhost.com) — free tier, PHP + MySQL, easy to use.
- **Hostinger** — very cheap shared hosting popular in Pakistan, PHP + MySQL included.

General steps for any of these:
1. Sign up, create a new hosting account/site.
2. Use their **File Manager** (or FTP) to upload everything inside `crumb-co-site/` to the site's root folder (often called `public_html`).
3. Use their **phpMyAdmin** (usually available from the hosting dashboard) to import `database/schema.sql`, exactly like you did locally.
4. Update `config/db.php` with the database credentials **they give you** (host, database name, username, password — these are almost never `root`/empty on real hosting, unlike XAMPP).
5. Visit your new live URL (e.g. `https://yourname.infinityfreeapp.com/index.php`) — that's your shareable link.

Put that link directly on your portfolio/resume, and mention it's a full-stack project (PHP, MySQL, vanilla JS) with a working checkout flow and admin dashboard — that combination, not just a static frontend, is what makes it stand out to recruiters.
