// To use your OWN photo for any item: add  image: "images/your-photo.jpg"  to that product.
// When "image" is set, it's shown automatically; when it's null, the c1/c2 gradient block is shown instead.
// Put your photo files in an "images" folder next to index.html, then set image to that path.
// If index.php injected real data from the MySQL database, use that.
// Otherwise (e.g. opening this file directly without the PHP backend), fall back to this list.
const PRODUCTS = window.SERVER_PRODUCTS && window.SERVER_PRODUCTS.length
  ? window.SERVER_PRODUCTS
  : [
  {
    id: 1,
    name: "Salted Caramel Layer Cake",
    category: "cakes",
    tag: "Cakes",
    price: 2400,
    desc: "Four layers, salted caramel drip, a little too much frosting on purpose.",
    c1: "#f2c9c2",
    c2: "#c65d6c",
    size: "big",
    image: "images/salted-caramel-cake.jpg",
  },
  {
    id: 2,
    name: "Strawberry Cake",
    category: "cakes",
    tag: "Cakes",
    price: 2200,
    desc: "Baked cheesecake layered with strawberry and a saffron drizzle.",
    c1: "#e7c26b",
    c2: "#9c3f4c",
    size: "small",
    image: "images/strawberry-cake.jpg",
  },
  {
    id: 3,
    name: "Kesar Pista Cake",
    category: "cakes",
    tag: "Cakes",
    price: 2600,
    desc: "Saffron-soaked sponge, crushed pistachio, a hint of cardamom.",
    c1: "#d9a441",
    c2: "#7c8b6f",
    size: "wide",
    image: "images/pista-cake.jpg",
  },
  {
    id: 4,
    name: "Brown Butter Croissant",
    category: "pastries",
    tag: "Pastries",
    price: 350,
    desc: "Laminated over three days, baked at 9am.",
    c1: "#e8a6ad",
    c2: "#c65d6c",
    size: "small",
    image: "images/corrisont.jpg",
  },
  {
    id: 5,
    name: "Cardamom Cinnamon Roll",
    category: "pastries",
    tag: "Pastries",
    price: 420,
    desc: "Cardamom in the dough, not just the glaze.",
    c1: "#e7c26b",
    c2: "#c9986b",
    size: "small",
    image: "images/Cardamom-Cinnamon-Roll.jpg",
  },
  {
    id: 6,
    name: "Mango Cream Tart",
    category: "pastries",
    tag: "Pastries",
    price: 480,
    desc: "Seasonal mango, light cream, a shortcrust base that stays crisp.",
    c1: "#8a95a6",
    c2: "#3f2e29",
    size: "big",
    image: "images/mango-tart-cake.jpg",
  },
  {
    id: 7,
    name: "Brown Butter Chocolate Chip",
    category: "cookies",
    tag: "Cookies",
    price: 220,
    desc: "Crisp edge, molten center, browned butter instead of melted.",
    c1: "#c9986b",
    c2: "#9c3f4c",
    size: "small",
    image: "images/Brown-Butter-Chocolate-Chip.jpg",
  },
  {
    id: 8,
    name: "Pistachio Shortbread",
    category: "cookies",
    tag: "Cookies",
    price: 200,
    desc: "Ground pistachio through the whole dough.",
    c1: "#7c8b6f",
    c2: "#3f2e29",
    size: "small",
    image: "images/Pistachio-Shortbread.jpg",
  },
  {
    id: 9,
    name: "Orange Cardamom Biscotti",
    category: "cookies",
    tag: "Cookies",
    price: 180,
    desc: "Twice-baked, built for dunking, keeps for weeks.",
    c1: "#e7c26b",
    c2: "#c65d6c",
    size: "small",
    image: "images/Orange-Cardamom-Biscotti.jpg",
  },
];

let cart = {};
let customCart = [];
let activeFilter = "all";
let searchTerm = "";

/* ---------- Cart persistence (so checkout.php can read it) ---------- */
const CART_STORAGE_KEY = "crumbco_cart";

function persistCart() {
  try {
    localStorage.setItem(
      CART_STORAGE_KEY,
      JSON.stringify({ cart, customCart }),
    );
  } catch (e) {
    // localStorage unavailable (private browsing, etc.) — cart just won't survive a page change
  }
}

