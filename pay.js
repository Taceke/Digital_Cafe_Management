document.addEventListener("DOMContentLoaded", function () {
    let selectedProductRow = null;

    // Add product to order list when clicked
    window.addToOrder = function (productName, price) {
        const orderList = document.getElementById("order-list");

        // Check if item already exists
        let existingRow = [...orderList.rows].find(row => row.dataset.name === productName);
        if (existingRow) {
            alert("Product already added! Update quantity instead.");
            return;
        }

        let newRow = document.createElement("tr");
        newRow.dataset.name = productName;
        newRow.dataset.price = price;

        newRow.innerHTML = `
            <td>${productName}</td>
            <td>${price}</td>
            <td class="qty">1</td>
            <td class="salesperson"></td>
            <td class="discount">0</td>
            <td class="total">${price}</td>
            <td><button onclick="removeItem(this)">X</button></td>
        `;

        newRow.onclick = () => showPopup(newRow);
        orderList.appendChild(newRow);
    };

    // Show popup for entering quantity and salesperson
    window.showPopup = function (row) {
        selectedProductRow = row;
        document.getElementById("popup").style.display = "block";
    };

    // Submit quantity and salesperson details
    window.submitDetails = function () {
        const quantity = document.getElementById("quantity").value;
        const salesperson = document.getElementById("salesperson").value;

        if (!quantity || quantity <= 0 || !salesperson) {
            alert("Please enter valid quantity and salesperson name!");
            return;
        }

        if (selectedProductRow) {
            selectedProductRow.querySelector(".qty").innerText = quantity;
            selectedProductRow.querySelector(".salesperson").innerText = salesperson;

            const price = parseFloat(selectedProductRow.dataset.price);
            selectedProductRow.querySelector(".total").innerText = (price * quantity).toFixed(2);
        }

        document.getElementById("popup").style.display = "none";
    };

    // Remove item from order list
    window.removeItem = function (button) {
        button.closest("tr").remove();
    };

    // Close popup
    window.closePopup = function () {
        document.getElementById("popup").style.display = "none";
    };

    // Process payment
    window.pay = function () {
        const orderItems = [];
        document.querySelectorAll("#order-list tr").forEach(row => {
            const name = row.dataset.name;
            const price = parseFloat(row.dataset.price);
            const quantity = parseInt(row.querySelector(".qty").innerText);
            const salesperson = row.querySelector(".salesperson").innerText;
            const total = parseFloat(row.querySelector(".total").innerText);

            if (name && quantity > 0 && salesperson) {
                orderItems.push({ name, price, quantity, salesperson, total });
            }
        });

        if (orderItems.length === 0) {
            alert("No valid items to process payment!");
            return;
        }

        fetch("process_payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ orderItems })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Order completed successfully!");
                document.getElementById("order-list").innerHTML = ""; // Clear order list
            } else {
                alert("Error processing payment: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    };
});
