<?php
include '../connection.php';

// Initialize search variables
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$searchBy = isset($_GET['search_by']) ? $_GET['search_by'] : 'name';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';

// Base query
$query = "SELECT a.*, r.RoleTitle 
          FROM admins a 
          LEFT JOIN roles r ON a.RoleId = r.RoleID";

// Add search conditions
if (!empty($searchTerm)) {
    if ($searchBy == 'name') {
        $query .= " WHERE a.AdminName LIKE '%$searchTerm%'";
    }
}

// Add role filter
if (!empty($roleFilter)) {
    $query .= (!empty($searchTerm) && $searchBy == 'name') ? " AND" : " WHERE";
    $query .= " a.RoleId = '$roleFilter'";
}

$query .= " ORDER BY a.AdminName";
$admins = mysqli_query($connect, $query);

// Fetch roles for filter
$rolesQuery = "SELECT * FROM roles WHERE RoleID IN (1, 2)"; // Only Administrator and Follow Up roles
$roles = mysqli_query($connect, $rolesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/AllTeachers.css">
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">

    <link rel="stylesheet" href="css/sidebar.css">
    <script src="js/sidebar.js" defer></script>
    <style>
        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin-top: 5px;
        }
        .role-administrator {
            background-color: #ffba00;
            color: #0c3b2e;
        }
        .role-followup {
            background-color: #6d9773;
            color: white;
        }
        
        /* Admin specific styles */
        .admins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        
        .admin-card-link {
            text-decoration: none;
            color: inherit;
        }
        
        .admin-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .admin-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .admin-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .admin-name {
            font-size: 1.1em;
            font-weight: bold;
            color: #0c3b2e;
            margin: 0;
        }
        
        .admin-email, .admin-phone {
            font-size: 0.9em;
            color: #666;
            margin: 0;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .add-btn {
            background-color: #6d9773;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .add-btn:hover {
            background-color: #5a7d5f;
            color: white;
        }

        .add-btn i {
            font-size: 0.9em;
        }
    </style>
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
            <li class="active">
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
            <h1>All Administrators</h1>
            <a href="AddAdmin.php" class="add-btn">
                <i class="fas fa-plus"></i> Add New Admin
            </a>
        </div>
        
        <!-- Search Section -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search by name..."
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                
                <select name="role" class="search-select">
                    <option value="">All Roles</option>
                    <?php while($role = mysqli_fetch_assoc($roles)): ?>
                        <option value="<?php echo $role['RoleID']; ?>" 
                                <?php echo (isset($_GET['role']) && $_GET['role'] == $role['RoleID']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($role['RoleTitle']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <div class="admins-grid">
            <?php while ($admin = mysqli_fetch_assoc($admins)) { ?>
                <a href="AdminProfile.php?id=<?php echo $admin['AdminID']; ?>" class="admin-card-link">
                    <div class="admin-card">
                        <div class="admin-info">
                            <h3 class="admin-name"><?php echo htmlspecialchars($admin['AdminName']); ?></h3>
                            <p class="admin-email"><?php echo htmlspecialchars($admin['AdminEmail']); ?></p>
                            <p class="admin-phone"><?php echo htmlspecialchars($admin['AdminNumber']); ?></p>
                            <div class="role-badge role-<?php echo strtolower(str_replace(' ', '', $admin['RoleTitle'])); ?>">
                                <?php echo htmlspecialchars($admin['RoleTitle']); ?>
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
