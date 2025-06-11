<?php
include '../connection.php';

// Initialize search variables
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : 'name';

// Base query to get parents with their children count
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM family f WHERE f.ParentID = p.ParentID) as ChildrenCount
          FROM parents p";

// Add search conditions
if (!empty($searchTerm)) {
    if ($searchBy == 'name') {
        $query .= " WHERE p.ParentName LIKE '%$searchTerm%'";
    } elseif ($searchBy == 'child') {
        $query = "SELECT DISTINCT p.*, 
                  (SELECT COUNT(*) FROM family f WHERE f.ParentID = p.ParentID) as ChildrenCount
                  FROM parents p
                  JOIN family f ON p.ParentID = f.ParentID
                  JOIN students s ON f.StudentID = s.StudentID
                  WHERE s.StudentName LIKE '%$searchTerm%'";
    }
}

$query .= " ORDER BY p.ParentName";
$parents = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Parents</title>
    <link rel="stylesheet" href="css/AllParents.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <li>
                <a href="AllStudents.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
            </li>
            <li class="active">
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
            <h1>All Parents</h1>
            <a href="AddParent.php" class="add-btn">
                <i class="fas fa-plus"></i> Add New Parent
            </a>
        </div>
        
        <!-- Search Section -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search parents or children..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <select name="search_by" class="search-select">
                    <option value="name" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'name') ? 'selected' : ''; ?>>
                        By Parent Name
                    </option>
                    <option value="child" <?php echo (isset($_GET['search_by']) && $_GET['search_by'] == 'child') ? 'selected' : ''; ?>>
                        By Child Name
                    </option>
                </select>

                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="parents-grid">
            <?php while ($parent = mysqli_fetch_assoc($parents)) { 
                // Fetch children names for this parent
                $childrenQuery = "SELECT s.StudentName 
                                FROM students s 
                                JOIN family f ON s.StudentID = f.StudentID 
                                WHERE f.ParentID = '{$parent['ParentID']}'
                                LIMIT 3";
                $children = mysqli_query($connect, $childrenQuery);
                $childrenNames = [];
                while ($child = mysqli_fetch_assoc($children)) {
                    $childrenNames[] = $child['StudentName'];
                }
            ?>
                <a href="ParentProfile.php?id=<?php echo $parent['ParentID']; ?>" class="parent-card-link">
                    <div class="parent-card">
                        <div class="parent-info">
                            <h3 class="parent-name"><?php echo htmlspecialchars($parent['ParentName']); ?></h3>
                            <p class="parent-email"><?php echo htmlspecialchars($parent['ParentEmail']); ?></p>
                            <p class="parent-phone"><?php echo htmlspecialchars($parent['ParentNumber']); ?></p>
                            <div class="children-info">
                                <span class="children-count">
                                    <i class="fas fa-child"></i> <?php echo $parent['ChildrenCount']; ?> 
                                    <?php echo $parent['ChildrenCount'] == 1 ? 'Child' : 'Children'; ?>
                                </span>
                                <?php if (!empty($childrenNames)) { ?>
                                    <p class="children-names">
                                        <?php 
                                        echo htmlspecialchars(implode(', ', $childrenNames));
                                        if (count($childrenNames) < $parent['ChildrenCount']) {
                                            echo ' ...';
                                        }
                                        ?>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html> 
