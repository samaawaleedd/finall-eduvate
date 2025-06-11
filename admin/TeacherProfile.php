<?php
include '../connection.php';


// Check if user is logged in and is an admin
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: AllTeachers.php");
    exit();
}

$teacherId = mysqli_real_escape_string($connect, $_GET['id']);
$currentAdminId = $_SESSION['AdminID'];

// Fetch current admin's role
$currentAdminQuery = "SELECT RoleId FROM admins WHERE AdminID = '$currentAdminId'";
$currentAdminResult = mysqli_query($connect, $currentAdminQuery);
$currentAdmin = mysqli_fetch_assoc($currentAdminResult);
$isAdministrator = ($currentAdmin['RoleId'] == 1);

// Fetch teacher information
$query = "SELECT t.*, s.SubjectName, r.RoleTitle 
          FROM teachers t 
          LEFT JOIN subjects s ON t.Subject = s.SubjectID 
          LEFT JOIN roles r ON t.RoleId = r.RoleID 
          WHERE t.TeacherID = '$teacherId'";
$result = mysqli_query($connect, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: AllTeachers.php");
    exit();
}

$teacher = mysqli_fetch_assoc($result);

// Handle form submission for updating teacher information
if (isset($_POST['update'])) {
    // Only allow administrators to update profiles
    if (!$isAdministrator) {
        $error = "You don't have permission to update teacher profiles.";
    } else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $phone = mysqli_real_escape_string($connect, $_POST['phone']);
        $subject = mysqli_real_escape_string($connect, $_POST['subject']);
        $role = mysqli_real_escape_string($connect, $_POST['role']);
        
        // Handle image upload
        $image = $teacher['TeacherPic']; // Default to current image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $newImage = uniqid() . "." . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], "../Media/" . $newImage)) {
                    $image = $newImage;
                }
            }
        }
        
        // Update query
        $update = "UPDATE teachers SET 
                   TeacherName = '$name',
                   TeacherEmail = '$email',
                   TeacherNumber = '$phone',
                   Subject = '$subject',
                   RoleId = '$role',
                   TeacherPic = '$image'
                   WHERE TeacherID = '$teacherId'";
                   
        if (mysqli_query($connect, $update)) {
            $success = "Teacher information updated successfully!";
            // Refresh teacher data
            $result = mysqli_query($connect, $query);
            $teacher = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating teacher information: " . mysqli_error($connect);
        }
    }
}

// Fetch all subjects for the dropdown
$subjectsQuery = "SELECT * FROM subjects ORDER BY SubjectName";
$subjects = mysqli_query($connect, $subjectsQuery);

// Fetch all roles for the dropdown
$rolesQuery = "SELECT * FROM roles WHERE RoleID IN (3, 4)"; // Only Teacher and Supervisor roles
$roles = mysqli_query($connect, $rolesQuery);

// Add schedule data
$periods = [
    1 => ['8:00:00', '8:45:00'],
    2 => ['8:45:00', '9:30:00'],
    3 => ['9:30:00', '10:15:00'],
    4 => ['10:15:00', '11:00:00'],
    'Break' => ['11:00:00', '11:30:00'],
    5 => ['11:30:00', '12:15:00'],
    6 => ['12:15:00', '13:00:00'],
    7 => ['13:00:00', '13:45:00']
];

