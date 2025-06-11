<?php
    include '../connection.php';


$selectGrades = "SELECT * FROM grades ORDER BY GradeID ASC";
$gradesResult = mysqli_query($connect, $selectGrades);
$grades = [];
while ($grade = mysqli_fetch_assoc($gradesResult)) {
    $grades[] = $grade;
}
mysqli_data_seek($gradesResult, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/AllClass.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
</head>
<body>

<div class="page-wrapper">
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
            <li class="active">
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


    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Grade Navigation -->
        <div class="grade-nav-container">
            <div class="grade-nav">
                <?php foreach ($grades as $grade) { ?>
                    <a href="#grade-<?= $grade['GradeID'] ?>" class="grade-nav-item">
                        <!-- <i class="fas fa-graduation-cap"></i> -->
                        <span>Grade <?= $grade['GradeID'] ?></span>
                    </a>
                <?php } ?>
            </div>
        </div>

        <div class="container py-4">
            <div class="text-center mb-5">
                <h1 class="main-title">School Classes</h1>
            </div>

            <?php while ($grade = mysqli_fetch_assoc($gradesResult)) { ?>
                <div id="grade-<?= $grade['GradeID'] ?>" class="grade-section mb-5">
                    <div class="grade-header">
                        <h2 class="grade-title">
                            <i class="fas fa-graduation-cap me-2"></i>
                            <?= $grade['GradeNumber'] ?>
                        </h2>
                    </div>
                    <div class="row g-4">
                        <?php
                        $selectClasses = "SELECT c.*, COUNT(s.StudentID) as student_count 
                                        FROM class c 
                                        LEFT JOIN students s ON s.Class = c.ClassID AND s.Grade = {$grade['GradeID']}
                                        GROUP BY c.ClassID";
                        $classesResult = mysqli_query($connect, $selectClasses);

                        while ($class = mysqli_fetch_assoc($classesResult)) {
                        ?>
                            <div class="col-md-2-4">
                                <a href="ClassDetails.php?Grade=<?= $grade['GradeID'] ?>&Class=<?= $class['ClassID'] ?>" 
                                   class="text-decoration-none">
                                    <div class="class-card">
                                        <div class="card-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h3 class="class-name">Class <?php echo $class['ClassName'] ?></h3>
                                        <div class="grade-info"> <?php echo $grade['GradeNumber'] ?></div>
                                        <div class="student-count">
                                            <i class="fas fa-user-graduate"></i>
                                            <?= $class['student_count'] ?> Student<?= $class['student_count'] != 1 ? 's' : '' ?>
                                        </div>
                                        <div class="view-details">
                                            View Details <i class="fas fa-arrow-right ms-2"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Add smooth scrolling to grade navigation
document.querySelectorAll('.grade-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
        
        // Update active state
        document.querySelectorAll('.grade-nav-item').forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
    });
});

// Highlight current grade section in navigation based on scroll position
window.addEventListener('scroll', function() {
    const gradeSections = document.querySelectorAll('.grade-section');
    const navItems = document.querySelectorAll('.grade-nav-item');
    
    gradeSections.forEach((section, index) => {
        const rect = section.getBoundingClientRect();
        if (rect.top <= 100 && rect.bottom >= 100) {
            navItems.forEach(nav => nav.classList.remove('active'));
            navItems[index].classList.add('active');
        }
    });
});

// Sidebar toggle functionality
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
});

// Initialize sidebar state on load
window.dispatchEvent(new Event('resize'));
</script>
</body>
</html>
