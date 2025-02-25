<?php 
include 'db_connect.php';
include './templates/header.php';
$message = "";

// Update product (including quantity)
if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantity = intval($_POST['quantity']); // Get quantity

    $sql = "UPDATE product_added 
            SET name='$name', category='$category', price='$price', description='$description', quantity='$quantity' 
            WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        $message = "<p class='success'>Product updated successfully!</p>";
    } else {
        $message = "<p class='error'>Error updating product: " . $conn->error . "</p>";
    }
}

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM product_added WHERE id=$id") === TRUE) {
        $message = "<p class='success'>Product deleted successfully!</p>";
    } else {
        $message = "<p class='error'>Error deleting product: " . $conn->error . "</p>";
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM product_added");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Manage Products</h2>
    <?php echo $message; ?>
    
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['quantity']; ?></td> <!-- Added Quantity -->
                <td><?php echo $row['description']; ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $row['id']; ?>" style="background: green; padding: 5px; text-decoration: none; color: white;">Edit</a>
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" style="background: red; padding: 5px; text-decoration: none; color: white;">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
