<?php
session_start();
include 'db_connect.php';

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user profile
$query = "SELECT * FROM user_profiles WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Default profile image path
$profile_image = "uploads/default.jpg";

// If a profile image exists in the database, update the path
if (!empty($user['profile_image']) && file_exists("uploads/" . $user['profile_image'])) {
    $profile_image = "uploads/" . $user['profile_image'];
}

// Force image refresh to avoid caching issues
$profile_image .= "?t=" . time();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = $_POST['gender'];
    $phone_number = trim($_POST['phone_number']);

    // Default to existing profile image
    $new_profile_image = $user['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            header("Location: profile.php?error=Invalid file type. Only JPG, PNG, and GIF are allowed.");
            exit();
        }

        // Check if file is an actual image
        if (getimagesize($_FILES["profile_image"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $new_profile_image = $file_name; // Update profile image
            } else {
                header("Location: profile.php?error=Error uploading the file.");
                exit();
            }
        } else {
            header("Location: profile.php?error=File is not a valid image.");
            exit();
        }
    }

    if ($result->num_rows > 0) {
        // Update existing profile
        $query = "UPDATE user_profiles SET first_name = ?, last_name = ?, gender = ?, phone_number = ?, profile_image = ? WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $first_name, $last_name, $gender, $phone_number, $new_profile_image, $username);
    } else {
        // Insert new profile if it doesn't exist
        $query = "INSERT INTO user_profiles (username, first_name, last_name, gender, phone_number, profile_image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $username, $first_name, $last_name, $gender, $phone_number, $new_profile_image);
    }

    if ($stmt->execute()) {
        // Update session with the new image
        $_SESSION['profile_image'] = $new_profile_image;

        header("Location: profile.php?success=Profile updated successfully");
        exit();
    } else {
        header("Location: profile.php?error=Error updating profile");
        exit();
    }
}

// Ensure session has updated profile image
if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) && file_exists("uploads/" . $_SESSION['profile_image'])) {
    $profile_image = "uploads/" . $_SESSION['profile_image'];
}

// Force browser to refresh the updated image
$profile_image .= "?t=" . time();
?>

<!-- Display Profile Image -->
<img src="<?php echo $profile_image; ?>" alt="Profile Image" width="150" height="150">
