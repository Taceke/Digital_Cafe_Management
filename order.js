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
      productPrice.textContent = `ETB ${product.price}`;
      productPrice.style.fontSize = "12px";
      productPrice.style.color = "#333";

      let productStock = document.createElement("p");
      productStock.innerHTML = `Stock: <span id="stock-${product.id}">${product.quantity}</span>`;
      productStock.style.fontSize = "12px";
      productStock.style.color = "#ff0000";

      let addButton = document.createElement("button");
      addButton.textContent = "+";
      addButton.style.background = " #6a4c93";
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
    const quantity = parseInt(document.getElementById("quantity").value);
    const salesperson = document.getElementById("salesperson").value;
    const productId = popup.dataset.productId;
    const productPrice = popup.dataset.productPrice;
    const productName = popup.dataset.productName;
    const currentStock = parseInt(popup.dataset.productStock);

    if (!quantity || !salesperson) {
      alert("Please fill in all fields.");
      return;
    }

    if (quantity > currentStock) {
      alert("Not enough stock available!");
      return;
    }

    const total = quantity * productPrice;
    const orderList = document.getElementById("order-list");
    const row = document.createElement("tr");
    row.innerHTML = `
        <td>${productName}</td>
        <td>${productPrice}</td>
        <td>${quantity}</td>
        <td>${salesperson}</td>
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
    const priceText = row.cells[1] ? row.cells[1].innerText.replace("ETB ", "") : "";
    const quantityText = row.cells[2] ? row.cells[2].innerText : "";

    const price = parseFloat(priceText);
    const quantity = parseInt(quantityText);

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

  const paymentMethod = prompt("Enter Payment Method (Cash, Card, Mobile Pay):", "Cash");
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
              stockElement.innerText = Math.max(0, parseInt(stockElement.innerText) - item.quantity);
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
  document.getElementById("salesperson-input").value = ""; // Reset salesperson field
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

//FILTERS

// function pay() {
//   console.log("Pay function triggered!");

//   const orderItems = [];
//   document.querySelectorAll("#order-list tr").forEach((row) => {
//     const name = row.cells[0]?.innerText;
//     const price = parseFloat(row.cells[1]?.innerText.replace("$", ""));
//     const quantity = parseInt(row.cells[2]?.innerText);

//     if (!name || isNaN(price) || isNaN(quantity)) {
//       console.error("Invalid order data:", { name, price, quantity });
//       return;
//     }

//     orderItems.push({ name, price, quantity });
//   });

//   if (orderItems.length === 0) {
//     alert("No items in order. Please add products.");
//     return;
//   }

//   const paymentMethod = prompt(
//     "Enter Payment Method (Cash, Card, Mobile Pay):",
//     "Cash"
//   );
//   if (!paymentMethod) return;

//   const customerName = prompt("Enter Customer Name (Optional):", "Guest");

//   const requestData = {
//     items: orderItems,
//     payment_method: paymentMethod,
//     customer: customerName,
//   };

//   fetch("process_payment.php", {
//     method: "POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify(requestData),
//   })
//     .then((response) => response.json())
//     .then((data) => {
//       console.log("Server response:", data);

//       if (data.status === "success") {
//         alert(`Payment successful! Total: $${data.totalAmount}`);
//         document.getElementById("order-list").innerHTML = ""; // Clear order list
//         fetchReportData(); // Refresh report table
//       } else {
//         alert("Payment failed. Please try again.");
//       }
//     })
//     .catch((error) => {
//       console.error("Error:", error);
//       alert("An error occurred while processing payment.");
//     });
// }

// // document.addEventListener("DOMContentLoaded", function () {
// //   // Sample product data; replace with dynamic data if needed.
// //   const products = [
// //     { name: "Product 1", price: 25, img: "image1.png" },
// //     { name: "Product 2", price: 75, img: "image2.jpg" },
// //     { name: "Product 3", price: 20, img: "image3.jpg" },
// //     { name: "Product 4", price: 25, img: "image1.jpg" },
// //     { name: "Product 5", price: 75, img: "image2.jpg" },
// //     { name: "Product 6", price: 20, img: "image3.jpg" },

// //   ];

// //   const productList = document.getElementById("product-list");
// //   productList.innerHTML = "";
// //   products.forEach((product) => {
// //     let productDiv = document.createElement("div");
// //     productDiv.classList.add("product-item");
// //     productDiv.setAttribute("data-name", product.name);
// //     productDiv.innerHTML = `
// //       <img src="${product.img}" alt="${product.name}" />
// //       <p>${product.name}</p>
// //       <p>ETB ${product.price}</p>
// //       <button onclick="openPopup('${product.name}', ${product.price})">+</button>
// //     `;
// //     productList.appendChild(productDiv);
// //   });
// // });

