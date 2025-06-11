<?php
include '../connection.php';

// Fetch all subjects first
$subjectsQuery = "SELECT * FROM subjects ORDER BY SubjectName";
$subjects = mysqli_query($connect, $subjectsQuery);

// Initialize search variables
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : 'name';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Teachers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

    <link rel="stylesheet" href="css/AllTeachers.css">
    <link rel="stylesheet" href="css/AllStudents.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
</head>
<body>
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
            <li  class="active">
                <a href="AllTeachers.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li>
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
            <h1>All Teachers by Subject</h1>
            <a href="AddTeacher.php" class="add-btn">
                <i class="fas fa-plus"></i> Add New Teacher
            </a>
        </div>
        
        <!-- Search Section -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Enter teacher name or subject..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <select name="search_by" class="search-select">
                    <option value="name" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'name') ? 'selected' : ''; ?>>
                        By Name
                    </option>
                    <option value="subject" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'subject') ? 'selected' : ''; ?>>
                        By Subject
                    </option>
                </select>

                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <?php
        while ($subject = mysqli_fetch_assoc($subjects)) {
            // Modify query based on search
            $teachersQuery = "SELECT * FROM teachers WHERE Subject = '{$subject['SubjectID']}'";
            
            if (!empty($searchTerm)) {
                if ($searchBy == 'name') {
                    $teachersQuery .= " AND TeacherName LIKE '%$searchTerm%'";
                } elseif ($searchBy == 'subject' && stripos($subject['SubjectName'], $searchTerm) === false) {
                    // Skip this subject if searching by subject and doesn't match
                    continue;
                }
            }
            
            $teachersQuery .= " ORDER BY TeacherName";
            $teachers = mysqli_query($connect, $teachersQuery);
            
            // Only show subject if it has matching teachers
            if (mysqli_num_rows($teachers) > 0) {
                ?>
                <div class="subject-section">
                    <h2 class="subject-title"><?php echo htmlspecialchars($subject['SubjectName']); ?></h2>
                    
                    <div class="teachers-grid">
                        <?php while ($teacher = mysqli_fetch_assoc($teachers)) { ?>
                            <a href="TeacherProfile.php?id=<?php echo $teacher['TeacherID']; ?>" style="text-decoration: none;">
                                <div class="teacher-card">
                                    <img src="../Media/<?php echo htmlspecialchars($teacher['TeacherPic']); ?>" 
                                         alt="<?php echo htmlspecialchars($teacher['TeacherName']); ?>" 
                                         class="teacher-image">
                                    <h3 class="teacher-name"><?php echo htmlspecialchars($teacher['TeacherName']); ?></h3>
                                    <p class="teacher-email"><?php echo htmlspecialchars($teacher['TeacherEmail']); ?></p>
                                    <p class="teacher-phone"><?php echo htmlspecialchars($teacher['TeacherNumber']); ?></p>
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
