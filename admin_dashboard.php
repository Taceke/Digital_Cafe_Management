<?php 
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<?php
include './templates/header.php';
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Cash Register Pro</title> -->
    <link rel="stylesheet" href="page.css">
    <style>
.popup-buttons button {
    font-size: 18px;
    padding: 10px 20px;
    margin: 8px;
    border-radius: 6px;
    background-color: #6a4c93;
    color: white;
    border: none;
    cursor: pointer;
}
.popup-buttons button:hover {
    background-color: #5a3d7e;
}
.popup-content {
    width: 300px; /* or more */
    padding: 20px;
    border-radius: 10px;
    background-color: white;
}

</style>


</head>

        <main>
            <section class="order-section">
                <table>
                    <thead class="Items">
                        <tr>
                            <th>Items</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>SalName</th>
                            <th>Total Disc</th>
                            <th>Total</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody id="order-list">
                        <!-- Order items will be added dynamically -->
                    </tbody>
                </table>
                <div class="order-actions">
                    <button onclick="undo()">Undo</button>
                    <button onclick="clearAll()">Clear All</button>
                    <button onclick="applyDiscount()">Discount</button>
                    <button onclick="printReceipt()">Print or Send Email</button>
                    <button  onclick="pay()">Pay</button>
                </div>
            </section>

            <section class="product-section">
                <div class="product-controls">
                    <!-- <span>
                        Sort by:
                        <select>
                            <option>Product Name</option>
                        </select>
                    </span> -->
                </div>

                <div class="product-list" id="product-list">
                    <!-- Products will be dynamically added here -->
                </div>
                
                <div class="category-buttons">
                <button onclick="filterProducts('All')">All</button>   
               <button onclick="filterProducts('Drinks')">Drinks</button>
               <button onclick="filterProducts('Food')">Food</button>
               <button onclick="filterProducts('Others')">Others</button>


                </div>
            </section>
        </main>
    </div>

    <div id="popup" class="popup">
        <div class="popup-content">
            <h3>Enter Details</h3>
            <label for="quantity">Quantity: </label>
            <input type="number" id="quantity" min="0.01" step="0.01" required>
            <!-- <label for="salesperson">Salesperson Name:</label>
            <input type="text" id="salesperson" required> -->
            <div class="popup-buttons">
                <button onclick="submitDetails()">Submit</button>
                <button onclick="closePopup()">Cancel</button>
            </div>
        </div>
        <script src="order.js" defer></script>

        
</body>
</html>
