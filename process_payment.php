<?php
session_start();
include 'db_connect.php'; // Ensure this connects to your database

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch company info for charge and VAT
    $companyResult = $conn->query("SELECT * FROM company_info WHERE id=1");
    $company = $companyResult->fetch_assoc();

    $chargeAmount = $company['charge_amount'];  // Get the charge amount
    $vatCharge = $company['vat_charge'];  // Get VAT charge
    $companyId = $company['id']; // Get company ID
    
    $orderData = json_decode(file_get_contents("php://input"), true);

    if (!isset($_SESSION['username']) || empty($orderData['items'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request"]);
        exit();
    }

    $salesperson = $_SESSION['username'];
    $totalAmount = 0;
    $totalQty = 0;
    $totalDiscount = 0;
    $totalTax = 0;
    $paymentMethod = $orderData['payment_method'] ?? 'Cash';
    $customer = $orderData['customer'] ?? 'Guest'; // Default customer name

    $conn->begin_transaction(); // Start transaction

    try {
        foreach ($orderData['items'] as $item) {
            $product_name = $item['name'];
            $price = $item['price'];
            $quantity = $item['quantity'];

            // Apply VAT to the price for each item
            $itemTax = ($price * $quantity) * ($vatCharge / 100); // Calculate VAT for each item
            $total = ($price * $quantity) + $itemTax + $chargeAmount; // Total price with VAT and charge

            $totalAmount += $total;
            $totalQty += $quantity;
            $totalTax += $itemTax;

            // Insert into sales table
            $stmt = $conn->prepare("INSERT INTO sales (product_name, price, quantity, salesperson, total, date) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sdiss", $product_name, $price, $quantity, $salesperson, $total);
            $stmt->execute();
            $stmt->close();

            // Update stock in product table
            $updateStmt = $conn->prepare("UPDATE product_added SET quantity = quantity - ? WHERE name = ?");
            $updateStmt->bind_param("is", $quantity, $product_name);
            $updateStmt->execute();
            $updateStmt->close();

            // Fetch the raw materials required for this product
            $ingredientQuery = $conn->prepare("
                SELECT material_id, quantity_required 
                FROM product_ingredients 
                WHERE product_id = (SELECT id FROM product_added WHERE name = ?)
            ");
            $ingredientQuery->bind_param("s", $product_name);
            $ingredientQuery->execute();
            $ingredientsResult = $ingredientQuery->get_result();

            while ($ingredient = $ingredientsResult->fetch_assoc()) {
                $material_id = $ingredient['material_id'];
                $required_qty = $ingredient['quantity_required'] * $quantity; // Calculate total needed amount

                // Deduct raw materials from inventory
                $updateMaterialStmt = $conn->prepare("UPDATE raw_materials SET quantity = quantity - ? WHERE id = ?");
                $updateMaterialStmt->bind_param("di", $required_qty, $material_id);
                $updateMaterialStmt->execute();
                $updateMaterialStmt->close();
            }

            $ingredientQuery->close();
        }

        // Insert summary into sales_report
        $stmt = $conn->prepare("INSERT INTO sales_report (customer, qty, order_date, salesperson, payment_method, discount, total_amount, company_id, charge_amount, vat_charge) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissddddd", $customer, $totalQty, $salesperson, $paymentMethod, $totalDiscount, $totalAmount, $companyId, $chargeAmount, $totalTax);
        $stmt->execute();
        $stmt->close();

        $conn->commit(); // Commit transaction

        echo json_encode(["status" => "success", "message" => "Payment successful", "totalAmount" => $totalAmount, "items" => $orderData['items']]);
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction if an error occurs
        error_log("Payment error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Transaction failed"]);
    }

    exit();
}
?>