function restoreCart() {
  try {
    const saved = localStorage.getItem(CART_STORAGE_KEY);
    if (saved) {
      const parsed = JSON.parse(saved);
      cart = parsed.cart || {};
      customCart = parsed.customCart || [];
    }
  } catch (e) {
    cart = {};
    customCart = [];
  }
}
restoreCart();

const menuGrid = document.getElementById("menuGrid");
const filterRow = document.getElementById("filterRow");
const searchInput = document.getElementById("searchInput");
const noResults = document.getElementById("noResults");
const resultCount = document.getElementById("resultCount");
const cartCount = document.getElementById("cartCount");
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
const cartPanel = document.getElementById("cartPanel");
const cartOverlay = document.getElementById("cartOverlay");

function renderMenu() {
  let list =
    activeFilter === "all"
      ? PRODUCTS
      : PRODUCTS.filter((p) => p.category === activeFilter);
  if (searchTerm.trim() !== "") {
    const q = searchTerm.toLowerCase();
    list = list.filter(
      (p) =>
        p.name.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q),
    );
  }

  resultCount.textContent = `${list.length} bake${list.length !== 1 ? "s" : ""} ${searchTerm ? "found" : "today"}`;
  noResults.style.display = list.length === 0 ? "block" : "none";
  menuGrid.style.display = list.length === 0 ? "none" : "grid";

  menuGrid.innerHTML = list
    .map((p) => {
      const bg = p.image
        ? `url('${p.image}') center/cover no-repeat`
        : `linear-gradient(160deg, ${p.c1}, ${p.c2})`;
      return `
      <div class="bento-card ${p.size}" data-id="${p.id}" style="background:${bg};">
        <div class="bento-overlay"></div>
        <div class="bento-content">
          <div class="bento-tag">${p.tag}</div>
          <h3>${p.name}</h3>
          <p>${p.desc}</p>
          <div class="bento-foot">
            <span class="bento-price">Rs ${p.price.toLocaleString()}</span>
            <button class="bento-add" data-id="${p.id}">+ Add</button>
          </div>
        </div>
      </div>
    `;
    })
    .join("");

  menuGrid.querySelectorAll(".bento-add").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      const id = Number(btn.dataset.id);
      addToCart(id);
      btn.textContent = "Added ✓";
      btn.classList.add("added");
      setTimeout(() => {
        btn.textContent = "+ Add";
        btn.classList.remove("added");
      }, 900);
    });
  });

  // Clicking anywhere on the card (not just the button) also orders it
  menuGrid.querySelectorAll(".bento-card").forEach((card) => {
    card.addEventListener("click", () => {
      const btn = card.querySelector(".bento-add");
      if (btn) btn.click();
    });
  });
}

function addToCart(id) {
  cart[id] = (cart[id] || 0) + 1;
  renderCart();
  const p = PRODUCTS.find((prod) => prod.id === id);
  if (p) {
    showToast("Added to your basket!", `${p.name} · Rs ${p.price.toLocaleString()}`);
  }
}
function changeQty(id, delta) {
  if (!cart[id]) return;
  cart[id] += delta;
  if (cart[id] <= 0) delete cart[id];
  renderCart();
}
function removeFromCart(id) {
  delete cart[id];
  renderCart();
}
function removeCustomCake(idx) {
  customCart.splice(idx, 1);
  renderCart();
}

