<?php
session_start();
include('includes/functions.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch the user data using the user_id from the session
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            padding: 20px;
        }
        .profile-header {
            margin-bottom: 20px;
        }
        .profile-pic {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .user-info {
            display: inline-block;
            text-align: left;
            padding: 10px;
            margin-top: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }
        .user-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>

    <?php include('includes/header.php'); ?>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <div class="container">
        <div class="profile-header">
            <h1>Profile - <?php echo htmlspecialchars($user['name']); ?></h1>
            <img src="images/<?php echo htmlspecialchars($user['profile_pic'] ?? 'blankProfile.png'); ?>" alt="Profile Picture">
        </div>

        <div class="user-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Profile Picture:</strong> <?php echo htmlspecialchars($user['profile_pic']) ?: 'No profile picture set'; ?></p>
        </div>
    </div>

</body>
</html>
