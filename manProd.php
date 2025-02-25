<?php
include 'db_connect.php'; // Database connection
session_start();

// Add product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
    
    $sql = "INSERT INTO products (name, category, price, image) VALUES ('$name', '$category', '$price', '$image')";
    $conn->query($sql);
}

// Delete product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
}

// Fetch all products
$result = $conn->query("SELECT * FROM products");

// Update profile
if (isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_id = $_SESSION['user_id'];
    
    $sql = "UPDATE users SET full_name='$full_name', username='$username', password='$password' WHERE id='$user_id'";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products & Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Manage Products</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="file" name="image" required>
        <button type="submit" name="add_product">Add Product</button>
    </form>
    
    <h3>Product List</h3>
    <table>
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    
    <h2>Update Profile</h2>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="New Password" required>
        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</body>
</html>
