<?php
include '../connection.php';

if(isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    
    // Get teachers for the specific subject
    $query = "SELECT TeacherID, TeacherName FROM teachers WHERE Subject = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $subject_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Generate options HTML
    echo '<option value="">Select teacher</option>';
    while($teacher = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $teacher['TeacherID'] . '">' . $teacher['TeacherName'] . '</option>';
    }
} else {
    echo '<option value="">Select teacher</option>';
}
?> 