// // Open popup modal and store product details for later use
// // function openPopup(productName, productPrice) {
// //   const popup = document.getElementById("popup");
// //   popup.classList.add("show-popup");
// //   document.querySelector("#popup .popup-content h3").innerText =
// //     "Add " + productName + " Details";
// //   popup.dataset.productPrice = productPrice;
// //   popup.dataset.productName = productName;
// // }

// // function closePopup() {
// //   document.getElementById("popup").classList.remove("show-popup");
// // }

// // function submitDetails() {
// //   const quantity = document.getElementById("quantity").value;
// //   const salesperson = document.getElementById("salesperson").value;
// //   const productPrice = document.getElementById("popup").dataset.productPrice;
// //   const productName = document.getElementById("popup").dataset.productName;

// //   if (quantity && salesperson) {
// //     const total = quantity * productPrice;
// //     const orderList = document.getElementById("order-list");
// //     const row = document.createElement("tr");
// //     row.innerHTML = `
// //       <td>${productName}</td>
// //       <td>${productPrice}</td>
// //       <td>${quantity}</td>
// //       <td>${salesperson}</td>
// //       <td>0</td>
// //       <td>${total}</td>
// //       <td><button onclick="removeItem(this)">X</button></td>
// //     `;
// //     // Insert new order at the top of the order list
// //     orderList.insertBefore(row, orderList.firstChild);
// //     closePopup();
// //   } else {
// //     alert("Please fill in all fields.");
// //   }
// // }

// //end popup function

// // function removeItem(button) {
// //   button.parentElement.parentElement.remove();
// // }

// // Dummy functions for order actions
// // STSRT UNDO FUNC
// // let orderHistory = []; // Stack to keep track of removed orders

// // function addToOrder(itemName, price) {
// //     let orderList = document.getElementById("order-list");
// //     let newRow = document.createElement("tr");

// //     newRow.innerHTML = `
// //         <td>${itemName}</td>
// //         <td>${price.toFixed(2)}</td>
// //         <td><input type="number" value="1" min="1" class="order-qty"></td>
// //         <td><input type="text" placeholder="Salesperson" class="salesperson-name"></td>
// //         <td>0.00</td>
// //         <td class="total-price">${price.toFixed(2)}</td>
// //         <td><button class="delete-btn" onclick="removeOrder(this)">X</button></td>
// //     `;

// //     orderList.appendChild(newRow);
// // }

// // function removeOrder(button) {
// //     let row = button.parentElement.parentElement; // Get the row
// //     let rowData = {
// //         itemName: row.cells[0].innerText,
// //         price: parseFloat(row.cells[1].innerText),
// //         quantity: row.cells[2].querySelector("input").value,
// //         salesperson: row.cells[3].querySelector("input").value,
// //         discount: row.cells[4].innerText,
// //         total: row.cells[5].innerText,
// //     };

// //     orderHistory.push(rowData); // Store removed order in history
// //     row.remove(); // Remove the row from the table
// // }

// // function undo() {
// //     if (orderHistory.length > 0) {
// //         let lastOrder = orderHistory.pop(); // Get last removed order
// //         let orderList = document.getElementById("order-list");
// //         let restoredRow = document.createElement("tr");

