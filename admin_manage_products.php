<?php 
include 'db_connect.php';

$message = ""; // Variable to store success/error message

// Add product
if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantity = (int)$_POST['quantity']; // Get quantity from the form

    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO product_added (name, category, price, image, description, quantity) 
                VALUES ('$name', '$category', '$price', '$image', '$description', '$quantity')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<p class='success'>Product added successfully!</p>";
        } else {
            $message = "<p class='error'>Error adding product: " . $conn->error . "</p>";
        }
    } else {
        $message = "<p class='error'>Failed to upload image.</p>";
    }
}


 ?>
<?php
 include './templates/header.php';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="addP.css">
    <script src="script.js" defer></script>
</head>
<body>
    <h2>Manage Products</h2>
    
    <!-- Display Message -->
    <?php echo $message; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <textarea name="description" placeholder="Product Description" required></textarea>
        <input type="file" name="image" id="imageUpload" required>
        <img id="previewImage" src="" alt="Image Preview" style="display:none; width: 100px;">
        <button type="submit" name="add_product">Add Product</button>
    </form>

   
</body>
</html>
