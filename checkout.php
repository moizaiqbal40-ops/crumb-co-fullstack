<?php
require_once __DIR__ . "/config/db.php";

// Same product list the main site uses, so checkout.js can resolve prices/names/images
$stmt = $pdo->query(
    "SELECT id, name, category, tag, price, description, color1, color2, size, image
     FROM products WHERE is_available = 1 ORDER BY id ASC"
);
$dbProducts = $stmt->fetchAll();
$serverProducts = array_map(function ($p) {
    return [
        "id" => (int) $p["id"],
        "name" => $p["name"],
        "price" => (int) $p["price"],
        "image" => $p["image"],
    ];
}, $dbProducts);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout — Crumb & Co</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Work+Sans:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/style.css" />
  </head>
  <body>
    <div class="grain-overlay" aria-hidden="true"></div>

    <header>
      <nav>
        <a href="index.php" class="logo">
          <svg viewBox="0 0 24 24" fill="none">
            <ellipse cx="12" cy="15" rx="8" ry="6" fill="currentColor" opacity="0.9" />
            <path d="M6 12C6 8 8 4 12 4C16 4 18 8 18 12" stroke="currentColor" stroke-width="1.4" fill="none" />
          </svg>
          Crumb & Co
        </a>
        <a href="index.php" class="checkout-back-link">← Back to menu</a>
      </nav>
    </header>

    <main class="checkout-page">
      <div class="wrap checkout-wrap">
        <h1 class="checkout-title">Checkout</h1>

        <div id="checkoutEmptyState" class="checkout-empty" style="display:none;">
          <p>Your basket is empty.</p>
          <a href="index.php#menu" class="btn-primary">Browse today's menu</a>
        </div>

        <div class="checkout-grid" id="checkoutFormWrap">
          <div class="checkout-summary-col">
            <div class="summary-card" id="orderSummaryCard">
              <h3>Order Summary</h3>
              <div id="orderSummaryItems"></div>
              <div class="summary-total-row">
                <span>Total</span>
                <strong id="orderSummaryTotal">Rs 0</strong>
              </div>
            </div>
          </div>

          <div class="checkout-form-col">
            <form id="checkoutForm">
              <div class="form-card">
                <h3>Delivery Details</h3>
                <div class="form-row">
                  <label for="customerName">Full Name</label>
                  <input type="text" id="customerName" name="customerName" placeholder="Your name" required />
                </div>
                <div class="form-row">
                  <label for="customerPhone">Phone Number</label>
                  <input type="tel" id="customerPhone" name="customerPhone" placeholder="03XX XXXXXXX" required />
                </div>
                <div class="form-row">
                  <label for="deliveryAddress">Delivery Address</label>
                  <textarea id="deliveryAddress" name="deliveryAddress" rows="3" placeholder="House #, street, area, city" required></textarea>
                </div>
              </div>

              <div class="form-card">
                <h3>Payment Method</h3>
                <div class="payment-options">
                  <label class="payment-option selected">
                    <input type="radio" name="paymentMethod" value="cod" checked />
                    <span class="payment-icon">💵</span>
                    <span class="payment-label">Cash on Delivery</span>
                  </label>
                  <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="jazzcash" />
                    <span class="payment-icon">📱</span>
                    <span class="payment-label">JazzCash</span>
                  </label>
                  <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="easypaisa" />
                    <span class="payment-icon">📲</span>
                    <span class="payment-label">Easypaisa</span>
                  </label>
                  <label class="payment-option">
                    <input type="radio" name="paymentMethod" value="card" />
                    <span class="payment-icon">💳</span>
                    <span class="payment-label">Credit / Debit Card</span>
                  </label>
                </div>

                <div class="card-fields" id="cardFields" style="display:none;">
                  <div class="form-row">
                    <label>Card Number</label>
                    <input type="text" placeholder="4242 4242 4242 4242" maxlength="19" />
                  </div>
                  <div class="form-row">
                    <label>Expiry</label>
                    <input type="text" placeholder="MM/YY" maxlength="5" />
                  </div>
                  <div class="form-row">
                    <label>CVV</label>
                    <input type="text" placeholder="123" maxlength="3" />
                  </div>
                  <p class="card-demo-note">Demo only — no real payment is processed. Card details aren't sent or stored.</p>
                </div>
              </div>

              <button type="submit" class="btn-primary place-order-btn" id="placeOrderBtn">
                Place Order
              </button>
            </form>
          </div>
        </div>

        <div class="checkout-success" id="checkoutSuccess" style="display:none;">
          <div class="success-icon">✓</div>
          <h2>Order Confirmed!</h2>
          <p class="success-order-id">Order <strong id="successOrderId"></strong></p>
          <div class="success-details">
            <div><span>Total</span><strong id="successTotal"></strong></div>
            <div><span>Payment</span><strong id="successPayment"></strong></div>
            <div><span>Delivering to</span><strong id="successAddress"></strong></div>
          </div>
          <p class="success-note">We've received your order and will start baking right away. You'll get a call to confirm delivery timing.</p>
          <a href="index.php" class="btn-primary">Continue Shopping</a>
        </div>
      </div>
    </main>

    <footer class="checkout-footer">
      <div class="wrap">
        <span>© 2026 Crumb & Co. Baked fresh, ordered your way.</span>
        <span>Lahore, Pakistan</span>
      </div>
    </footer>

    <script>
      window.SERVER_PRODUCTS = <?php echo json_encode($serverProducts); ?>;
    </script>
    <script src="assets/checkout.js"></script>
  </body>
</html>