// //         restoredRow.innerHTML = `
// //             <td>${lastOrder.itemName}</td>
// //             <td>${lastOrder.price.toFixed(2)}</td>
// //             <td><input type="number" value="${lastOrder.quantity}" min="1" class="order-qty"></td>
// //             <td><input type="text" value="${lastOrder.salesperson}" class="salesperson-name"></td>
// //             <td>${lastOrder.discount}</td>
// //             <td class="total-price">${lastOrder.total}</td>
// //             <td><button class="delete-btn" onclick="removeOrder(this)">X</button></td>
// //         `;

// //         orderList.appendChild(restoredRow);
// //     } else {
// //         alert("No orders to undo!");
// //     }
// // }
// // END UNDO FUNCTION

// function clearAll() {
//   document.getElementById("order-list").innerHTML = "";
// }

// function applyDiscount() {
//   alert("Discount applied");
// }
// // PrSendEmail

// function printReceipt() {
//   let orderItems = document.querySelectorAll("#order-list tr");

//   if (orderItems.length === 0) {
//       alert("No items in the order.");
//       return;
//   }

//   let receiptContent = `
//       <html>
//       <head>
//           <title>Invoice</title>
//           <style>
//               body { font-family: Arial, sans-serif; text-align: center; }
//               h2 { margin-bottom: 5px; }
//               table { width: 100%; border-collapse: collapse; margin-top: 10px; }
//               th, td { border: 1px solid #000; padding: 8px; text-align: left; }
//               th { background-color: #f2f2f2; }
//               .total-row { font-weight: bold; }
//               .text-right { text-align: right; }
//           </style>
//       </head>
//       <body>
//           <h2>Cash Register Pro - Invoice</h2>
//           <hr>
//           <table>
//               <tr>
//                   <th>Item</th>
//                   <th>Qty</th>
//                   <th>Price</th>
//                   <th>Discount</th>
//                   <th>Tax</th>
//                   <th>Total</th>
//               </tr>
//   `;

//   let orderData = [];
//   let totalAmount = 0;
//   let taxRate = 10; // Example tax rate (10%)
//   let totalTax = 0;
//   let totalDiscount = 0;

//   orderItems.forEach(row => {
//       let itemName = row.cells[0].innerText;
//       let itemPrice = parseFloat(row.cells[1].innerText);
//       let itemQtyElement = row.cells[2].querySelector("input");
//       let itemQty = itemQtyElement ? parseInt(itemQtyElement.value) : 1;
//       let discount = parseFloat(row.cells[4]?.innerText) || 0; // Assuming discount is in column 4
//       let total = (itemPrice * itemQty) - discount;
//       let tax = (total * taxRate) / 100;

//       totalAmount += total;
//       totalTax += tax;
//       totalDiscount += discount;

//       receiptContent += `
//           <tr>
//               <td>${itemName}</td>
//               <td class="text-right">${itemQty}</td>
//               <td class="text-right">${itemPrice.toFixed(2)}</td>
//               <td class="text-right">${discount.toFixed(2)}</td>
//               <td class="text-right">${tax.toFixed(2)}</td>
//               <td class="text-right">${total.toFixed(2)}</td>
//           </tr>
//       `;

//       orderData.push({
//           name: itemName,
//           price: itemPrice,
//           quantity: itemQty,
//           discount: discount,
//           tax: tax,
//           total: total
//       });
//   });

//   let grandTotal = totalAmount + totalTax;

//   receiptContent += `
//       <tr class="total-row">
//           <td colspan="3" class="text-right">Subtotal</td>
//           <td class="text-right">${totalDiscount.toFixed(2)}</td>
//           <td class="text-right">${totalTax.toFixed(2)}</td>
//           <td class="text-right">${grandTotal.toFixed(2)}</td>
//       </tr>
//       </table>
//       <hr>
//       <p>Thank you for shopping with us!</p>
//       <script>window.print();</script>
//       </body></html>
//   `;

