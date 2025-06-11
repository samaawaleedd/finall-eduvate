<?php
include '../connection.php';

// Fetch all grades first
$gradesQuery = "SELECT * FROM grades ORDER BY GradeNumber";
$grades = mysqli_query($connect, $gradesQuery);

// Initialize search variables
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : 'name';
$gradeFilter = isset($_GET['grade']) ? $_GET['grade'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students</title>
    <link rel="stylesheet" href="css/AllStudents.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
</head>
<body>
y>
    <!-- Vertical Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-user-shield"></i> Admin Panel</h3>
            <button class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="Dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="MyProfile.php">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            
            <!-- Users Management Section -->
            <li class="menu-section">
                <span class="menu-title">Users Management</span>
            </li>
            <li>
                <a href="AllAdmins.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Admins</span>
                </a>
            </li>
            <li >
                <a href="AllTeachers.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li class="active">
                <a href="AllStudents.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </li>
            <li>
                <a href="AllParents.php">
                    <i class="fas fa-users"></i>
                    <span>Parents</span>
                </a>
            </li>

            <!-- Class Management Section -->
            <li class="menu-section">
                <span class="menu-title">Class Management</span>
            </li>
            <li>
                <a href="AllClass.php">
                    <i class="fas fa-school"></i>
                    <span>Classes</span>
                </a>
            </li>

            <!-- Settings Section -->
            <li class="menu-section">
                <span class="menu-title">Settings</span>
            </li>
            <li>
                <a href="EditProfile.php">
                    <i class="fas fa-user-edit"></i>
                    <span>Edit Profile</span>
                </a>
            </li>
            <li>
                <a href="ResetPassword.php">
                    <i class="fas fa-key"></i>
                    <span>Reset Password</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>
    <div class="main-content">
    <div class="container">
        <div class="header-section">
            <h1>All Students by Grade</h1>
            <a href="AddStudent.php" class="add-btn">
                <i class="fas fa-plus"></i> Add New Student
            </a>
        </div>
        
        <!-- Search Section -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Enter student name or grade..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <select name="search_by" class="search-select">
                    <option value="name" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'name') ? 'selected' : ''; ?>>
                        By Name
                    </option>
                    <option value="grade" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'grade') ? 'selected' : ''; ?>>
                        By Grade
                    </option>
                </select>

                <select name="grade" class="search-select">
                    <option value="">All Grades</option>
                    <?php 
                    mysqli_data_seek($grades, 0);
                    while ($grade = mysqli_fetch_assoc($grades)) { ?>
                        <option value="<?php echo $grade['GradeID']; ?>" 
                                <?php echo (isset($_GET['grade']) && $_GET['grade'] == $grade['GradeID']) ? 'selected' : ''; ?>>
                            Grade <?php echo htmlspecialchars($grade['GradeNumber']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <?php
        mysqli_data_seek($grades, 0);
        while ($grade = mysqli_fetch_assoc($grades)) {
            // Modify query based on search
            $studentsQuery = "SELECT s.*, c.ClassName 
                            FROM students s 
                            LEFT JOIN class c ON s.Class = c.ClassID 
                            WHERE s.Grade = '{$grade['GradeID']}'";
            
            if (!empty($searchTerm)) {
                if ($searchBy == 'name') {
                    $studentsQuery .= " AND s.StudentName LIKE '%$searchTerm%'";
                } elseif ($searchBy == 'grade' && $grade['GradeID'] != $gradeFilter) {
                    continue;
                }
            }
            
            if (!empty($gradeFilter) && $grade['GradeID'] != $gradeFilter) {
                continue;
            }
            
            $studentsQuery .= " ORDER BY s.StudentName";
            $students = mysqli_query($connect, $studentsQuery);
            
            // Only show grade if it has matching students
            if (mysqli_num_rows($students) > 0) {
                ?>
                <div class="grade-section">
                    <h2 class="grade-title">Grade <?php echo htmlspecialchars($grade['GradeNumber']); ?></h2>
                    
                    <div class="students-grid">
                        <?php while ($student = mysqli_fetch_assoc($students)) { ?>
                            <a href="StudentProfile.php?id=<?php echo $student['StudentID']; ?>" class="student-card-link">
                                <div class="student-card">
                                    <img src="../Media/<?php echo htmlspecialchars($student['Picture']); ?>" 
                                         alt="<?php echo htmlspecialchars($student['StudentName']); ?>" 
                                         class="student-image">
                                    <h3 class="student-name"><?php echo htmlspecialchars($student['StudentName']); ?></h3>
                                    <p class="student-class"><?php echo htmlspecialchars($student['ClassName']); ?></p>
                                    <p class="student-email"><?php echo htmlspecialchars($student['StudentEmail']); ?></p>
                                    <p class="student-phone"><?php echo htmlspecialchars($student['StudentNumber']); ?></p>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
</body>
</html> 