function renderCart() {
  persistCart();
  const ids = Object.keys(cart);
  const totalStdItems = ids.reduce((sum, id) => sum + cart[id], 0);
  const totalCustomItems = customCart.length;
  cartCount.textContent = totalStdItems + totalCustomItems;

  let html = "";
  let total = 0;

  ids.forEach((id) => {
    const p = PRODUCTS.find((prod) => prod.id === Number(id));
    const qty = cart[id];
    total += p.price * qty;
    const thumbBg = p.image
      ? `url('${p.image}') center/cover no-repeat`
      : `linear-gradient(160deg, ${p.c1}, ${p.c2})`;
    html += `
        <div class="cart-item">
          <div class="thumb" style="background:${thumbBg};"></div>
          <div class="info">
            <strong>${p.name}</strong>
            <span>Rs ${p.price.toLocaleString()} each</span>
            <div class="qty-control">
              <button data-id="${p.id}" data-delta="-1">–</button>
              <span>${qty}</span>
              <button data-id="${p.id}" data-delta="1">+</button>
            </div>
          </div>
          <button class="cart-item-remove" data-remove-id="${p.id}" title="Remove item">✕</button>
        </div>`;
  });

  customCart.forEach((c, idx) => {
    total += c.price;
    html += `
        <div class="cart-item">
          <div class="thumb" style="background:linear-gradient(160deg, ${c.c1}, ${c.c2});"></div>
          <div class="info">
            <strong>Custom Cake: ${c.base}</strong>
            <span>${c.decor} · "${c.message || "No message"}"</span>
            <span>Rs ${c.price.toLocaleString()}</span>
          </div>
          <button class="cart-item-remove" data-remove-custom="${idx}" title="Remove item">✕</button>
        </div>`;
  });

  if (ids.length === 0 && customCart.length === 0) {
    cartItems.innerHTML = `<div class="cart-empty">Your basket is empty — add something warm from today's menu.</div>`;
  } else {
    cartItems.innerHTML = html;
  }
  cartTotal.textContent = "Rs " + total.toLocaleString();

  cartItems.querySelectorAll(".qty-control button").forEach((btn) => {
    btn.addEventListener("click", () =>
      changeQty(Number(btn.dataset.id), Number(btn.dataset.delta)),
    );
  });
  cartItems.querySelectorAll("[data-remove-id]").forEach((btn) => {
    btn.addEventListener("click", () =>
      removeFromCart(Number(btn.dataset.removeId)),
    );
  });
  cartItems.querySelectorAll("[data-remove-custom]").forEach((btn) => {
    btn.addEventListener("click", () =>
      removeCustomCake(Number(btn.dataset.removeCustom)),
    );
  });
}

filterRow.querySelectorAll(".filter-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    filterRow
      .querySelectorAll(".filter-btn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");
    activeFilter = btn.dataset.filter;
    renderMenu();
  });
});

searchInput.addEventListener("input", (e) => {
  searchTerm = e.target.value;
  renderMenu();
});

document.getElementById("cartOpenBtn").addEventListener("click", () => {
  cartPanel.classList.add("open");
  cartOverlay.classList.add("open");
});
document.getElementById("cartCloseBtn").addEventListener("click", closeCart);
cartOverlay.addEventListener("click", closeCart);
function closeCart() {
  cartPanel.classList.remove("open");
  cartOverlay.classList.remove("open");
}

document.getElementById("checkoutBtn").addEventListener("click", () => {
  const totalItems =
    Object.values(cart).reduce((a, b) => a + b, 0) + customCart.length;
  if (totalItems === 0) {
    showToast("Your basket is empty", "Add something from today's menu first.");
    return;
  }
  persistCart();
  window.location.href = "checkout.php";
});

renderMenu();
renderCart();

/* ---------- BUILD YOUR OWN CAKE ---------- */
let selectedBase = {
  name: "vanilla",
  price: 1600,
  c1: "#fbe9dd",
  c2: "#f2c9c2",
};
let selectedDecor = { name: "fruit", add: 200, icon: "🍓🍒" };

const baseOptions = document.getElementById("baseOptions");
const decorOptions = document.getElementById("decorOptions");
const cakeMessage = document.getElementById("cakeMessage");
const builderPrice = document.getElementById("builderPrice");
const cakeBody = document.getElementById("cakeBody");
const cakeSwirl = document.getElementById("cakeSwirl");
const cakeDrip = document.getElementById("cakeDrip");
const cakeDecor = document.getElementById("cakeDecor");
const cakePlaque = document.getElementById("cakePlaque");

function updateBuilder() {
  const total = selectedBase.price + selectedDecor.add;
  builderPrice.textContent = "Rs " + total.toLocaleString();
  cakeBody.style.setProperty("--cake-base", selectedBase.c1);
  cakeSwirl.style.setProperty("--cake-top", selectedBase.c2);
  cakeDrip.style.setProperty("--cake-top", selectedBase.c2);
  cakeDecor.textContent = selectedDecor.icon;
  cakePlaque.textContent = cakeMessage.value.trim() || "Your message here";
}

baseOptions.querySelectorAll(".option-chip").forEach((btn) => {
  btn.addEventListener("click", () => {
    baseOptions
      .querySelectorAll(".option-chip")
      .forEach((b) => b.classList.remove("selected"));
    btn.classList.add("selected");
    selectedBase = {
      name: btn.dataset.base,
      price: Number(btn.dataset.price),
      c1: btn.dataset.color1,
      c2: btn.dataset.color2,
    };
    updateBuilder();
  });
});

