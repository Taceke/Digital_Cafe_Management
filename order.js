document.addEventListener("DOMContentLoaded", function () {
  // Wait for the product-list element before calling fetchProducts
  const checkExist = setInterval(() => {
    if (document.getElementById("product-list")) {
      clearInterval(checkExist);
      fetchProducts();
    }
  }, 100); // Check every 100ms

  async function fetchProducts(category = "All") {
    try {
      const response = await fetch(`fetch_products.php?category=${category}`);
      const products = await response.json();
      renderProducts(products);
    } catch (error) {
      console.error("Error fetching products:", error);
    }
  }

  function renderProducts(products) {
    const productList = document.getElementById("product-list");

    if (!productList) {
      console.error("Error: Element with ID 'product-list' not found.");
      return;
    }

    productList.innerHTML = "";

    products.forEach((product) => {
      let productDiv = document.createElement("div");
      productDiv.classList.add("product-item");

      let productImage = document.createElement("img");
      productImage.src = `uploads/${product.image}`;
      productImage.alt = product.name;
      productImage.style.width = "100px";
      productImage.style.height = "100px";
      productImage.style.objectFit = "cover";
      productImage.style.borderRadius = "2px";

      let productName = document.createElement("p");
      productName.textContent = product.name;
      productName.style.fontSize = "12px";
      productName.style.margin = "5px 0";

      let productPrice = document.createElement("p");
      productPrice.innerHTML = `ETB ${product.price} <br>per ${product.unit}`;
      productPrice.style.fontSize = "12px";
      productPrice.style.color = "#333";

      let productStock = document.createElement("p");
      productStock.innerHTML = `Stock: <span id="stock-${product.id}">${product.quantity} ${product.unit}</span>`;
      productStock.style.fontSize = "12px";
      productStock.style.color = "#ff0000";

      let addButton = document.createElement("button");
      addButton.textContent = "order";
      addButton.style.background = "#6a4c93";
      addButton.style.color = "white";
      addButton.style.border = "none";
      addButton.style.padding = "6px 6px";
      addButton.style.cursor = "pointer";
      addButton.style.borderRadius = "5px";
      addButton.style.width = "20%";
      addButton.style.fontSize = "14px";
      addButton.onclick = () =>
        openPopup(product.id, product.name, product.price, product.quantity);

      productDiv.appendChild(productImage);
      productDiv.appendChild(productName);
      productDiv.appendChild(productPrice);
      productDiv.appendChild(productStock);
      productDiv.appendChild(addButton);

      productList.appendChild(productDiv);
    });
  }

  // Function to filter products by category
  window.filterProducts = function (category) {
    fetchProducts(category);
  };
});

window.openPopup = function (id, name, price, stock) {
  const popup = document.getElementById("popup");
  popup.classList.add("show-popup");
  popup.dataset.productId = id;
  popup.dataset.productPrice = price;
  popup.dataset.productName = name;
  popup.dataset.productStock = stock;
};
window.submitDetails = function () {
  const quantity = parseFloat(document.getElementById("quantity").value);
  const productId = popup.dataset.productId;
  const productPrice = parseFloat(popup.dataset.productPrice);
  const productName = popup.dataset.productName;
  const currentStock = parseFloat(popup.dataset.productStock);

  if (!quantity || quantity <= 0) {
    alert("Please enter a valid quantity.");
    return;
  }

  if (quantity > currentStock) {
    alert("Not enough stock available!");
    return;
  }

  const total = (quantity * productPrice).toFixed(2);
  const orderList = document.getElementById("order-list");
  const row = document.createElement("tr");
  row.innerHTML = `
      <td>${productName}</td>
      <td>${productPrice}</td>
      <td>${quantity}</td>
      <td>-</td>
      <td>0</td>
      <td>${total}</td>
      <td><button onclick="removeItem(this)">X</button></td>
  `;
  orderList.insertBefore(row, orderList.firstChild);

  closePopup();
};

function updateStock(items) {
  items.forEach((item) => {
    fetch("update_stock.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name: item.name, quantity: item.quantity }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById(`stock-${data.product_id}`).innerText =
            data.new_quantity;
        }
      })
      .catch((error) => console.error("Error updating stock:", error));
  });
}

