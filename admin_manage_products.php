<?php 
include 'db_connect.php';

$message = "";

if (isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $quantity = (int)$_POST['quantity']; 
    $unit = $conn->real_escape_string($_POST['unit']);

    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO product_added (name, category, price, image, description, quantity, unit) 
                VALUES ('$name', '$category', '$price', '$image', '$description', '$quantity', '$unit')";

        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success text-center'>✅ Product added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>❌ Error: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>⚠️ Failed to upload image.</div>";
    }
}
?>

<?php include './templates/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Manage Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e2e6ea);
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }

        #previewImage {
            display: none;
            max-width: 150px;
            border-radius: 8px;
            border: 2px dashed #ccc;
            padding: 6px;
        }

        label {
            font-weight: 600;
            font-size: 1rem;
            color: #6a11cb;
            text-align: center;
            display: block;
            margin-bottom: 6px;
        }

        .form-floating > label {
            color: #6a11cb !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            left: 50%;
            transform: translateX(-50%);
            padding-left: 0;
        }

        select.form-select,
        input[type="file"] {
            text-align: center;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #6a11cb;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.15);
            letter-spacing: 1px;
        }

        button.btn-primary {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            border: none;
        }

        button.btn-primary:hover {
            background: linear-gradient(90deg, #2575fc, #6a11cb);
        }
    </style>
</head>
<body>

<div class="text-center my-4">
    <h1 class="section-title">📦 Add New Product</h1>
</div>

<div class="container">
    <div class="card p-4 mb-5">
        <h4 class="mb-3 fw-bold text-primary text-center">Product Form</h4>

        <?php echo $message; ?>
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-12 mb-3 form-floating">
                    <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Product Name" required>
                    <label for="name">Product Name</label>
                </div>

                <div class="col-12 mb-3 form-floating">
                    <input type="text" class="form-control form-control-lg" id="category" name="category" placeholder="Category" required>
                    <label for="category">Category</label>
                </div>

                <div class="col-12 mb-3 form-floating">
                    <input type="number" class="form-control form-control-lg" id="price" name="price" placeholder="Price" required>
                    <label for="price">Price</label>
                </div>

                <div class="col-12 mb-3 form-floating">
                    <input type="number" class="form-control form-control-lg" id="quantity" name="quantity" placeholder="Quantity" required>
                    <label for="quantity">Quantity</label>
                </div>

                <div class="col-12 mb-3 text-center">
                    <label for="unit">Unit</label>
                    <select name="unit" id="unit" class="form-select form-select-lg" required>
                        <option value="">Select Unit</option>
                        <option value="unit">per ptoduct</option>

                        <option value="unit">per unit</option>

                        <option value="kg">Kilogram (kg)</option>
                        <option value="L">Liter (L)</option>
                        <option value="ml"> miliLiter (ml)</option>

                        <option value="loaf">Loaf</option>
                        <option value="piece">Piece</option>
                        <option value="pack">Pack</option>
                    </select>
                </div>

                <div class="col-12 mb-3 text-center">
                    <label for="imageUpload">Product Image</label>
                    <input type="file" class="form-control form-control-lg" name="image" id="imageUpload" accept="image/*" required>
                    <img id="previewImage" alt="Image Preview" class="mt-2">
                </div>

                <div class="col-12 mb-4 form-floating">
                    <textarea class="form-control form-control-lg" id="description" name="description" placeholder="Product Description" style="height: 120px;" required></textarea>
                    <label for="description">Product Description</label>
                </div>

                <div class="col-12 d-grid">
                    <button type="submit" name="add_product" class="btn btn-primary btn-lg shadow">➕ Add Product</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Bootstrap validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Image preview
    document.getElementById('imageUpload').addEventListener('change', function(event) {
        const preview = document.getElementById('previewImage');
        const file = event.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
</script>

</body>
</html>
