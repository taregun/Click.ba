<?php  
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($title) || empty($content)) {
        echo "Please fill all fields.";
        exit;
    }

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_folder = "images/" . $image;

        // Check if the file is a valid image
        $file_type = strtolower(pathinfo($image_folder, PATHINFO_EXTENSION));
        $allowed_types = array("jpg", "jpeg", "png", "gif");

        if (!in_array($file_type, $allowed_types)) {
            echo "Invalid image format.";
            exit;
        }

        // Move the uploaded image to the "images/" folder
        if (!move_uploaded_file($image_tmp, $image_folder)) {
            echo "Error uploading image.";
            exit;
        }
    }

    // Include the savePost function from functions.php
    include('includes/functions.php');
    savePost($title, $content, $user_id, $image);  // Pass image filename to savePost

    // Redirect after successful post creation
    header('Location: index.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/gmailprof.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - click.ba</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <?php include('includes/header.php'); ?>
    <h1>Create a Post</h1>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Post Title" required><br>
        <textarea name="content" placeholder="Write your post here..." required></textarea><br>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit">Create Post</button>
    </form>
</body>
</html>
