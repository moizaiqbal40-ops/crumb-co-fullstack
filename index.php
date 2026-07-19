<?php
require_once __DIR__ . "/config/db.php";

// Fetch today's menu from the database instead of a hardcoded JS array.
$stmt = $pdo->query(
    "SELECT id, name, category, tag, price, description, color1, color2, size, image
     FROM products WHERE is_available = 1 ORDER BY id ASC"
);
$dbProducts = $stmt->fetchAll();

// Reshape field names to match what script.js expects (c1, c2, desc).
$serverProducts = array_map(function ($p) {
    return [
        "id" => (int) $p["id"],
        "name" => $p["name"],
        "category" => $p["category"],
        "tag" => $p["tag"],
        "price" => (int) $p["price"],
        "desc" => $p["description"],
        "c1" => $p["color1"],
        "c2" => $p["color2"],
        "size" => $p["size"],
        "image" => $p["image"],
    ];
}, $dbProducts);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crumb & Co — Baked Fresh in Lahore</title>
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
    <div class="toast-container" id="toastContainer" aria-live="polite"></div>
    <div class="scroll-progress" id="scrollProgress" aria-hidden="true"></div>
    <header>
      <nav>
        <a href="#" class="logo">
          <svg viewBox="0 0 24 24" fill="none">
            <ellipse
              cx="12"
              cy="15"
              rx="8"
              ry="6"
              fill="currentColor"
              opacity="0.9"
            />
            <path
              d="M6 12C6 8 8 4 12 4C16 4 18 8 18 12"
              stroke="currentColor"
              stroke-width="1.4"
              fill="none"
            />
          </svg>
          Crumb & Co
        </a>
        <div class="search-box">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" />
          </svg>
          <input
            type="text"
            id="searchInput"
            placeholder="Search pastries, cakes, cookies…"
          />
        </div>
        <div class="navlinks">
          <a href="#menu">Menu</a>
          <a href="#builder">Build a Cake</a>
          <a href="#wheel">Feeling Lucky?</a>
        </div>
        <button class="cart-btn" id="cartOpenBtn">
          🧺 Cart <span class="cart-count" id="cartCount">0</span>
        </button>
      </nav>
    </header>

    <section class="hero" data-reveal="fade">
      <div class="wrap">
        <div>
          <div class="eyebrow">Home bakers, based in Lahore</div>
          <h1>Baked fresh, <em>ordered your way.</em></h1>
          <p class="lede">
            Crumb & Co is a small home bakery serving Lahore — small batches,
            custom cakes with your own message on top, and always something new
            coming out of the oven.
          </p>
          <div class="hero-actions">
            <a href="#menu" class="btn-primary">See today's menu</a>
            <a href="#builder" class="btn-ghost"
              ><span class="circle">✎</span> Build your own cake</a
            >
          </div>
        </div>
        <div class="hero-visual">
          <img src="assets/images/pista-cake.jpg" alt="Kesar Pista Cake" class="hero-photo" />
          <div class="hero-photo-shade"></div>
          <div class="floating-particles" aria-hidden="true">
            <span class="particle p1">🍓</span>
            <span class="particle p2">🌿</span>
            <span class="particle p3">✨</span>
            <span class="particle p4">🍪</span>
            <span class="particle p5">🥐</span>
            <span class="particle p6">✨</span>
          </div>
          <div class="hero-card">
            <div class="dot"></div>
            <div>
              <strong>Kesar Pista Cake</strong
              ><span>This week's most-ordered flavor</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="schedule-section" id="schedule" data-reveal="stagger">
      <div class="wrap">
        <div class="schedule-head">
          <h2>Today's Bake Schedule</h2>
          <p>
            Small batches, all day — here's what's coming out of the oven, and
            when.
          </p>
        </div>
        <div class="bake-strip">
          <div class="bake-slot" style="--tint: #f5d98a" data-hour="7">
            <div class="t">7 AM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🥖</span></div>
            <div class="item">Sourdough</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #e3ae7a" data-hour="9">
            <div class="t">9 AM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🥐</span></div>
            <div class="item">Croissant</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #f0a8b4" data-hour="11">
            <div class="t">11 AM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🍩</span></div>
            <div class="item">Cinnamon Roll</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #e88a95" data-hour="13">
            <div class="t">1 PM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🍰</span></div>
            <div class="item">Layer Cake</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #a9b8cc" data-hour="15">
            <div class="t">3 PM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🥭</span></div>
            <div class="item">Mango Tart</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #a8c19a" data-hour="17">
            <div class="t">5 PM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🍪</span></div>
            <div class="item">Cookies</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
          <div class="bake-slot" style="--tint: #d97e8c" data-hour="19">
            <div class="t">7 PM</div>
            <div class="bake-icon-badge"><span class="bake-icon">🧁</span></div>
            <div class="item">Gulab Jamun Cheesecake</div>
            <span class="fresh-badge"><span class="fresh-badge-inner">Fresh from oven</span></span>
          </div>
        </div>
      </div>
    </section>

    <section class="menu-section" id="menu" data-reveal="stagger">
      <div class="wrap">
        <div class="section-head">
          <h2>Today's menu</h2>
          <div class="num" id="resultCount">9 bakes today</div>
        </div>
        <div class="filter-row" id="filterRow">
          <button class="filter-btn active" data-filter="all">All</button>
          <button class="filter-btn" data-filter="cakes">Cakes</button>
          <button class="filter-btn" data-filter="pastries">Pastries</button>
          <button class="filter-btn" data-filter="cookies">Cookies</button>
        </div>
        <div class="bento-grid" id="menuGrid"></div>
        <div class="no-results" id="noResults" style="display: none">
          No bakes match that search — try "cake", "cookie", or a flavor.
        </div>
      </div>
    </section>

    <section class="builder-section" id="builder" data-reveal="scale">
      <div class="wrap">
        <div class="builder-panel">
          <div class="builder-controls">
            <div class="eyebrow">Custom order</div>
            <h2>Build your own cake</h2>
            <p>
              Pick a base, choose your topping, and put whatever message you
              like on it — birthday, anniversary, or just because.
            </p>

            <div class="control-group">
              <label>Base flavor</label>
              <div class="option-row" id="baseOptions">
                <button
                  class="option-chip selected"
                  data-base="vanilla"
                  data-price="1600"
                  data-color1="#fbe9dd"
                  data-color2="#f2c9c2"
                >
                  Vanilla
                </button>
                <button
                  class="option-chip"
                  data-base="chocolate"
                  data-price="1800"
                  data-color1="#7a5041"
                  data-color2="#4a2f26"
                >
                  Chocolate
                </button>
                <button
                  class="option-chip"
                  data-base="redvelvet"
                  data-price="1900"
                  data-color1="#9c3f4c"
                  data-color2="#6b2530"
                >
                  Red Velvet
                </button>
                <button
                  class="option-chip"
                  data-base="kesarpista"
                  data-price="2100"
                  data-color1="#d9a441"
                  data-color2="#8a95a6"
                >
                  Kesar Pista
                </button>
              </div>
            </div>

            <div class="control-group">
              <label>Top decoration</label>
              <div class="option-row" id="decorOptions">
                <button
                  class="option-chip selected"
                  data-decor="fruit"
                  data-add="200"
                  data-icon="🍓🍒"
                >
                  Fresh Fruit
                </button>
                <button
                  class="option-chip"
                  data-decor="choc"
                  data-add="150"
                  data-icon="🍫🍫"
                >
                  Chocolate Drip
                </button>
                <button
                  class="option-chip"
                  data-decor="flowers"
                  data-add="250"
                  data-icon="🌸🌼"
                >
                  Edible Flowers
                </button>
                <button
                  class="option-chip"
                  data-decor="sprinkles"
                  data-add="100"
                  data-icon="✨✨"
                >
                  Sprinkles
                </button>
              </div>
            </div>

            <div class="control-group">
              <label>Message on cake</label>
              <input
                type="text"
                class="name-input"
                id="cakeMessage"
                placeholder="e.g. Happy Birthday Ayesha"
                maxlength="30"
              />
            </div>

            <div class="builder-total">
              <span class="price" id="builderPrice">Rs 1,800</span>
              <button class="btn-primary" id="addCustomCake">
                Add to basket
              </button>
            </div>
          </div>

          <div class="builder-preview">
            <img
              src="assets/images/strawberry-cake.jpg"
              alt=""
              class="builder-photo-backdrop"
            />
            <div class="cake-visual">
              <div class="cake-body" id="cakeBody">
                <div class="cake-frosting-swirl" id="cakeSwirl"></div>
                <div class="cake-drip" id="cakeDrip"></div>
              </div>
              <div class="cake-plate"></div>
              <div class="cake-decor" id="cakeDecor">🍓🍒</div>
              <div class="cake-plaque" id="cakePlaque">Your message here</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="wheel-section" id="wheel" data-reveal="rotate">
      <div class="wrap">
        <div class="wheel-panel">
          <div class="wheel-copy">
            <div class="eyebrow">Can't decide?</div>
            <h2>Spin the <em>Crumb Wheel</em></h2>
            <p>
              Feeling indecisive? Spin it and we'll pick today's treat for you —
              every flavor on the wheel is baked and ready today.
            </p>
            <div class="wheel-result" id="wheelResult">
              <div class="label">Your mood pick</div>
              <strong id="wheelResultName">—</strong>
              <a href="#menu" class="add-btn" id="wheelAddBtn">Add to basket</a>
            </div>
          </div>
          <div class="wheel-wrap">
            <div class="wheel-outer">
              <div class="wheel-pointer"></div>
              <div class="wheel-disc" id="wheelDisc"></div>
              <div class="wheel-center">🎂</div>
            </div>
            <button class="spin-btn" id="spinBtn">Spin the wheel</button>
          </div>
        </div>
      </div>
    </section>

    <section class="story" id="story" data-reveal="mask">
      <div class="wrap">
        <div>
          <div class="eyebrow">A home bakery, since 2022</div>
          <h2>Started in <em>one kitchen</em>, still just one kitchen.</h2>
          <p>
            Crumb & Co began as weekend orders for neighbors in Lahore. We've
            kept it small on purpose — every cake is still made by hand, in
            small batches, with local flavors like kesar, pista, mango and gulab
            jamun alongside the classics.
          </p>
          <div class="story-stats">
            <div>
              <strong>2022</strong><span>First order, first kitchen</span>
            </div>
            <div><strong>500+</strong><span>Custom cakes made</span></div>
            <div><strong>Lahore</strong><span>Home delivery only</span></div>
          </div>
        </div>
        <div class="story-visual">
          <img src="assets/images/salted-caramel-cake.jpg" alt="A Crumb & Co cake, made by hand" />
        </div>
      </div>
    </section>

    <section class="cta-band" data-reveal="fade">
      <div class="wrap">
        <div class="eyebrow">Custom cakes, made to order</div>
        <h2>Tell us the occasion. <em>We'll handle the sugar.</em></h2>
        <div class="cta-actions">
          <a href="#builder" class="btn-primary">Build a custom cake</a>
          <a
            href="https://wa.me/923000000000"
            target="_blank"
            class="btn-secondary"
            >Order on WhatsApp</a
          >
        </div>
        <p class="note">
          Replace the WhatsApp number above with your own before publishing.
        </p>
      </div>
    </section>

    <footer data-reveal="parallax">
      <svg
        class="footer-flowers"
        viewBox="0 0 320 190"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <path d="M18 190 C 18 130, 30 90, 26 60" stroke="#5e6d54" stroke-width="4" fill="none" stroke-linecap="round"/>
        <path d="M55 190 C 52 140, 66 110, 60 75" stroke="#7c8b6f" stroke-width="4" fill="none" stroke-linecap="round"/>
        <path d="M92 190 C 90 135, 78 100, 88 68" stroke="#5e6d54" stroke-width="4" fill="none" stroke-linecap="round"/>
        <path d="M128 190 C 126 150, 138 120, 132 95" stroke="#7c8b6f" stroke-width="3.5" fill="none" stroke-linecap="round"/>

        <path d="M26 100 C 8 92, 4 108, 20 114 C 8 122, 14 138, 30 128" fill="#7c8b6f" opacity="0.9"/>
        <path d="M60 95 C 42 88, 38 104, 54 110 C 42 118, 48 134, 64 122" fill="#5e6d54" opacity="0.9"/>
        <path d="M88 88 C 106 82, 110 98, 94 104 C 108 110, 100 126, 84 116" fill="#7c8b6f" opacity="0.9"/>

        <g transform="translate(26,60)">
          <ellipse cx="0" cy="-14" rx="9" ry="14" fill="#c65d6c"/>
          <ellipse cx="13" cy="-4" rx="9" ry="14" fill="#c65d6c" transform="rotate(72 13 -4)"/>
          <ellipse cx="8" cy="12" rx="9" ry="14" fill="#c65d6c" transform="rotate(144 8 12)"/>
          <ellipse cx="-8" cy="12" rx="9" ry="14" fill="#c65d6c" transform="rotate(216 -8 12)"/>
          <ellipse cx="-13" cy="-4" rx="9" ry="14" fill="#c65d6c" transform="rotate(288 -13 -4)"/>
          <circle cx="0" cy="0" r="7" fill="#d9a441"/>
        </g>

        <g transform="translate(60,72) scale(0.82)">
          <ellipse cx="0" cy="-14" rx="9" ry="14" fill="#d9a441"/>
          <ellipse cx="13" cy="-4" rx="9" ry="14" fill="#d9a441" transform="rotate(72 13 -4)"/>
          <ellipse cx="8" cy="12" rx="9" ry="14" fill="#d9a441" transform="rotate(144 8 12)"/>
          <ellipse cx="-8" cy="12" rx="9" ry="14" fill="#d9a441" transform="rotate(216 -8 12)"/>
          <ellipse cx="-13" cy="-4" rx="9" ry="14" fill="#d9a441" transform="rotate(288 -13 -4)"/>
          <circle cx="0" cy="0" r="7" fill="#9c3f4c"/>
        </g>

        <g transform="translate(88,65) scale(0.62)">
          <ellipse cx="0" cy="-14" rx="9" ry="14" fill="#f2c9c2"/>
          <ellipse cx="13" cy="-4" rx="9" ry="14" fill="#f2c9c2" transform="rotate(72 13 -4)"/>
          <ellipse cx="8" cy="12" rx="9" ry="14" fill="#f2c9c2" transform="rotate(144 8 12)"/>
          <ellipse cx="-8" cy="12" rx="9" ry="14" fill="#f2c9c2" transform="rotate(216 -8 12)"/>
          <ellipse cx="-13" cy="-4" rx="9" ry="14" fill="#f2c9c2" transform="rotate(288 -13 -4)"/>
          <circle cx="0" cy="0" r="7" fill="#c65d6c"/>
        </g>

        <g transform="translate(130,92) scale(0.5)">
          <ellipse cx="0" cy="-14" rx="9" ry="14" fill="#c65d6c"/>
          <ellipse cx="13" cy="-4" rx="9" ry="14" fill="#c65d6c" transform="rotate(72 13 -4)"/>
          <ellipse cx="8" cy="12" rx="9" ry="14" fill="#c65d6c" transform="rotate(144 8 12)"/>
          <ellipse cx="-8" cy="12" rx="9" ry="14" fill="#c65d6c" transform="rotate(216 -8 12)"/>
          <ellipse cx="-13" cy="-4" rx="9" ry="14" fill="#c65d6c" transform="rotate(288 -13 -4)"/>
          <circle cx="0" cy="0" r="7" fill="#d9a441"/>
        </g>

        <circle cx="150" cy="130" r="3" fill="#7c8b6f"/>
        <circle cx="165" cy="150" r="2.4" fill="#d9a441"/>
        <circle cx="140" cy="160" r="2" fill="#c65d6c"/>
      </svg>
      <div class="footer-big-text" aria-hidden="true">Butter &amp; Love</div>
      <div class="wrap">
        <div class="footer-top">
          <div class="footer-brand">
            <a href="#" class="logo">
              <svg viewBox="0 0 24 24" fill="none" width="20" height="20">
                <ellipse cx="12" cy="15" rx="8" ry="6" fill="currentColor" />
                <path
                  d="M6 12C6 8 8 4 12 4C16 4 18 8 18 12"
                  stroke="currentColor"
                  stroke-width="1.4"
                  fill="none"
                />
              </svg>
              Crumb & Co
            </a>
            <p>
              Small-batch cakes and pastries, baked fresh and delivered across
              Lahore.
            </p>
          </div>
          <div class="footer-cols">
            <div class="footer-col">
              <h4>Shop</h4>
              <a href="#menu">Today's Menu</a>
              <a href="#builder">Build a Cake</a>
              <a href="#wheel">Spin the Wheel</a>
            </div>
            <div class="footer-col">
              <h4>Studio</h4>
              <a href="#story">Our Story</a>
              <a href="#">Delivery Areas</a>
              <a href="https://wa.me/923000000000" target="_blank"
                >WhatsApp Us</a
              >
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <span>© 2026 Crumb & Co. Baked fresh, ordered your way.</span>
          <span>Lahore, Pakistan</span>
        </div>
      </div>
    </footer>

    <div class="cart-overlay" id="cartOverlay"></div>
    <aside class="cart-panel" id="cartPanel">
      <div class="cart-head">
        <h3>Your Basket</h3>
        <button class="cart-close" id="cartCloseBtn">✕</button>
      </div>
      <div class="cart-items" id="cartItems">
        <div class="cart-empty">
          Your basket is empty — add something warm from today's menu.
        </div>
      </div>
      <div class="cart-foot">
        <div class="cart-total-row">
          <span>Total</span><span id="cartTotal">Rs 0</span>
        </div>
        <button class="checkout-btn" id="checkoutBtn">Checkout</button>
      </div>
    </aside>

    <script>
      // Products come from the MySQL database (see config/db.php + api/products.php).
      // script.js uses this if present, falling back to its own hardcoded list otherwise.
      window.SERVER_PRODUCTS = <?php echo json_encode($serverProducts); ?>;
    </script>
    <script src="assets/script.js"></script>
  </body>
</html>
