<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>CMN System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Background gradient */
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 800px; /* Almost full width */
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 0; /* Perfect rectangle */
            padding: 20px 40px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2 class="text-center mb-4">CMN System</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="col-12 text-end">
                <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
