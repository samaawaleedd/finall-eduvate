<?php
session_start();
include "../../connection.php";
include "../chat_system.php";

if (!isset($_SESSION['ParentID']) && !isset($_SESSION['TeacherID'])) {
    die(json_encode(['error' => 'Not authenticated']));
}

$chatId = $_POST['chat_id'] ?? 0;
$message = $_POST['message'] ?? '';
$media = null;

// Handle file upload if present
if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['media']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['media']['tmp_name'], $targetPath)) {
        $media = $fileName;
    }
}

// Determine sender type
$sender = isset($_SESSION['ParentID']) ? 'Parent' : 'Teacher';
$senderId = isset($_SESSION['ParentID']) ? $_SESSION['ParentID'] : $_SESSION['TeacherID'];

// Insert message
$result = insertMessage($chatId, $sender, $senderId, $message, $media, $connect);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to send message']);
} 