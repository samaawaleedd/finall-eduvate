<?php
include '../connection.php';

// Updated color palette to match interface greens and yellows
$primary = "#43a047";         // Green
$primary_dark = "#357a38";    // Darker green for hover
$accent = "#fbc02d";          // Yellow accent
$bg = "#f4f6f8";
$card_bg = "#fff";
$text_main = "#2d3e50";
$text_sub = "#34495e";
$text_muted = "#888";
$success = "#43a047";         // Green for success
$error = "#c62828";           // Red for errors

// Handle deletion with redirect to avoid double delete and "not found" on refresh
$delete_msg = "";
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $img_q = mysqli_query($connect, "SELECT Pics FROM news WHERE NewsID=$del_id");
    if ($img_q && $img_row = mysqli_fetch_assoc($img_q)) {
        $img_file = $img_row['Pics'];
        $del_q = mysqli_query($connect, "DELETE FROM news WHERE NewsID=$del_id");
        if ($del_q) {
            if ($img_file && file_exists("../Media/$img_file")) {
                unlink("../Media/$img_file");
            }
            // Redirect after successful delete
            header("Location: AddPost.php");
            exit;
        } else {
            $delete_msg = "<div class='msg-error'>Failed to delete announcement.</div>";
        }
    } else {
        // Only show this if not a redirect after delete
        $delete_msg = "<div class='msg-error'>Announcement not found.</div>";
    }
}
if (isset($_GET['deleted'])) {
    $delete_msg = "<div class='msg-success'>Announcement deleted successfully.</div>";
}

