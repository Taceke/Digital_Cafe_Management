<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password/Username</title>
    <link rel="stylesheet" href="forP.css"> <!-- Link to the existing CSS -->
</head>
<body>
    <div class="login-container">
        <h2>Reset Username/Password</h2>
        <p>Enter your current username and provide new details to reset your username or password.</p>
        <form action="forgot_password.php" method="POST">
            <div class="input-group">
                <label for="username">Current Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="new_username">New Username (optional)</label>
                <input type="text" id="new_username" name="new_username">
            </div>
            <div class="input-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn">Reset</button>
        </form>
        
        <?php
        include 'db_connect.php';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $new_username = $_POST['new_username'];
            $new_password = $_POST['new_password'];
            
            // Check if the current username exists
            $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // If a new username is provided, update it, else leave it as is
                if (!empty($new_username)) {
                    $update_stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
                    $update_stmt->bind_param("sss", $new_username, $hashed_password, $username);
                } else {
                    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                    $update_stmt->bind_param("ss", $hashed_password, $username);
                }
                
                $update_stmt->execute();
                
                echo "<p class='success'>Your username and/or password has been successfully updated.</p>";
            } else {
                echo "<p class='error'>Username not found.</p>";
            }
            
            $stmt->close();
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