//   // Prompt the user
//   let action = prompt("Type 'print' to print the receipt or enter an email to send the receipt:");

//   if (!action) {
//       alert("Action canceled.");
//       return;
//   }

//   action = action.trim().toLowerCase();

//   if (action === "print") {
//       // Open a new window and print
//       let printWindow = window.open('', '', 'width=800,height=800');
//       printWindow.document.write(receiptContent);
//       printWindow.document.close();
//   } else if (action.includes("@")) {
//       // Send email
//       let emailData = {
//           email: action,
//           order: orderData,
//           subtotal: totalAmount,
//           tax: totalTax,
//           discount: totalDiscount,
//           total: grandTotal
//       };

//       fetch("send_receipt.php", {
//           method: "POST",
//           headers: { "Content-Type": "application/json" },
//           body: JSON.stringify(emailData)
//       })
//       .then(response => response.json())
//       .then(data => {
//           if (data.status === "success") {
//               alert("Receipt sent successfully!");
//           } else {
//               alert("Error sending receipt: " + data.message);
//           }
//       })
//       .catch(error => console.error("Error:", error));
//   } else {
//       alert("Invalid input. Please type 'print' to print or enter a valid email.");
//   }
// }

// function pay() {
//   console.log("Pay function triggered!");

//   const orderItems = [];
//   document.querySelectorAll("#order-list tr").forEach((row) => {
//     const name = row.cells[0]?.innerText;
//     const price = parseFloat(row.cells[1]?.innerText.replace("$", ""));
//     const quantity = parseInt(row.cells[2]?.innerText);

//     if (!name || isNaN(price) || isNaN(quantity)) {
//       console.error("Invalid order data:", { name, price, quantity });
//       return;
//     }

//     orderItems.push({ name, price, quantity });
//   });

//   if (orderItems.length === 0) {
//     alert("No items in order. Please add products.");
//     return;
//   }

//   const paymentMethod = prompt(
//     "Enter Payment Method (Cash, Card, Mobile Pay):",
//     "Cash"
//   );
//   if (!paymentMethod) return;

//   const customerName = prompt("Enter Customer Name (Optional):", "Guest");

//   const requestData = {
//     items: orderItems,
//     payment_method: paymentMethod,
//     customer: customerName,
//   };

//   fetch("process_payment.php", {
//     method: "POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify(requestData),
//   })
//     .then((response) => response.json())
//     .then((data) => {
//       console.log("Server response:", data);

//       if (data.status === "success") {
//         alert(`Payment successful! Total: $${data.totalAmount}`);
//         document.getElementById("order-list").innerHTML = ""; // Clear order list
//         fetchReportData(); // Refresh report table
//       } else {
//         alert("Payment failed. Please try again.");
//       }
//     })
//     .catch((error) => {
//       console.error("Error:", error);
//       alert("An error occurred while processing payment.");
//     });
// }

// // Fetch updated sales report data
// function fetchReportData() {
//   fetch("fetch_report.php")
//     .then((response) => response.text())
//     .then((data) => {
//       document.getElementById("reportData").innerHTML = data;
//     })
//     .catch((error) => console.error("Error fetching report:", error));
// }

// // Load report data on page load
// document.addEventListener("DOMContentLoaded", fetchReportData);

// // document.addEventListener("DOMContentLoaded", function () {
// //   const products = [
// //     {
// //       name: "Water (Liter)",
// //       price: 10,
// //       img: "images/water.jpg",
// //       category: "drinks",
// //     },
// //     {
// //       name: "Cola",
// //       price: 25,
// //       img: "images/cola.jpg",
// //       category: "soft drinks",
// //     },
// //     {
// //       name: "Cappuccino",
// //       price: 35,
// //       img: "images/cappuccino.jpg",
// //       category: "hot drinks",
// //     },
// //     { name: "Burger", price: 50, img: "images/burger.jpg", category: "food" },
// //     {
// //       name: "Espresso",
// //       price: 30,
// //       img: "images/espresso.jpg",
// //       category: "coffee",
// //     },
// //     {
// //       name: "Green Tea",
// //       price: 20,
// //       img: "images/green_tea.jpg",
// //       category: "tea",
// //     },

