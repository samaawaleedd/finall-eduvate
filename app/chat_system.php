<?php
// Function to get messages for a specific chat
function getMessages($chatId, $conn) {
    $sql = "SELECT * FROM messages 
            WHERE ChatID = ? 
            AND IsDeleted = 'False'
            ORDER BY DateTime ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $chatId);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Function to get or create a chat
function getOrCreateChat($teacherId, $parentId, $conn) {
    // Check if chat exists
    $sql = "SELECT ChatID FROM chat 
            WHERE TeacherID = ? AND ParentID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $teacherId, $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $chat = mysqli_fetch_assoc($result);
        return $chat['ChatID'];
    }
    
    // Create new chat if it doesn't exist
    $sql = "INSERT INTO chat (TeacherID, ParentID) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $teacherId, $parentId);
    mysqli_stmt_execute($stmt);
    
    return mysqli_insert_id($conn);
}

// Function to mark messages as seen
function markMessagesAsSeen($chatId, $conn) {
    $sql = "UPDATE messages 
            SET Seen = 'Seen' 
            WHERE ChatID = ? 
            AND Seen = 'Unseen'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $chatId);
    mysqli_stmt_execute($stmt);
}

// Function to get unread message count
function getUnreadCount($chatId, $conn) {
    $sql = "SELECT COUNT(*) as count 
            FROM messages 
            WHERE ChatID = ? 
            AND Seen = 'Unseen'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $chatId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// Function to check if parent is subscribed
function isParentSubscribed($parentId, $conn) {
    $sql = "SELECT Is_subscribed FROM parents WHERE ParentID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['Is_subscribed'] == 1;
}

// Function to get parent details
function getParent($parentId, $conn) {
    $sql = "SELECT * FROM parents WHERE ParentID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $parentId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Function to get teacher details
function getTeacher($teacherId, $conn) {
    $sql = "SELECT * FROM teachers WHERE TeacherID = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $teacherId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?> 