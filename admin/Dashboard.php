<?php
include '../connection.php';

if (!isset($_SESSION['AdminID'])) {
    header("Location: login.php");
    exit();
}
$AdminID = $_SESSION['AdminID'];
$selectAdmin = "SELECT * FROM admins WHERE AdminID =$AdminID ";
$runAdmin=mysqli_query($connect, $selectAdmin);
$fetch=mysqli_fetch_assoc($runAdmin);
$name=$fetch['AdminName'];

// Fetch quick statistics
$totalStudentsQuery = "SELECT COUNT(*) as total FROM students";
$totalTeachersQuery = "SELECT COUNT(*) as total FROM teachers";
$totalClassesQuery = "SELECT COUNT(DISTINCT Class) as total FROM students";
$totalParentsQuery = "SELECT COUNT(*) as total FROM parents";

// Add payment statistics queries
$totalPaidQuery = "SELECT COUNT(DISTINCT p.StudentID) as paid_count, SUM(p.Fees) as total_paid FROM payments p WHERE p.Fees IS NOT NULL";
$totalUnpaidQuery = "SELECT COUNT(*) as unpaid_count FROM students s WHERE NOT EXISTS (SELECT 1 FROM payments p WHERE p.StudentID = s.StudentID AND p.Fees IS NOT NULL)";

$studentsResult = mysqli_query($connect, $totalStudentsQuery);
$teachersResult = mysqli_query($connect, $totalTeachersQuery);
$classesResult = mysqli_query($connect, $totalClassesQuery);
$parentsResult = mysqli_query($connect, $totalParentsQuery);
$paidResult = mysqli_query($connect, $totalPaidQuery);
$unpaidResult = mysqli_query($connect, $totalUnpaidQuery);

$totalStudents = mysqli_fetch_assoc($studentsResult)['total'];
$totalTeachers = mysqli_fetch_assoc($teachersResult)['total'];
$totalClasses = mysqli_fetch_assoc($classesResult)['total'];
$totalParents = mysqli_fetch_assoc($parentsResult)['total'];
$paidStudents = mysqli_fetch_assoc($paidResult);
$unpaidStudents = mysqli_fetch_assoc($unpaidResult)['unpaid_count'];
$totalPaid = $paidStudents['total_paid'];
$paidCount = $paidStudents['paid_count'];

// Calculate percentages
$paymentRate = $totalStudents > 0 ? round(($paidCount / $totalStudents) * 100) : 0;

// Fetch recent activities (example: latest registered students)
$recentStudentsQuery = "SELECT * FROM students ORDER BY StudentID DESC LIMIT 5";
$recentStudents = mysqli_query($connect, $recentStudentsQuery);

// Fetch upcoming events or announcements if you have such a table
// $eventsQuery = "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date LIMIT 3";
// $events = mysqli_query($connect, $eventsQuery);

// New queries for parent subscription status
$subscribedParentsQuery = "SELECT COUNT(*) as subscribed FROM parents WHERE Is_subscribed = 1";
$unsubscribedParentsQuery = "SELECT COUNT(*) as unsubscribed FROM parents WHERE Is_subscribed = 0";

$subscribedResult = mysqli_query($connect, $subscribedParentsQuery);
$unsubscribedResult = mysqli_query($connect, $unsubscribedParentsQuery);

