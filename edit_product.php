<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM product_added WHERE id=$id");
    $product = $result->fetch_assoc();
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantity = intval($_POST['quantity']); // Added quantity

    $sql = "UPDATE product_added 
            SET name='$name', category='$category', price='$price', description='$description', quantity='$quantity' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Product updated successfully!</p>";
    } else {
        echo "<p class='error'>Error updating product: " . $conn->error . "</p>";
    }
}
?>
<?php
 include './templates/header.php';
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="addP.css">
</head>
<body>
    <h2>Edit Product</h2>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
        <input type="text" name="category" value="<?php echo $product['category']; ?>" required>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
        <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required> <!-- Added Quantity -->
        <textarea name="description" required><?php echo $product['description']; ?></textarea>
        <button type="submit" name="update_product">Update Product</button>
    </form>

</body>
</html>