// Handle form submission
$form_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $desc = trim($_POST['desc']);
    $pic = $_FILES['pic'];

    $errors = [];

    // Validate required fields
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($pic['name'])) {
        $errors[] = "Picture is required.";
    }

    // Handle file upload
    if (empty($errors)) {
        $target_dir = "../Media/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $pic_name = uniqid() . "_" . basename($pic["name"]);
        $target_file = $target_dir . $pic_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (move_uploaded_file($pic["tmp_name"], $target_file)) {
            // Insert into database using simple PHP
            $title_esc = mysqli_real_escape_string($connect, $title);
            $desc_esc = mysqli_real_escape_string($connect, $desc);
            $pic_esc = mysqli_real_escape_string($connect, $pic_name);
            $sql = "INSERT INTO news (Title, Pics, `desc`) VALUES ('$title_esc', '$pic_esc', " . ($desc_esc === "" ? "NULL" : "'$desc_esc'") . ")";
            if (mysqli_query($connect, $sql)) {
                $form_msg = "<div class='msg-success'>News post added successfully!</div>";
            } else {
                $form_msg = "<div class='msg-error'>Database error: " . mysqli_error($connect) . "</div>";
            }
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            $form_msg .= "<div class='msg-error'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/bootstrap.min.css">    <title>Add News Post</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: <?= $bg ?>;
            margin: 0;
            padding: 0;
        }
        .container {
            background: <?= $card_bg ?>;
            width: 50%;
            margin: 40px auto 0 auto;
            padding: 32px 32px 24px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        }
        h2 {
            text-align: center;
            color: <?= $primary ?>;
            margin-bottom: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        label {
            font-weight: 500;
            color: <?= $text_sub ?>;
            margin-bottom: 4px;
            display: block;
        }
        input[type="text"], textarea {
            width: 98%;
            padding: 10px 12px;
            margin-top: 6px;
            margin-bottom: 18px;
            border: 1px solid #cfd8dc;
            border-radius: 6px;
            background: #f9fafb;
            font-size: 15px;
            transition: border 0.2s;
        }
        input[type="text"]:focus, textarea:focus {
            border-color: <?= $accent ?>;
            outline: none;
        }
        input[type="file"] {
            margin-top: 6px;
            margin-bottom: 18px;
        }
        input[type="submit"] {
            background: <?= $primary ?>;
            color: #fff;
            border: none;
            padding: 12px 0;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        input[type="submit"]:hover {
            background: <?= $primary_dark ?>;
        }
        .note {
            color: <?= $accent ?>;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .msg-success {
            color: <?= $primary ?>;
            background: #e8f5e9;
            border: 1px solid #b2dfdb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 18px;
            text-align: center;
        }
        .msg-error {
            color: <?= $error ?>;
            background: #ffebee;
            border: 1px solid #ffcdd2;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 18px;
            text-align: center;
        }
        .back-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            font-size: 15px;
            background: <?= $primary?>;
            width: 100%;
            text-align: center;
            border-radius: 5px;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: <?= $primary_dark ?>;
            text-decoration: underline;
        }
        .news-section {
            max-width: 1000px;
            margin: 40px auto 0 auto;
            padding: 0 28px 40px 28px;
        }
        .news-title {
            color: <?= $primary ?>;
            font-size: 22px;
            margin-bottom: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .news-list {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            justify-content: flex-start;
        }
        .news-card {
            background: <?= $card_bg ?>;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            width: 270px;
            padding: 18px 16px 16px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            margin-bottom: 8px;
            transition: box-shadow 0.2s;
            border-top: 4px solid <?= $accent ?>;
        }
        .news-card:hover {
            box-shadow: 0 6px 24px rgba(67,160,71,0.10);
        }
        .news-card img {
            width: 100%;
            max-width: 220px;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
            background: <?= $bg ?>;
            border: 1px solid #e0e0e0;
        }
        .news-card .news-card-title {
            font-size: 17px;
            font-weight: 600;
            color: <?= $primary ?>;
            margin-bottom: 8px;
            text-align: center;
        }
        .news-card .news-card-desc {
            font-size: 14px;
            color: <?= $text_sub ?>;
            text-align: center;
            min-height: 40px;
        }
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: <?= $error ?>;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .delete-btn:hover {
            background: #a31515;
        }

        .test{
            width: 98%;
        }

        @media (max-width: 900px) {
            .news-list { flex-direction: column; align-items: center; }
            .news-card { width: 95%; }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Add News/Announcement</h2>
        <!-- Display messages with new classes -->
        <?php
        // Show delete or form messages
        if (!empty($delete_msg)) echo $delete_msg;
        if (!empty($form_msg)) echo $form_msg;
        ?>
        <form method="post" enctype="multipart/form-data">
            <label for="title">Title<span style="color:red;">*</span>:</label>
            <input type="text" id="title" name="title" required>

            <label for="pic" >Picture<span style="color:red;">*</span>:</label> 
            <input type="file" class="form-control test" id="pic" name="pic" accept="image/*" required>

            <div class="note">Only JPG, JPEG, PNG, GIF allowed.</div>

            <label for="desc">Description:</label>
            <textarea id="desc" name="desc" rows="4" cols="50" placeholder="Optional"></textarea>

            <input type="submit" value="Add Post">
             <a href="Dashboard.php" class="back-link  ">&larr; Back to Dashboard</a>
        </form>
    </div>

    <!-- News Section -->
    <div class="news-section">
        <div class="news-title">All News & Announcements</div>
        <div class="news-list">
        <?php
        $news_q = mysqli_query($connect, "SELECT * FROM news ORDER BY NewsID DESC");
        if ($news_q && mysqli_num_rows($news_q) > 0) {
            while ($row = mysqli_fetch_assoc($news_q)) {
                $img = !empty($row['Pics']) ? "../Media/" . htmlspecialchars($row['Pics']) : "";
                $title = htmlspecialchars($row['Title']);
                $desc = htmlspecialchars($row['desc']);
                $newsid = intval($row['NewsID']);
                echo '<div class="news-card">';
                // Delete button
                echo '<form method="get" style="position:absolute;top:10px;right:10px;">
                        <input type="hidden" name="delete" value="' . $newsid . '">
                        <button type="submit" class="delete-btn" onclick="return confirm(\'Delete this announcement?\')">Delete</button>
                      </form>';
                if ($img) {
                    echo '<img src="' . $img . '" alt="News Image">';
                }
                echo '<div class="news-card-title">' . $title . '</div>';
                echo '<div class="news-card-desc">' . ($desc ? $desc : '<span style=\'color:' . $text_muted . ';\' >No description</span>') . '</div>';
                echo '</div>';
            }
        } else {
            echo '<div style="color:' . $text_muted . ';">No news posts found.</div>';
        }
        ?>
        </div>
    </div>
</body>
</html>