// //   ];

// //   function renderProducts(category = null) {
// //     const productList = document.getElementById("product-list");
// //     productList.innerHTML = "";

// //     const filteredProducts = category
// //       ? products.filter((p) => p.category === category)
// //       : products;

// //     filteredProducts.forEach((product) => {
// //       let productDiv = document.createElement("div");
// //       productDiv.classList.add("product-item");
// //       productDiv.innerHTML = `
// //               <img src="${product.img}" alt="${product.name}" />
// //               <p>${product.name}</p>
// //               <p>ETB ${product.price}</p>
// //               <button onclick="openPopup('${product.name}', ${product.price})">+</button>
// //           `;
// //       productList.appendChild(productDiv);
// //     });
// //   }

// //   window.filterCategory = function (category) {
// //     renderProducts(category);
// //   };

// //   renderProducts();
// // });

// //sdfghjkl FOR EMALI AND PRINT

// z

// //END FOREMAIL
// function openPopup(productName, productPrice) {
//   const popup = document.getElementById("popup");
//   popup.classList.add("show-popup");
//   document.querySelector("#popup .popup-content h3").innerText =
//     "Add " + productName + " Details";
//   popup.dataset.productPrice = productPrice;
//   popup.dataset.productName = productName;
// }

// function closePopup() {
//   document.getElementById("popup").classList.remove("show-popup");
// }

// function submitDetails() {
//   const quantity = document.getElementById("quantity").value;
//   const salesperson = document.getElementById("salesperson").value;
//   const productPrice = document.getElementById("popup").dataset.productPrice;
//   const productName = document.getElementById("popup").dataset.productName;

//   if (quantity && salesperson) {
//     const total = quantity * productPrice;
//     const orderList = document.getElementById("order-list");
//     const row = document.createElement("tr");
//     row.innerHTML = `
//           <td>${productName}</td>
//           <td>${productPrice}</td>
//           <td>${quantity}</td>
//           <td>${salesperson}</td>
//           <td>0</td>
//           <td>${total}</td>
//           <td><button onclick="removeItem(this)">X</button></td>
//       `;
//     orderList.insertBefore(row, orderList.firstChild);
//     closePopup();
//   } else {
//     alert("Please fill in all fields.");
//   }
// }

// function removeItem(button) {
//   button.parentElement.parentElement.remove();
// }

// function renderProducts(category = null) {
//   const productList = document.getElementById("products");
//   productList.innerHTML = "";

//   const filteredProducts = category
//     ? products.filter((p) => p.category === category)
//     : products;

//   filteredProducts.forEach((product) => {
//     let productDiv = document.createElement("div");
//     productDiv.classList.add("product-item");
//     productDiv.innerHTML = `
//           <img src="${product.img}" alt="${product.name}" />
//           <p>${product.name}</p>
//           <p>ETB ${product.price}</p>
//           <button onclick="openPopup('${product.name}', ${product.price})">+</button>
//       `;
//     productList.appendChild(productDiv);
//   });
// }

// //bbbbbbbbbmmmmmmmmmmmmmmmmmmmmmmmdfghjk

// //vnhhhhhhhhhhhhhhhhhhhhhhhhhhhh
// document.addEventListener("DOMContentLoaded", function () {
//   const products = [
//     {
//       name: "Water (Liter)",
//       price: 10,
//       img: "images/water.jpg",
//       category: "drinks",
//     },
//     { name: "Cola", price: 25, img: "images/cola.jpg", category: "drinks" },
//     {
//       name: "Cappuccino",
//       price: 35,
//       img: "images/cappuccino.jpg",
//       category: "drinks",
//     },
//     { name: "Burger", price: 50, img: "images/burger.jpg", category: "food" },
//     {
//       name: "Espresso",
//       price: 30,
//       img: "images/espresso.jpg",
//       category: "food",
//     },
//     {
//       name: "Green Tea",
//       price: 20,
//       img: "images/green_tea.jpg",
//       category: "others",
//     },
//   ];