function pay() {
  console.log("Pay function triggered!");

  const orderItems = [];
  document.querySelectorAll("#order-list tr").forEach((row) => {
    const name = row.cells[0] ? row.cells[0].innerText : "";
    const priceText = row.cells[1]
      ? row.cells[1].innerText.replace("ETB ", "")
      : "";
    const quantityText = row.cells[2] ? row.cells[2].innerText : "";

    const price = parseFloat(priceText);
    const quantity = parseFloat(quantityText); // allow decimals instead of parseInt

    if (!name || isNaN(price) || isNaN(quantity)) {
      console.error("Invalid order data:", { name, price, quantity });
      return;
    }

    orderItems.push({ name, price, quantity });
  });

  if (orderItems.length === 0) {
    alert("No items in order. Please add products.");
    return;
  }

  const paymentMethod = prompt(
    "Enter Payment Method (Cash, Card, Mobile Pay):",
    "Cash"
  );
  if (!paymentMethod) return;

  const customerName = prompt("Enter Customer Name (Optional):", "Guest");

  const requestData = {
    items: orderItems,
    payment_method: paymentMethod,
    customer: customerName,
  };

  fetch("process_payment.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(requestData),
  })
    .then((response) => response.text()) // First get text to debug errors
    .then((text) => {
      try {
        return JSON.parse(text); // Try to parse JSON
      } catch (error) {
        console.error("Invalid JSON response from server:", text);
        throw new Error("Server response is not valid JSON.");
      }
    })
    .then((data) => {
      console.log("Server response:", data);

      if (data.status === "success") {
        alert(`Payment successful! Total: ETB${data.totalAmount}`);

        document.getElementById("order-list").innerHTML = "";

        if (data.items) {
          data.items.forEach((item) => {
            const stockElement = document.getElementById(`stock-${item.name}`);
            if (stockElement) {
              stockElement.innerText = Math.max(
                0,
                parseInt(stockElement.innerText) - item.quantity
              );
            }
          });
        }

        // ✅ Ensure fetchReportByDate() is defined before calling it
        if (typeof fetchReportByDate === "function") {
          setTimeout(fetchReportByDate, 500);
        }

        const orderSection = document.getElementById("order-section");
        if (orderSection) {
          const paymentMessage = document.createElement("p");
          paymentMessage.innerText = "✅ Payment Completed";
          paymentMessage.style.color = "green";
          paymentMessage.style.fontWeight = "bold";
          orderSection.appendChild(paymentMessage);
        }
      } else {
        alert("Payment failed: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while processing payment.");
    });
}

function resetPopupFields() {
  document.getElementById("quantity-input").value = ""; // Reset quantity field
  // document.getElementById("salesperson-input").value = ""; // Reset salesperson field
}

function closePopup() {
  document.getElementById("popup").classList.remove("show-popup");
}

function removeItem(button) {
  button.parentElement.parentElement.remove();
}
function clearAll() {
  document.getElementById("order-list").innerHTML = "";
}

function printReceipt() {
  let orderItems = document.querySelectorAll("#order-list tr");

  if (orderItems.length === 0) {
    alert("No items in the order.");
    return;
  }

  let receiptContent = `
      <html>
      <head>
          <title>Invoice</title>
          <style>
              body { font-family: Arial, sans-serif; text-align: center; }
              h2 { margin-bottom: 5px; }
              table { width: 100%; border-collapse: collapse; margin-top: 10px; }
              th, td { border: 1px solid #000; padding: 8px; text-align: left; }
              th { background-color: #f2f2f2; }
              .total-row { font-weight: bold; }
              .text-right { text-align: right; }
          </style>
      </head>
      <body>
          <h2>Cash Register Pro - Invoice</h2>
          <hr>
          <table>
              <tr>
                  <th>Item</th>
                  <th>Qty</th>
                  <th>Price</th>
                  <th>Discount</th>
                  <th>Tax</th>
                  <th>Total</th>
              </tr>
  `;

  let orderData = [];
  let totalAmount = 0;
  let taxRate = 15; // Example tax rate (15%)
  let totalTax = 0;
  let totalDiscount = 0;

  orderItems.forEach((row) => {
    let itemName = row.cells[0].innerText;
    let itemPrice = parseFloat(row.cells[1].innerText);
    let itemQtyElement = row.cells[2].querySelector("input");
    let itemQty = itemQtyElement ? parseInt(itemQtyElement.value) : 1;
    let discount = parseFloat(row.cells[4]?.innerText) || 0; // Assuming discount is in column 4
    let total = itemPrice * itemQty - discount;
    let tax = (total * taxRate) / 100;

    totalAmount += total;
    totalTax += tax;
    totalDiscount += discount;

    receiptContent += `
          <tr>
              <td>${itemName}</td>
              <td class="text-right">${itemQty}</td>
              <td class="text-right">${itemPrice.toFixed(2)}</td>
              <td class="text-right">${discount.toFixed(2)}</td>
              <td class="text-right">${tax.toFixed(2)}</td>
              <td class="text-right">${total.toFixed(2)}</td>
          </tr>
      `;

    orderData.push({
      name: itemName,
      price: itemPrice,
      quantity: itemQty,
      discount: discount,
      tax: tax,
      total: total,
    });
  });

  let grandTotal = totalAmount + totalTax;

  receiptContent += `
      <tr class="total-row">
          <td colspan="3" class="text-right">Subtotal</td>
          <td class="text-right">${totalDiscount.toFixed(2)}</td>
          <td class="text-right">${totalTax.toFixed(2)}</td>
          <td class="text-right">${grandTotal.toFixed(2)}</td>
      </tr>
      </table>
      <hr>
      <p>Thank you for shopping with us!</p>
      <script>window.print();</script>
      </body></html>
  `;

  // Prompt the user
  let action = prompt(
    "Type 'print' to print the receipt or enter an email to send the receipt:"
  );

  if (!action) {
    alert("Action canceled.");
    return;
  }

  action = action.trim().toLowerCase();

  if (action === "print") {
    // Open a new window and print
    let printWindow = window.open("", "", "width=800,height=800");
    printWindow.document.write(receiptContent);
    printWindow.document.close();
  } else if (action.includes("@")) {
    // Send email
    let emailData = {
      email: action,
      order: orderData,
      subtotal: totalAmount,
      tax: totalTax,
      discount: totalDiscount,
      total: grandTotal,
    };

    fetch("send_receipt.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(emailData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert("Receipt sent successfully!");
        } else {
          alert("Error sending receipt: " + data.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  } else {
    alert(
      "Invalid input. Please type 'print' to print or enter a valid email."
    );
  }
}

function applyDiscount() {
  alert("Discount applied");
}
