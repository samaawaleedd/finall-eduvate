<?php
include '../connection.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}

$currentAdminId = $_SESSION['AdminID'];

// Fetch current admin's role
$currentAdminQuery = "SELECT RoleId FROM admins WHERE AdminID = '$currentAdminId'";
$currentAdminResult = mysqli_query($connect, $currentAdminQuery);
$currentAdmin = mysqli_fetch_assoc($currentAdminResult);
$isAdministrator = ($currentAdmin['RoleId'] == 1);

if (!isset($_GET['id'])) {
    header("Location: AllClass.php");
    exit();
}

$studentId = mysqli_real_escape_string($connect, $_GET['id']);

// Fetch student information including class and grade
$query = "SELECT s.*, c.ClassName, g.GradeNumber
          FROM students s 
          LEFT JOIN class c ON s.Class = c.ClassID 
          LEFT JOIN grades g ON s.Grade = g.GradeID
        --   LEFT JOIN bus b ON s.BusNumber = b.BusID
          WHERE s.StudentID = '$studentId'";
$result = mysqli_query($connect, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: AllClass.php");
    exit();
}

$student = mysqli_fetch_assoc($result);

// Fetch parent information
$parentsQuery = "SELECT p.* 
                FROM parents p
                JOIN family f ON p.ParentID = f.ParentID
                WHERE f.StudentID = '$studentId'";
$parentsResult = mysqli_query($connect, $parentsQuery);

// Fetch all grades for the dropdown
$gradesQuery = "SELECT * FROM grades ORDER BY GradeNumber";
$grades = mysqli_query($connect, $gradesQuery);

// Fetch all classes for the dropdown
$classesQuery = "SELECT * FROM class ORDER BY ClassName";
$classes = mysqli_query($connect, $classesQuery);

// Fetch student's subjects and teachers
$subjectsQuery = "SELECT DISTINCT s.SubjectName, t.TeacherName, t.TeacherID
                 FROM s_details sd
                 JOIN schedule sch ON sd.ScheduleID = sch.ScheduleID
                 JOIN subjects s ON sd.SubjectID = s.SubjectID
                 JOIN teachers t ON sd.TeacherID = t.TeacherID
                 WHERE sch.ClassID = '{$student['Class']}' 
                 AND sch.grade = '{$student['Grade']}'
                 ORDER BY s.SubjectName";
$subjectsResult = mysqli_query($connect, $subjectsQuery);

// Handle form submission for updating student information
if (isset($_POST['update'])) {
    // Only allow administrators to update profiles
    if (!$isAdministrator) {
        $error = "You don't have permission to update student profiles.";
    } else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $phone = mysqli_real_escape_string($connect, $_POST['phone']);
        $address = mysqli_real_escape_string($connect, $_POST['address']);
        $grade = mysqli_real_escape_string($connect, $_POST['grade']);
        $class = mysqli_real_escape_string($connect, $_POST['class']);
        
        // Handle image upload
        $image = $student['Picture']; // Default to current image
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
        $update = "UPDATE students SET 
                   StudentName = '$name',
                   StudentEmail = '$email',
                   StudentNumber = '$phone',
                   StudentAddress = '$address',
                   Grade = '$grade',
                   Class = '$class',
                   Picture = '$image'
                   WHERE StudentID = '$studentId'";
                   
        if (mysqli_query($connect, $update)) {
            $success = "Student information updated successfully!";
            // Refresh student data
            $result = mysqli_query($connect, $query);
            $student = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating student information: " . mysqli_error($connect);
        }
    }
}

// Handle delete student action
if (isset($_POST['delete_student']) && $isAdministrator) {
    // Delete student from all related tables if needed (e.g., family, payments, etc.)
    $studentId = mysqli_real_escape_string($connect, $_GET['id']);
    // Example: delete from family table (if exists)
    mysqli_query($connect, "DELETE FROM family WHERE StudentID = '$studentId'");
    // Example: delete from payments table (if exists)
    mysqli_query($connect, "DELETE FROM payments WHERE StudentID = '$studentId'");
    // Delete from students table
    mysqli_query($connect, "DELETE FROM students WHERE StudentID = '$studentId'");
    // Redirect after deletion
    header("Location: AllStudents.php");
    exit();
}

