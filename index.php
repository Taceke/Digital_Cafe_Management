<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
unset($_SESSION['error']); // Clear error after displaying
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>
    <link rel="stylesheet" href="log.css">
</head>
<body>
    
    <div class="login-container" style="align-items: center;">
        <h2>Login System</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input  type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="forgot-container">
                <a href="forgot_password.php" class="forgot">Forgot password</a>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
<script src="log.js"></script>
</body>
</html>
