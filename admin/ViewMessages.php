<?php
include '../connection.php';

// Fetch all messages from contactus table
$query = "SELECT * FROM contactus ORDER BY ContactID DESC";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us Messages</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .messages-table-container {
            width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 30px;
        }
        .messages-table {
            width: 100%;
            border-collapse: collapse;
        }
        .messages-table th, .messages-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .messages-table th {
            background: #f7f7f7;
            color: #0C3B2E;
        }
        .messages-table tr:hover {
            background: #f0f2f1;
        }
        .back-btn {
            margin-bottom: 20px;
            display: inline-block;
            color: #fff;
            background: #0C3B2E;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }
        .back-btn:hover {
            background: #6d9773;
            color: #fff;
        }
        @media (max-width: 950px) {
            .messages-table-container { width: 98vw; padding: 8px; }
            .messages-table th, .messages-table td { font-size: 0.95em; }
        }
    </style>
</head>
<body>
    <div>
        <div class="messages-table-container">
            <a href="Dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
            <h2 style="margin-bottom:20px;">Contact Us Messages</h2>
            <div style="overflow-x:auto;">
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(mysqli_num_rows($result) > 0) {
                        $i = 1;
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $i++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['PhoneNumber']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Subject']) . "</td>";
                            echo "<td>" . nl2br(htmlspecialchars($row['Message'])) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No messages found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js"></script>
</body>
</html>