$scheduleQuery = "SELECT sd.Weekday, sd.PeriodNumber, 
                 CONCAT(sch.grade, c.ClassName) AS GradeClass, 
                 s.SubjectName
                 FROM s_details sd
                 JOIN schedule sch ON sd.ScheduleID = sch.ScheduleID
                 JOIN class c ON sch.ClassID = c.ClassID
                 JOIN subjects s ON sd.SubjectID = s.SubjectID
                 WHERE sd.TeacherID = '$teacherId'
                 ORDER BY FIELD(sd.Weekday, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'), 
                 sd.PeriodNumber";
$scheduleResult = mysqli_query($connect, $scheduleQuery);

$scheduleData = [];
while ($row = mysqli_fetch_assoc($scheduleResult)) {
    $scheduleData[$row['Weekday']][$row['PeriodNumber']] = [
        'class' => $row['GradeClass'],
        'subject' => $row['SubjectName']
    ];
}

// Get teacher's classes
$classesQuery = "SELECT 
                c.ClassID, 
                c.ClassName, 
                sch.grade,
                COUNT(DISTINCT st.StudentID) AS StudentCount
                FROM s_details sd
                JOIN schedule sch ON sd.ScheduleID = sch.ScheduleID
                JOIN class c ON sch.ClassID = c.ClassID
                LEFT JOIN students st ON st.Class = c.ClassID AND st.Grade = sch.grade
                WHERE sd.TeacherID = '$teacherId'
                GROUP BY c.ClassID, c.ClassName, sch.grade
                ORDER BY sch.grade, c.ClassName";
$classesResult = mysqli_query($connect, $classesQuery);
// $fetchClass= mysqli_fetch_Assoc($classesResult);
// $ClassID=$fetchClass['ClassID'];
// $Grade=$fetchClass['grade'];


// Get students with IDs
// $studentsQuery = "SELECT StudentID, StudentName 
//                  FROM students 
//                  WHERE Class = $ClassID AND Grade = $Grade
//                  ORDER BY StudentName";
// $studentsResult = mysqli_query($connect, $studentsQuery);
// $studentCount = mysqli_num_rows($studentsResult);

?>

<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - <?php echo htmlspecialchars($teacher['TeacherName']); ?></title>
    <link rel="stylesheet" href="css/TeacherProfile.css">
</head>
<body>
    <div class="container">
        <h1>Teacher Profile</h1>
    </div>

    <div class="edit-section">
        <div class="edit-container">
            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="profile-section">
                    <div class="profile-image-section">
                        <img src="../Media/<?php echo htmlspecialchars($teacher['TeacherPic']); ?>" 
                             alt="<?php echo htmlspecialchars($teacher['TeacherName']); ?>" 
                             class="profile-image">
                        <?php if ($isAdministrator): ?>
                            <input type="file" name="image" accept="image/*">
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-info">
                        <div class="info-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($teacher['TeacherName']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($teacher['TeacherEmail']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($teacher['TeacherNumber']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="subject">Subject</label>
                            <?php if ($isAdministrator): ?>
                                <select name="subject" id="subject" required>
                                    <?php foreach($subjects as $subject): ?>
                                        <option value="<?php echo $subject['SubjectID']; ?>" 
                                                <?php echo ($subject['SubjectID'] == $teacher['Subject']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subject['SubjectName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" value="<?php echo htmlspecialchars($teacher['SubjectName']); ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="info-group">
                            <label for="role">Role</label>
                            <?php if ($isAdministrator): ?>
                                <select name="role" id="role" required>
                                    <?php foreach($roles as $role): ?>
                                        <option value="<?php echo $role['RoleID']; ?>" 
                                                <?php echo ($role['RoleID'] == $teacher['RoleId']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['RoleTitle']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" value="<?php echo htmlspecialchars($teacher['RoleTitle']); ?>" readonly>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a href="AllTeachers.php" class="btn back-btn">Back to All Teachers</a>
                    <?php if ($isAdministrator): ?>
                        <button type="submit" name="update" class="btn">Update Profile</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Schedule Section -->
        <h2 class="section-title">Weekly Schedule</h2>
        <div class="schedule-container">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="period-header">Day/Period</th>
                        <th class="period-header">Period 1<br>8:00-8:45</th>
                        <th class="period-header">Period 2<br>8:45-9:30</th>
                        <th class="period-header">Period 3<br>9:30-10:15</th>
                        <th class="period-header">Period 4<br>10:15-11:00</th>
                        <th class="break-cell">   Break<br>11:00-11:30</th>
                        <th class="period-header">Period 5<br>11:30-12:15</th>
                        <th class="period-header">Period 6<br>12:15-1:00</th>
                        <th class="period-header">Period 7<br>1:00-1:45</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
                    foreach ($days as $day) { ?>
                        <tr>
                            <td class="day-header"><?php echo $day ?></td>
                            <?php foreach ([1, 2, 3, 4, 'Break', 5, 6, 7] as $period) { 
                                if ($period === 'Break') { ?>
                                    <td class="break-cell">BREAK</td>
                                <?php } else { 
                                    $classInfo = $scheduleData[$day][$period] ?? null; ?>
                                    <td class="<?php echo $classInfo ? 'teaching-period' : '' ?>">
                                        <?php if ($classInfo) { 
                                            echo htmlspecialchars($classInfo['class']);
                                        } else { 
                                            echo 'Free';
                                        } ?>
                                    </td>
                                <?php } 
                            } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Class Cards Section -->
        <h2 class="section-title">Classes</h2>
        <div class="classes-container">
            <?php while ($class = mysqli_fetch_assoc($classesResult)) { ?>
                <div class="class-card">
                    <div class="class-header">
                        <h3 class="class-name">Class <?php echo htmlspecialchars($class['ClassName']) ?></h3>
                        <p class="class-grade">Grade <?php echo htmlspecialchars($class['grade']) ?></p>
                    </div>
                    
                    <div class="class-details">
                        <p class="class-detail">
                            <span class="detail-label">Students:</span> 
                            <?php echo htmlspecialchars($class['StudentCount']) ?>
                        </p>
                    </div>
                    
                    <a href="ClassDetails.php?Class=<?php echo $class['ClassID'] ?>&Grade=<?php echo $class['grade'] ?>" 
                       class="show-details">
                       Show Details
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