decorOptions.querySelectorAll(".option-chip").forEach((btn) => {
  btn.addEventListener("click", () => {
    decorOptions
      .querySelectorAll(".option-chip")
      .forEach((b) => b.classList.remove("selected"));
    btn.classList.add("selected");
    selectedDecor = {
      name: btn.dataset.decor,
      add: Number(btn.dataset.add),
      icon: btn.dataset.icon,
    };
    updateBuilder();
  });
});

cakeMessage.addEventListener("input", updateBuilder);
updateBuilder();

document.getElementById("addCustomCake").addEventListener("click", () => {
  customCart.push({
    base: baseOptions.querySelector(".selected").textContent,
    decor: decorOptions.querySelector(".selected").textContent,
    message: cakeMessage.value.trim(),
    price: selectedBase.price + selectedDecor.add,
    c1: selectedBase.c1,
    c2: selectedBase.c2,
  });
  renderCart();
  showToast(
    "Added to your basket!",
    "Your custom cake order is ready to confirm.",
  );
  cartPanel.classList.add("open");
  cartOverlay.classList.add("open");
});

/* ---------- MOOD WHEEL ---------- */
const WHEEL_ITEMS = [
  { name: "Kesar Pista Cake", price: 2600, color: "#d9a441" },
  { name: "Brown Butter Croissant", price: 350, color: "#e8a6ad" },
  { name: "Gulab Jamun Cheesecake", price: 2200, color: "#9c3f4c" },
  { name: "Mango Cream Tart", price: 480, color: "#8a95a6" },
  { name: "Pistachio Shortbread", price: 200, color: "#7c8b6f" },
  { name: "Salted Caramel Cake", price: 2400, color: "#c65d6c" },
  { name: "Cardamom Cinnamon Roll", price: 420, color: "#e7c26b" },
  { name: "Orange Biscotti", price: 180, color: "#c9986b" },
];

const wheelDisc = document.getElementById("wheelDisc");
const wheelOuter = document.querySelector(".wheel-outer");
const spinBtn = document.getElementById("spinBtn");
const wheelResult = document.getElementById("wheelResult");
const wheelResultName = document.getElementById("wheelResultName");
const wheelAddBtn = document.getElementById("wheelAddBtn");

const N = WHEEL_ITEMS.length;
const segAngle = 360 / N;

function buildWheel() {
  // color segments with a thin divider line between each slice
  const stops = [];
  WHEEL_ITEMS.forEach((item, i) => {
    const start = i * segAngle;
    const end = (i + 1) * segAngle;
    stops.push(`${item.color} ${start}deg ${end - 0.6}deg`);
    stops.push(`rgba(255,246,239,0.9) ${end - 0.6}deg ${end}deg`);
  });
  wheelDisc.style.background = `conic-gradient(${stops.join(", ")})`;

  // clear old labels, then place new ones using trig so text always stays upright
  wheelDisc.querySelectorAll(".wheel-label").forEach((l) => l.remove());
  const size = wheelDisc.getBoundingClientRect().width || 280;
  const radius = (size / 2) * 0.64;

  WHEEL_ITEMS.forEach((item, i) => {
    const angleDeg = i * segAngle + segAngle / 2;
    const rad = ((angleDeg - 90) * Math.PI) / 180;
    const x = radius * Math.cos(rad);
    const y = radius * Math.sin(rad);
    const label = document.createElement("div");
    label.className = "wheel-label";
    label.style.left = `calc(50% + ${x}px)`;
    label.style.top = `calc(50% + ${y}px)`;
    label.textContent = item.name.split(" ")[0];
    wheelDisc.appendChild(label);
  });
}
buildWheel();

let resizeTimer;
window.addEventListener("resize", () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(buildWheel, 150);
});

let totalRotation = 0;
let spinning = false;
let pendingResultIndex = null;

spinBtn.addEventListener("click", () => {
  if (spinning) return;
  spinning = true;
  spinBtn.disabled = true;
  wheelResult.classList.remove("show");

  const targetIndex = Math.floor(Math.random() * N);
  pendingResultIndex = targetIndex;
  const targetCenter = targetIndex * segAngle + segAngle / 2;
  const baseRotation = (360 - targetCenter) % 360;
  const currentMod = ((totalRotation % 360) + 360) % 360;
  let delta = (baseRotation - currentMod + 360) % 360;
  totalRotation += 4 * 360 + delta;

  wheelDisc.style.transform = `rotate(${totalRotation}deg)`;

  setTimeout(() => {
    const picked = WHEEL_ITEMS[pendingResultIndex];
    wheelResultName.textContent = `${picked.name} — Rs ${picked.price.toLocaleString()}`;
    wheelResult.classList.add("show");
    wheelAddBtn.dataset.name = picked.name;
    spinning = false;
    spinBtn.disabled = false;
  }, 3300);
});