//   function renderProducts(category) {
//     const productList = document.getElementById("product-list");
//     productList.innerHTML = "";

//     const filteredProducts =
//       category === "all"
//         ? products
//         : products.filter((p) => p.category === category);

//     filteredProducts.forEach((product) => {
//       let productDiv = document.createElement("div");
//       productDiv.classList.add("product-item");
//       productDiv.innerHTML = `
//               <img src="${product.img}" alt="${product.name}" />
//               <p>${product.name}</p>
//               <p>ETB ${product.price}</p>
//               <button onclick="openPopup('${product.name}', ${product.price})">+</button>
//           `;
//       productList.appendChild(productDiv);
//     });
//   }

//   window.filterProducts = function (category) {
//     renderProducts(category);
//   };

//   renderProducts("all");
// });

// function openPopup(productName, productPrice) {
//   const popup = document.getElementById("popup");
//   popup.classList.add("show-popup");
//   document.querySelector("#popup .popup-content h3").innerText =
//     "Add " + productName + " Details";
//   popup.dataset.productPrice = productPrice;
//   popup.dataset.productName = productName;
// }

// function closePopup() {
//   document.getElementById("popup").classList.remove("show-popup");
// }

// function submitDetails() {
//   const quantity = document.getElementById("quantity").value;
//   const salesperson = document.getElementById("salesperson").value;
//   const productPrice = document.getElementById("popup").dataset.productPrice;
//   const productName = document.getElementById("popup").dataset.productName;

//   if (quantity && salesperson) {
//     const total = quantity * productPrice;
//     const orderList = document.getElementById("order-list");
//     const row = document.createElement("tr");
//     row.innerHTML = `
//           <td>${productName}</td>
//           <td>${productPrice}</td>
//           <td>${quantity}</td>
//           <td>${salesperson}</td>
//           <td>0</td>
//           <td>${total}</td>
//           <td><button onclick="removeItem(this)">X</button></td>
//       `;
//     orderList.insertBefore(row, orderList.firstChild);
//     closePopup();
//   } else {
//     alert("Please fill in all fields.");
//   }
// }

// function removeItem(button) {
//   button.parentElement.parentElement.remove();
// }

// //asdfghjklhjkfor
// document.addEventListener("DOMContentLoaded", function () {
//   fetchChartData();
// });

// function fetchChartData() {
//   fetch("fetch_chart_data.php")
//     .then((response) => response.json())
//     .then((data) => {
//       createPieChart(data.sales, data.orders, data.salespersons);
//     })
//     .catch((error) => console.error("Error fetching chart data:", error));
// }

// function createPieChart(sales, orders, salespersons) {
//   const ctx = document.getElementById("salesChart").getContext("2d");

//   const total = sales + orders + salespersons;
//   const dataValues = [sales, orders, salespersons];
//   const colors = ["orange", "yellow", "tomato"];

//   new Chart(ctx, {
//     type: "pie",
//     data: {
//       labels: ["Total Sales", "Total Orders", "Total Salespersons"],
//       datasets: [
//         {
//           data: dataValues,
//           backgroundColor: colors,
//         },
//       ],
//     },
//     options: {
//       responsive: true,
//       plugins: {
//         legend: { position: "bottom" },
//         datalabels: {
//           color: "black",
//           font: { weight: "bold", size: 14 },
//           formatter: (value) => {
//             let percentage = ((value / total) * 100).toFixed(1);
//             return `${percentage}%`;
//           },
//         },
//       },
//     },
//     plugins: [ChartDataLabels],
//   });
// }
