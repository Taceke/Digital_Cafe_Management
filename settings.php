<?php
include 'db_connect.php';


$message = "";
// Fetch existing company info
$result = $conn->query("SELECT * FROM company_info WHERE id=1");
$company = $result->fetch_assoc();

// Update company info
if (isset($_POST['update_company'])) {
    $name = $conn->real_escape_string($_POST['company_name']);
    $charge = $conn->real_escape_string($_POST['charge_amount']);
    $vat = $conn->real_escape_string($_POST['vat_charge']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $country = $conn->real_escape_string($_POST['country']);
    $currency = $conn->real_escape_string($_POST['currency']);

    $sql = "UPDATE company_info SET company_name='$name', charge_amount='$charge', vat_charge='$vat', address='$address', phone='$phone', country='$country', currency='$currency' WHERE id=1";
    
    if ($conn->query($sql) === TRUE) {
        $message = "<p class='success'>Company info updated successfully!</p>";
    } else {
        $message = "<p class='error'>Error updating: " . $conn->error . "</p>";
    }
}
?>

<?php

include './templates/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="settings.css">
 
    <!-- <script src="company_info.js" defer></script> -->   
</head>

<body>

    <h2>Company Information</h2>
    <?php echo $message; ?>
    <form method="POST">
        <label>Company Name:</label>
        <input type="text" name="company_name" value="<?php echo $company['company_name']; ?>" required>
        
        <label>Charge Amount (%):</label>
        <input type="number" name="charge_amount" value="<?php echo $company['charge_amount']; ?>" required>
        
        <label>VAT Charge (%):</label>
        <input type="number" name="vat_charge" value="<?php echo $company['vat_charge']; ?>" required>
        
        <label>Address:</label>
        <textarea name="address" required><?php echo $company['address']; ?></textarea>
        
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $company['phone']; ?>" required>
        
        <label>Country:</label>
        <input type="text" name="country" value="<?php echo $company['country']; ?>" required>
        
        <label for="currency">Currency:</label>
    <select id="currency" name="currency">
       <option value="ETB">ETB</option>
        <option value="USD">USD</option>
        <option value="EUR">EUR</option>
        <option value="GBP">GBP</option>
        <option value="INR">INR</option>
    </select>
        <!-- <input type="text" name="currency" value="<?php echo $company['currency']; ?>" required> -->
        
        <button type="submit" name="update_company">Update Company Info</button>
    </form>
    <script src="settings.js"></script>
    
 
</body>
</html>
