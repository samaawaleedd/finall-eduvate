<?php
session_start();
include "../../connection.php";
include "../chat_system.php";

if (!isset($_SESSION['ParentID']) && !isset($_SESSION['TeacherID'])) {
    die(json_encode(['error' => 'Not authenticated']));
}

$chatId = $_GET['chat_id'] ?? 0;

// Get the last message timestamp from the session
$lastMessageTime = $_SESSION['last_message_time_' . $chatId] ?? 0;

// Get new messages
$query = "SELECT COUNT(*) as count FROM messages 
          WHERE ChatID = ? AND DateTime > FROM_UNIXTIME(?)";
$stmt = $connect->prepare($query);
$stmt->bind_param("ii", $chatId, $lastMessageTime);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Update last message time
$_SESSION['last_message_time_' . $chatId] = time();

echo json_encode(['hasNewMessages' => $row['count'] > 0]); 