
// fast-delivery.js

window.addEventListener('DOMContentLoaded', () => {
  const text = document.querySelector('.delivery-text h2');
  let blink = true;
  setInterval(() => {
    text.style.color = blink ? '#00aaff' : '#ff3e3e';
    blink = !blink;
  }, 800);
});
function createProductCard(product, quantity) {
  return `
    <img src="${product.image}" alt="${product.name}">
    <h3>${product.name} - $${product.price}</h3>
    <div class="quantity-control">
      <button class="sub-btn" onclick="updateCart('${product.name}', -1)">−</button>
      <div class="quantity-display" id="count-${product.name}">${quantity}</div>
      <button class="add-btn" onclick="updateCart('${product.name}', 1)">+</button>
    </div>
    <button class="buy-later-btn" onclick="buyLater('${product.name}')">🕒 Buy Later</button>
  `;
}
// Sample product data
const productsInCart = [
  {
    name: "1st Response",
    price: 20.0,
    image: "download (3).jpg",
    quantity: 2,
    expiry: "2026-12-31",
    batch: "A1234",
    allergyWarnings: "Contains latex",
    info: "Pregnancy test kit, easy to use."
  },
  {
    name: "Benadryl",
    price: 12.78,
    image: "download (2).jpg",
    quantity: 1,
    expiry: "2025-10-15",
    batch: "B5678",
    allergyWarnings: "May cause drowsiness",
    info: "Allergy relief antihistamine."
  }
];

const cartSection = document.getElementById("cart-items");
const summaryDetails = document.getElementById("summary-details");
const placeOrderBtn = document.getElementById("place-order");
const termsCheckbox = document.getElementById("terms-checkbox");

const MAX_QTY = 5;

function renderCart() {
  cartSection.innerHTML = "";
  if (productsInCart.length === 0) {
    cartSection.innerHTML = "<p>Your cart is empty.</p>";
    updateSummary();
    return;
  }

  productsInCart.forEach((prod, idx) => {
    const item = document.createElement("article");
    item.className = "cart-item";
    item.innerHTML = `
      <img src="${prod.image}" alt="${prod.name}" />
      <div class="product-info">
        <h3>
          ${prod.name} 
          <span class="tooltip" title="${prod.info}">ℹ️</span>
          <br />
          <small>Expiry: ${prod.expiry} | Batch: ${prod.batch}</small><br />
          <small style="color:#b00020;">Allergy Warning: ${prod.allergyWarnings}</small>
        </h3>
      </div>
      <div class="quantity-control" role="group" aria-label="Adjust quantity for ${prod.name}">
        <button aria-label="Decrease quantity" ${prod.quantity <= 1 ? "disabled" : ""} onclick="changeQuantity(${idx}, -1)">−</button>
        <div class="quantity-display" aria-live="polite">${prod.quantity}</div>
        <button aria-label="Increase quantity" ${prod.quantity >= MAX_QTY ? "disabled" : ""} onclick="changeQuantity(${idx}, 1)">+</button>
      </div>
      <button class="remove-btn" aria-label="Remove ${prod.name} from cart" onclick="removeItem(${idx})">Remove</button>
      <div class="subtotal">$${(prod.price * prod.quantity).toFixed(2)}</div>
    `;
    cartSection.appendChild(item);
  });

  updateSummary();
}

function changeQuantity(index, delta) {
  const prod = productsInCart[index];
  const newQty = prod.quantity + delta;
  if (newQty < 1 || newQty > MAX_QTY) return alert(`Quantity must be between 1 and ${MAX_QTY}`);
  prod.quantity = newQty;
  renderCart();
}

function removeItem(index) {
  productsInCart.splice(index, 1);
  renderCart();
}