// Fetch student's schedule
$scheduleQuery = "SELECT sd.*, s.SubjectName, t.TeacherName, t.TeacherID
                 FROM s_details sd
                 JOIN schedule sch ON sd.ScheduleID = sch.ScheduleID
                 JOIN subjects s ON sd.SubjectID = s.SubjectID
                 JOIN teachers t ON sd.TeacherID = t.TeacherID
                 WHERE sch.ClassID = '{$student['Class']}' AND sch.grade = '{$student['Grade']}'
                 ORDER BY FIELD(sd.Weekday, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'), 
                 sd.PeriodNumber";
$scheduleResult = mysqli_query($connect, $scheduleQuery);

$scheduleData = [];
while ($row = mysqli_fetch_assoc($scheduleResult)) {
    $day = $row['Weekday'];
    $period = $row['PeriodNumber'];
    
    if (!isset($scheduleData[$day])) {
        $scheduleData[$day] = array();
    }
    
    $scheduleData[$day][$period] = array(
        'subject' => $row['SubjectName'],
        'teacher' => $row['TeacherName'],
        'teacherId' => $row['TeacherID'],
        'start_time' => date('h:i A', strtotime($row['StartTime'])),
        'end_time' => date('h:i A', strtotime($row['EndTime']))
    );
}

// Fetch all available buses for the dropdown
// $busesQuery = "SELECT * FROM bus ORDER BY BusNumber";
// $buses = mysqli_query($connect, $busesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - <?php echo htmlspecialchars($student['StudentName']); ?></title>
    <link rel="stylesheet" href="css/TeacherProfile.css">
    <link rel="stylesheet" href="css/StudentProfile.css">

</head>
<body>
    <div class="container">
        <h1>Student Profile</h1>
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
                        <img src="../Media/<?php echo htmlspecialchars($student['Picture']); ?>" 
                             alt="<?php echo htmlspecialchars($student['StudentName']); ?>" 
                             class="profile-image">
                        <?php if ($isAdministrator): ?>
                            <input type="file" name="image" accept="image/*">
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-info">
                        <div class="info-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($student['StudentName']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($student['StudentEmail']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($student['StudentNumber']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" 
                                   value="<?php echo htmlspecialchars($student['StudentAddress']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="grade">Grade</label>
                            <?php if ($isAdministrator): ?>
                                <select name="grade" id="grade" required>
                                    <?php while($grade = mysqli_fetch_assoc($grades)): ?>
                                        <option value="<?php echo $grade['GradeID']; ?>" 
                                                <?php echo ($grade['GradeID'] == $student['Grade']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($grade['GradeNumber']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" value="<?php echo htmlspecialchars($student['Grade']); ?>" readonly>
                            <?php endif; ?>
                        </div>

                        <div class="info-group">
                            <label for="class">Class</label>
                            <?php if ($isAdministrator): ?>
                                <select name="class" id="class" required>
                                    <?php while($class = mysqli_fetch_assoc($classes)): ?>
                                        <option value="<?php echo $class['ClassID']; ?>" 
                                                <?php echo ($class['ClassID'] == $student['Class']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['ClassName']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" value="<?php echo htmlspecialchars($student['Class']); ?>" readonly>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a href="ClassDetails.php?Class=<?php echo $student['Class']; ?>&Grade=<?php echo $student['Grade']; ?>" class="btn back-btn">Back to Class Details</a>
                    <?php if ($isAdministrator): ?>
                        <button type="submit" name="update" class="btn">Update Profile</button>
                        <button type="submit" name="delete_student" class="btn" style="background:#dc3545;color:#fff;margin-left:10px;" onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                            Delete Student
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Parents Section -->
        <h2 class="section-title">Parents Information</h2>
        <div class="parent-section">
            <div class="parent-header">
                <a href="addParent.php?student_id=<?php echo $studentId; ?>" class="btn assign-parent-btn <?php echo mysqli_num_rows($parentsResult) > 0 ? 'small' : ''; ?>">
                    Assign Parent
                </a>
            </div>
            <div class="parent-cards">
                <?php 
                if (mysqli_num_rows($parentsResult) > 0):
                    while($parent = mysqli_fetch_assoc($parentsResult)): ?>
                        <a href="ParentProfile.php?id=<?php echo $parent['ParentID']; ?>" class="parent-card-link">
                            <div class="parent-card">
                                <h3 class="parent-name"><?php echo htmlspecialchars($parent['ParentName']); ?></h3>
                                <p class="parent-info">
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($parent['ParentNumber']); ?>
                                </p>
                                <p class="parent-info">
                                    <strong>Email:</strong> <?php echo htmlspecialchars($parent['ParentEmail']); ?>
                                </p>
                            </div>
                        </a>
                    <?php endwhile; 
                else: ?>
                    <div class="no-parents-message">
                        <p>No parents assigned to this student.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <style>
            .parent-section {
                position: relative;
            }
            .parent-header {
                text-align: right;
                margin-bottom: 10px;
            }
            .no-parents-message {
                text-align: center;
                padding: 20px;
                background: #f5f5f5;
                border-radius: 8px;
                margin: 20px 0;
            }
            .assign-parent-btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: all 0.3s;
            }
            .assign-parent-btn.small {
                padding: 5px 10px;
                font-size: 0.9em;
            }
            .assign-parent-btn:hover {
                background-color: #45a049;
                transform: translateY(-2px);
            }
        </style>

    <div class="main-content">
        <!-- Schedule Section -->
        <div class="schedule-container">
            <h2>Weekly Schedule</h2>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th class="period-header">Day/Period</th>
                        <th class="period-header">Period 1<br>8:00-8:45</th>
                        <th class="period-header">Period 2<br>8:45-9:30</th>
                        <th class="period-header">Period 3<br>9:30-10:15</th>
                        <th class="period-header">Period 4<br>10:15-11:00</th>
                        <th class="break-cell">Break<br>11:00-11:30</th>
                        <th class="period-header">Period 5<br>11:30-12:15</th>
                        <th class="period-header">Period 6<br>12:15-1:00</th>
                        <th class="period-header">Period 7<br>1:00-1:45</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $weekDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];
                    foreach ($weekDays as $day): 
                    ?>
                        <tr>
                            <td class="day-header"><?php echo $day; ?></td>
                            <?php for ($period = 1; $period <= 7; $period++): ?>
                                <?php if ($period == 5): ?>
                                    <td class="break-cell">Break</td>
                                <?php endif; ?>
                                <td class="period-cell">
                                    <?php if (isset($scheduleData[$day][$period])): ?>
                                        <div class="subject">
                                            <?php echo htmlspecialchars($scheduleData[$day][$period]['subject']); ?>
                                        </div>
                                        <div class="teacher">
                                            <a href="TeacherProfile.php?id=<?php echo $scheduleData[$day][$period]['teacherId']; ?>">
                                                <?php echo htmlspecialchars($scheduleData[$day][$period]['teacher']); ?>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="free-period">Free</span>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Subjects Section -->
        <div class="subjects-section">
            <h3>Subjects and Teachers</h3>
            <div class="subjects-grid">
                <?php while($subject = mysqli_fetch_assoc($subjectsResult)): ?>
                    <div class="subject-card">
                        <span class="subject-name"><?php echo htmlspecialchars($subject['SubjectName']); ?></span>
                        <span class="teacher-name">
                            - <a href="TeacherProfile.php?id=<?php echo $subject['TeacherID']; ?>">
                                <?php echo htmlspecialchars($subject['TeacherName']); ?>
                            </a>
                        </span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>