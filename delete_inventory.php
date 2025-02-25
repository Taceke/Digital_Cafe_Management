<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !isset($_GET['type'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request parameters']);
    exit;
}

$id = intval($_GET['id']);
$type = $_GET['type'];

// Determine table and column names
if ($type === 'material') {
    $table = 'raw_materials';
} elseif ($type === 'equipment') {
    $table = 'equipment';
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid type']);
    exit;
}

// Delete query
$sql = "DELETE FROM $table WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
