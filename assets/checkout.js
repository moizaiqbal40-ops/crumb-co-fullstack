/* ===================================================================
   CHECKOUT PAGE LOGIC
   Reads the cart saved by the main site (assets/script.js) from
   localStorage, shows an order summary, collects delivery + payment
   details, and submits the real order to api/create_order.php.
   =================================================================== */

const CART_STORAGE_KEY = "crumbco_cart";

// Keep this in sync with the PRODUCTS array used on the main site,
// so we can look up price/name/image even if only an id was stored.
// (index.php injects the live database list as window.SERVER_PRODUCTS.)
function getProductById(id) {
  const list = window.SERVER_PRODUCTS || [];
  return list.find((p) => p.id === Number(id));
}

function loadCart() {
  try {
    const saved = localStorage.getItem(CART_STORAGE_KEY);
    if (!saved) return { cart: {}, customCart: [] };
    const parsed = JSON.parse(saved);
    return { cart: parsed.cart || {}, customCart: parsed.customCart || [] };
  } catch (e) {
    return { cart: {}, customCart: [] };
  }
}

function clearCart() {
  try {
    localStorage.removeItem(CART_STORAGE_KEY);
  } catch (e) {
    /* ignore */
  }
}

const { cart, customCart } = loadCart();

const summaryEl = document.getElementById("orderSummaryItems");
const summaryTotalEl = document.getElementById("orderSummaryTotal");
const emptyStateEl = document.getElementById("checkoutEmptyState");
const checkoutFormWrap = document.getElementById("checkoutFormWrap");
const checkoutForm = document.getElementById("checkoutForm");
const successView = document.getElementById("checkoutSuccess");

let total = 0;
let itemCount = 0;
const standardItems = [];

Object.keys(cart).forEach((id) => {
  const p = getProductById(id);
  if (!p) return;
  const qty = cart[id];
  total += p.price * qty;
  itemCount += qty;
  standardItems.push({ id: p.id, name: p.name, price: p.price, qty, image: p.image });
});

customCart.forEach((c) => {
  total += c.price;
  itemCount += 1;
});

function renderSummary() {
  if (itemCount === 0) {
    emptyStateEl.style.display = "block";
    checkoutFormWrap.style.display = "none";
    return;
  }

  let html = "";
  standardItems.forEach((item) => {
    const thumbBg = item.image
      ? `url('${item.image}') center/cover no-repeat`
      : "var(--blush)";
    html += `
      <div class="summary-item">
        <div class="summary-thumb" style="background:${thumbBg};"></div>
        <div class="summary-info">
          <strong>${item.name}</strong>
          <span>Qty ${item.qty} × Rs ${item.price.toLocaleString()}</span>
        </div>
        <span class="summary-line-total">Rs ${(item.price * item.qty).toLocaleString()}</span>
      </div>`;
  });

  customCart.forEach((c) => {
    html += `
      <div class="summary-item">
        <div class="summary-thumb" style="background:linear-gradient(160deg, ${c.c1}, ${c.c2});"></div>
        <div class="summary-info">
          <strong>Custom Cake — ${c.base}</strong>
          <span>${c.decor}${c.message ? ` · "${c.message}"` : ""}</span>
        </div>
        <span class="summary-line-total">Rs ${c.price.toLocaleString()}</span>
      </div>`;
  });

  summaryEl.innerHTML = html;
  summaryTotalEl.textContent = "Rs " + total.toLocaleString();
}
renderSummary();

/* ---------- Payment method switching ---------- */
const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
const cardFields = document.getElementById("cardFields");

paymentRadios.forEach((radio) => {
  radio.addEventListener("change", () => {
    document.querySelectorAll(".payment-option").forEach((el) => el.classList.remove("selected"));
    radio.closest(".payment-option").classList.add("selected");
    cardFields.style.display = radio.value === "card" ? "grid" : "none";
  });
});

/* ---------- Form submission ---------- */
if (checkoutForm) {
  checkoutForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const submitBtn = document.getElementById("placeOrderBtn");
    const originalLabel = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Placing your order...";

    const formData = new FormData(checkoutForm);
    const payload = {
      items: standardItems.map((i) => ({ id: i.id, name: i.name, price: i.price, qty: i.qty })),
      customItems: customCart,
      total,
      customerName: formData.get("customerName"),
      customerPhone: formData.get("customerPhone"),
      deliveryAddress: formData.get("deliveryAddress"),
      paymentMethod: formData.get("paymentMethod"),
    };

    try {
      const response = await fetch("api/create_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const result = await response.json();

      if (result.success) {
        clearCart();
        showSuccess(result.order_id, payload);
      } else {
        alert(result.error || "Something went wrong placing your order. Please try again.");
        submitBtn.disabled = false;
        submitBtn.textContent = originalLabel;
      }
    } catch (err) {
      alert(
        "Couldn't reach the server. Make sure you're viewing this via XAMPP (http://localhost/...) and MySQL is running.",
      );
      submitBtn.disabled = false;
      submitBtn.textContent = originalLabel;
    }
  });
}

function showSuccess(orderId, payload) {
  checkoutFormWrap.style.display = "none";
  document.getElementById("orderSummaryCard").style.display = "none";
  successView.style.display = "block";

  document.getElementById("successOrderId").textContent = "#" + orderId;
  document.getElementById("successTotal").textContent = "Rs " + payload.total.toLocaleString();
  document.getElementById("successAddress").textContent = payload.deliveryAddress;
  document.getElementById("successPayment").textContent = paymentLabel(payload.paymentMethod);

  window.scrollTo({ top: 0, behavior: "smooth" });
}

function paymentLabel(method) {
  const labels = {
    cod: "Cash on Delivery",
    jazzcash: "JazzCash",
    easypaisa: "Easypaisa",
    card: "Credit / Debit Card",
  };
  return labels[method] || "Cash on Delivery";
}