$subscribedParents = mysqli_fetch_assoc($subscribedResult)['subscribed'];
$unsubscribedParents = mysqli_fetch_assoc($unsubscribedResult)['unsubscribed'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../fontawesome-free-6.4.0-web/css/all.min.css">
    
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
            <li class="active">
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
            <li  >
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
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h1>Welcome, <?php echo $name;?></h1>
            <p class="date"><?php echo date('l, F j, Y'); ?></p>
        </div>

        <!-- Quick Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-user-graduate"></i>
                <div class="stat-info">
                    <h3>Total Students</h3>
                    <p><?php echo $totalStudents; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <div class="stat-info">
                    <h3>Total Teachers</h3>
                    <p><?php echo $totalTeachers; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-school"></i>
                <div class="stat-info">
                    <h3>Total Classes</h3>
                    <p><?php echo $totalClasses; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <h3>Total Parents</h3>
                    <p><?php echo $totalParents; ?></p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            <!-- Quick Actions Section -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="AllTeachers.php" class="action-btn">
                        <i class="fas fa-user-tie"></i>
                        Manage Teachers
                    </a>
                    <a href="AllStudents.php" class="action-btn">
                        <i class="fas fa-user-graduate"></i>
                        Manage Students
                    </a>
                    <a href="AllClass.php" class="action-btn">
                        <i class="fas fa-chalkboard"></i>
                        Manage Classes
                    </a>
                    <a href="AllParents.php" class="action-btn">
                        <i class="fas fa-user-friends"></i>
                        Manage Parents
                    </a>
                    <a href="ViewMessages.php" class="btn action-btn btn-outline-primary">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                    <a href="AddPost.php" class="btn action-btn btn-outline-primary">
                        <i class="fas fa-envelope"></i> Posts And announcements
                    </a>
                </div>
            </div>

            <!-- Two Column Layout for Recent Activities and Analytics -->
            <div class="dashboard-grid">
                <!-- Charts Section -->
                <div class="charts-row">
                    <!-- Population Distribution Chart -->
                    <div class="dashboard-card">
                        <h2>Parent's Subscription Status</h2>
                        <canvas id="populationChart"></canvas>
                    </div>

                    <!-- Payment Status Chart -->
                    <div class="dashboard-card">
                        <h2>Fees Payment Status</h2>
                        <div class="payment-stats">
                            <div class="stat-item">
                                <span class="stat-label">Payment Rate</span>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $paymentRate; ?>%;" aria-valuenow="<?php echo $paymentRate; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $paymentRate; ?>%
                                    </div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Total Collected</span>
                                <span class="stat-value"><?php echo number_format($totalPaid, 2); ?> EGP</span>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Add View Messages Button at the bottom
                <div class="design" style="text-align:right; margin-top: 20px;">
                    <a href="ViewMessages.php" class="btn btn-outline-primary" style="padding: 10px 20px; font-weight: 500;">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                </div>
                 <div class="design" style="text-align:right; margin-top: 20px;">
                    <a href="AddPost.php" class="btn btn-outline-primary" style="padding: 10px 20px; font-weight: 500;">
                        <i class="fas fa-envelope"></i> Posts And announcements
                    </a>
                </div>
            </div> -->
                <!-- Recent Activities Section -->
                <div class="dashboard-card full-width">
                    <h2>Recent Activities</h2>
                    <div class="recent-activities">
                        <?php while($student = mysqli_fetch_assoc($recentStudents)) { ?>
                            <div class="activity-item">
                                <i class="fas fa-user-plus"></i>
                                <div class="activity-details">
                                    <p>New student registered: <?php echo htmlspecialchars($student['StudentName']); ?></p>
                                    <small>Class: <?php echo htmlspecialchars($student['Class']); ?></small>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>


        .main-content {
            padding: 20px;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            margin-bottom: 20px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            padding: 15px;
        }

        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .dashboard-card {
            padding: 30px;
            margin-bottom: 0;
            padding-bottom: 20px;
        }

        .quick-actions {
            margin-bottom: 20px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .action-btn {
            padding: 10px 15px;
        }

        .action-btn a{
            padding: 10px 15px;
            text-decoration: none;
            color: #333;
        }

        .recent-activities {
            min-height: 420px;
        }

        .activity-item {
            padding: 10px;
        }

        .payment-stats {
            margin-bottom: 15px;
        }

        .chart-container {
            height: 250px;
            margin-top: 15px;
        }

        .full-width {
            width: 100%;
        }

        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script>
        // Population Distribution Chart
        const ctx = document.getElementById('populationChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Subscribed Parents', 'Unsubscribed Parents'],
                datasets: [{
                    data: [<?php echo $subscribedParents; ?>, <?php echo $unsubscribedParents; ?>],
                    backgroundColor: [
                        '#ffba00', // Subscribed
                        '#6d9773'  // Unsubscribed
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Payment Status Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Unpaid'],
                datasets: [{
                    data: [<?php echo $paidCount; ?>, <?php echo $unpaidStudents; ?>],
                    backgroundColor: [
                        '#28a745',  // Green for paid
                        '#dc3545'   // Red for unpaid
                    ],
                    borderWidth: 0,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Student Fees Payment Status',
                        padding: {
                            bottom: 10
                        }
                    }
                },
                layout: {
                    padding: 20
                }
            },
            plugins: [{
                id: 'centerText',
                afterDraw: function(chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;
                    ctx.restore();
                    
                    // Draw center text
                    const fontSize = (height / 150).toFixed(2);
                    ctx.font = fontSize + 'em sans-serif';
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';
                    
                    const text = '<?php echo $paymentRate; ?>%';
                    const textX = width / 2;
                    const textY = height / 2;
                    
                    ctx.fillStyle = '#0C3B2E';
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });
    </script>

    <style>
    .payment-stats {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        margin-bottom: 0.5rem;
        padding: 0 1rem;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #0C3B2E;
        opacity: 0.9;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 600;
        color: #6d9773;
    }

    .progress {
        height: 8px;
        background-color: #f0f2f1;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        background-color: #4CAF50;
        color: white;
        text-align: center;
        font-size: 0.7rem;
        line-height: 8px;
        transition: width 0.6s ease;
    }

    .chart-container {
        position: relative;
        margin: auto;
        width: 100%;
    }
    </style>
</body>
</html>