wheelAddBtn.addEventListener("click", (e) => {
  e.preventDefault();
  const name = wheelAddBtn.dataset.name;
  const match =
    PRODUCTS.find((p) => p.name.includes(name.split(" ")[0])) || PRODUCTS[0];
  addToCart(match.id);
  cartPanel.classList.add("open");
  cartOverlay.classList.add("open");
});

/* ===================================================================
   PREMIUM INTERACTIONS
   =================================================================== */

/* ---------- Scroll progress bar ---------- */
(function () {
  const bar = document.getElementById("scrollProgress");
  if (!bar) return;
  window.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;
    const docHeight =
      document.documentElement.scrollHeight - window.innerHeight;
    const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    bar.style.width = pct + "%";
  });
})();

/* ---------- Section reveal on scroll (distinct personality per section) ---------- */
(function () {
  const revealEls = document.querySelectorAll("[data-reveal]");
  if (!revealEls.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          el.classList.add("in-view");

          // Stagger children for card/schedule sections
          if (el.dataset.reveal === "stagger") {
            const children = el.querySelectorAll(".bento-card, .bake-slot");
            children.forEach((child, i) => {
              child.style.transitionDelay = `${i * 0.06}s`;
            });
          }
          observer.unobserve(el);
        }
      });
    },
    { threshold: 0.15 },
  );

  revealEls.forEach((el) => {
    // Hero fades in immediately on load rather than waiting for scroll
    if (el.classList.contains("hero")) {
      requestAnimationFrame(() => el.classList.add("in-view"));
    } else {
      observer.observe(el);
    }
  });
})();

/* ---------- Dynamic time highlight on Bake Schedule ---------- */
(function () {
  const slots = document.querySelectorAll(".bake-slot[data-hour]");
  if (!slots.length) return;

  const currentHour = new Date().getHours();
  let closestSlot = null;
  let closestDiff = Infinity;

  slots.forEach((slot) => {
    const hour = Number(slot.dataset.hour);
    const diff = currentHour - hour;
    // Pick the most recent bake time that has already passed (or the first one, if before 7am)
    if (diff >= 0 && diff < closestDiff) {
      closestDiff = diff;
      closestSlot = slot;
    }
  });
  if (!closestSlot) closestSlot = slots[0];
  closestSlot.classList.add("time-active");
})();

/* ---------- Confetti sprinkle burst on "add to cart" ---------- */
function spawnConfetti(x, y) {
  const emojis = ["✨", "🍓", "🌿", "✨", "🍪"];
  for (let i = 0; i < 8; i++) {
    const piece = document.createElement("span");
    piece.className = "confetti-piece";
    piece.textContent = emojis[Math.floor(Math.random() * emojis.length)];
    const angle = Math.random() * Math.PI * 2;
    const distance = 40 + Math.random() * 50;
    const dx = Math.cos(angle) * distance;
    const dy = Math.sin(angle) * distance - 20;
    piece.style.setProperty("--dx", `${dx}px`);
    piece.style.setProperty("--dy", `${dy}px`);
    piece.style.setProperty("--rot", `${Math.random() * 360}deg`);
    piece.style.left = x + "px";
    piece.style.top = y + "px";
    document.body.appendChild(piece);
    setTimeout(() => piece.remove(), 850);
  }
}

document.addEventListener("click", (e) => {
  const target = e.target.closest(
    ".bento-add, #addCustomCake, #wheelAddBtn",
  );
  if (target) {
    spawnConfetti(e.clientX, e.clientY);
  }
});

/* ---------- Toast notifications (fake order confirmation) ---------- */
function showToast(title, sub) {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerHTML = `
    <span class="toast-icon">🎉</span>
    <div>
      <strong>${title}</strong>
      <span class="sub">${sub || ""}</span>
    </div>
  `;
  container.appendChild(toast);

  requestAnimationFrame(() => toast.classList.add("show"));

  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 400);
  }, 2800);
}
