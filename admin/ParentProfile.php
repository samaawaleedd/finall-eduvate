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
    header("Location: AllParents.php");
    exit();
}

$parentId = mysqli_real_escape_string($connect, $_GET['id']);

// Fetch parent information
$query = "SELECT * FROM parents WHERE ParentID = '$parentId'";
$result = mysqli_query($connect, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: AllParents.php");
    exit();
}

$parent = mysqli_fetch_assoc($result);

// Store parent info in session for AssignChildren.php
if(isset($_POST['add_child'])) {
    $_SESSION['existing_parent'] = [
        'id' => $parent['ParentID'],
        'name' => $parent['ParentName'],
        'email' => $parent['ParentEmail']
    ];
    header("Location: AssignChildren.php");
    exit();
}

// Fetch first child's ID for the back button
$firstChildQuery = "SELECT StudentID FROM family WHERE ParentID = '$parentId' LIMIT 1";
$firstChildResult = mysqli_query($connect, $firstChildQuery);
$firstChild = mysqli_fetch_assoc($firstChildResult);
$studentId = $firstChild ? $firstChild['StudentID'] : '';

// Fetch children information with additional details
$childrenQuery = "SELECT s.*, c.ClassName, g.GradeNumber, g.FeeAmount,
                 (SELECT SUM(p.TotalPrice) FROM payments p WHERE p.StudentID = s.StudentID) as total_payments
                 FROM students s
                 JOIN family f ON s.StudentID = f.StudentID
                 LEFT JOIN class c ON s.Class = c.ClassID
                 LEFT JOIN grades g ON s.Grade = g.GradeID
                 WHERE f.ParentID = '$parentId'
                 ORDER BY s.StudentName";
$childrenResult = mysqli_query($connect, $childrenQuery);

// Get total number of children
$childrenCount = mysqli_num_rows($childrenResult);

// Handle form submission for updating parent information
if (isset($_POST['update'])) {
    // Only allow administrators to update profiles
    if (!$isAdministrator) {
        $error = "You don't have permission to update parent profiles.";
    } else {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $phone = mysqli_real_escape_string($connect, $_POST['phone']);
        $isSubscribed = isset($_POST['is_subscribed']) ? 1 : 0;
        
        // Update query
        $update = "UPDATE parents SET 
                   ParentName = '$name',
                   ParentEmail = '$email',
                   ParentNumber = '$phone',
                   Is_Subscribed = '$isSubscribed'
                   WHERE ParentID = '$parentId'";
                   
        if (mysqli_query($connect, $update)) {
            $success = "Parent information updated successfully!";
            // Refresh parent data
            $result = mysqli_query($connect, $query);
            $parent = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating parent information: " . mysqli_error($connect);
        }
    }
}

// Handle removing a child-parent relation
if (isset($_POST['remove_child']) && isset($_POST['child_id'])) {
    $childId = mysqli_real_escape_string($connect, $_POST['child_id']);
    $removeQuery = "DELETE FROM family WHERE ParentID = '$parentId' AND StudentID = '$childId'";
    if (mysqli_query($connect, $removeQuery)) {
        $success = "Child removed from this parent successfully!";
        // Refresh children list
        $childrenResult = mysqli_query($connect, $childrenQuery);
        $childrenCount = mysqli_num_rows($childrenResult);
    } else {
        $error = "Error removing child: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Profile - <?php echo htmlspecialchars($parent['ParentName']); ?></title>
    <link rel="stylesheet" href="css/TeacherProfile.css">
    <link rel="stylesheet" href="css/ParentProfile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .add-child-btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .add-child-btn:hover {
            background-color: #219a52;
        }
        .add-child-btn i {
            font-size: 1.1rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Parent Profile</h1>
    </div>

    <div class="edit-section">
        <!-- Dashboard Summary -->
        <div class="dashboard-summary">
            <div class="summary-card">
                <i class="fas fa-children"></i>
                <div class="summary-info">
                    <h3><?php echo $childrenCount; ?></h3>
                    <p>Children</p>
                </div>
            </div>
            <div class="summary-card">
                <i class="<?php echo $parent['Is_subscribed'] ? 'fas fa-crown' : 'fas fa-user'; ?>"></i>
                <div class="summary-info">
                    <h3><?php echo $parent['Is_subscribed'] ? 'Premium' : 'Free'; ?></h3>
                    <p>Subscription Plan</p>
                </div>
            </div>
        </div>

        <div class="edit-container">
            <?php if (isset($success)): ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="profile-section">
                    <div class="profile-info wide">
                        <div class="info-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($parent['ParentName']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($parent['ParentEmail']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($parent['ParentNumber']); ?>"
                                   <?php echo !$isAdministrator ? 'readonly' : ''; ?> required>
                        </div>

                        <div class="info-group checkbox-group">
                            <label for="is_subscribed">Premium Subscription</label>
                            <input type="checkbox" id="is_subscribed" name="is_subscribed" 
                                   <?php echo $parent['Is_subscribed'] ? 'checked' : ''; ?>
                                   <?php echo !$isAdministrator ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                </div>

                <div class="buttons">
                    <a href="AllParents.php" class="btn back-btn">Back to All Parents</a>
                    <?php if ($isAdministrator): ?>
                        <button type="submit" name="update" class="btn">Update Profile</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Children Section -->
        <div class="section-header">
            <h2 class="section-title">Children Information</h2>
            <form method="post" style="display: inline;">
                <button type="submit" name="add_child" class="add-child-btn">
                    <i class="fas fa-plus"></i> Add Child
                </button>
            </form>
        </div>
        <div class="children-cards">
            <?php while($child = mysqli_fetch_assoc($childrenResult)): ?>
                <div class="child-card">
                    <div class="child-image">
                        <img src="../Media/<?php echo htmlspecialchars($child['Picture']); ?>" 
                             alt="<?php echo htmlspecialchars($child['StudentName']); ?>">
                    </div>
                    <div class="child-details">
                        <h3 class="child-name">
                            <a href="StudentProfile.php?id=<?php echo $child['StudentID']; ?>">
                                <?php echo htmlspecialchars($child['StudentName']); ?>
                            </a>
                        </h3>
                        <p class="child-info">
                            <strong>Grade:</strong> <?php echo htmlspecialchars($child['GradeNumber']); ?>
                        </p>
                        <p class="child-info">
                            <strong>Class:</strong> <?php echo htmlspecialchars($child['ClassName']); ?>
                        </p>
                        <p class="child-info">
                            <strong>Email:</strong> <?php echo htmlspecialchars($child['StudentEmail']); ?>
                        </p>
                        <p class="child-info">
                            <strong>Phone:</strong> <?php echo htmlspecialchars($child['StudentNumber']); ?>
                        </p>
                        <div class="child-stats">
                            <div class="stat">
                                <i class="fas fa-money-bill"></i>
                                <span>Fees: <?php echo number_format($child['FeeAmount'], 2); ?> EGP</span>
                            </div>
                        </div>
                        <form method="post" onsubmit="return confirm('Are you sure you want to remove this child from this parent?');" style="margin-top:10px;">
                            <input type="hidden" name="child_id" value="<?php echo $child['StudentID']; ?>">
                            <button type="submit" name="remove_child" class="btn btn-danger btn-sm">
                                <i class="fas fa-user-minus"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