function updateSummary() {
  let subtotal = productsInCart.reduce((acc, p) => acc + p.price * p.quantity, 0);
  const taxRate = 0.13; // 13% tax example
  const tax = subtotal * taxRate;
  const deliveryFee = subtotal > 50 ? 0 : 5; // Free delivery over $50

  let total = subtotal + tax + deliveryFee;

  summaryDetails.innerHTML = `
    <p>Subtotal: $${subtotal.toFixed(2)}</p>
    <p>Tax (13%): $${tax.toFixed(2)}</p>
    <p>Delivery Fee: $${deliveryFee.toFixed(2)}</p>
    <p><strong>Total: $${total.toFixed(2)}</strong></p>
  `;
}

// Prescription upload handling
const prescriptionUpload = document.getElementById("prescription-upload");
const uploadStatus = document.getElementById("upload-status");

prescriptionUpload.addEventListener("change", () => {
  const file = prescriptionUpload.files[0];
  if (!file) {
    uploadStatus.textContent = "";
    return;
  }
  // Simulate upload and verification
  uploadStatus.textContent = "Uploading...";
  setTimeout(() => {
    uploadStatus.textContent = "Prescription uploaded and verified ✅";
  }, 1500);
});

// Promo code logic
const applyPromoBtn = document.getElementById("apply-promo");
const promoInput = document.getElementById("promo-code");
const promoMessage = document.getElementById("promo-message");

applyPromoBtn.addEventListener("click", () => {
  const code = promoInput.value.trim().toUpperCase();
  if (!code) {
    promoMessage.textContent = "Please enter a promo code.";
    return;
  }
  // Simulated promo code check
  if (code === "SAVE10") {
    promoMessage.textContent = "Promo applied! 10% discount.";
    applyDiscount(0.10);
  } else {
    promoMessage.textContent = "Invalid promo code.";
  }
});

function applyDiscount(percent) {
  // For demo, just update total by discount percent
  let subtotal = productsInCart.reduce((acc, p) => acc + p.price * p.quantity, 0);
  const taxRate = 0.13;
  let discount = subtotal * percent;
  let tax = (subtotal - discount) * taxRate;
  let deliveryFee = subtotal - discount > 50 ? 0 : 5;
  let total = subtotal - discount + tax + deliveryFee;

  summaryDetails.innerHTML = `
    <p>Subtotal: $${subtotal.toFixed(2)}</p>
    <p>Discount: -$${discount.toFixed(2)}</p>
    <p>Tax (13%): $${tax.toFixed(2)}</p>
    <p>Delivery Fee: $${deliveryFee.toFixed(2)}</p>
    <p><strong>Total: $${total.toFixed(2)}</strong></p>
  `;
}

// Terms checkbox enables place order button
termsCheckbox.addEventListener("change", () => {
  placeOrderBtn.disabled = !termsCheckbox.checked;
});

// Place order handler
placeOrderBtn.addEventListener("click", () => {
  if (!termsCheckbox.checked) {
    alert("You must agree to terms before placing order.");
    return;
  }
  alert("Thank you for your order! It has been placed successfully.");
  // Reset cart & form (in real app, submit order to backend)
  productsInCart.length = 0;
  renderCart();
  document.getElementById("delivery-form").reset();
  prescriptionUpload.value = "";
  uploadStatus.textContent = "";
  promoInput.value = "";
  promoMessage.textContent = "";
  termsCheckbox.checked = false;
  placeOrderBtn.disabled = true;
});

renderCart();
// script.js

// Function to handle add to cart click
function addToCart(productName) {
  alert(productName + " has been added to your cart!");
}

// Handle the main hero "ADD TO CART" button
const mainAddToCartBtn = document.querySelector('.hero .btn');
if (mainAddToCartBtn) {
  mainAddToCartBtn.addEventListener('click', () => {
    addToCart('Featured Item');
  });
}

// Handle all "Quick ADD" buttons on product cards
const quickAddButtons = document.querySelectorAll('.product-card button');
quickAddButtons.forEach(button => {
  button.addEventListener('click', (event) => {
    // Get the product title in the same product card
    const productCard = event.target.closest('.product-card');
    const productTitle = productCard.querySelector('.product-title').textContent;
    addToCart(productTitle);
  });
});


  