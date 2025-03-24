<?php
session_start();
include('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        echo "Please fill all fields.";
        exit;
    }

    // Get user from file
    $user = getUserByEmail($email);

    if ($user && checkPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['profile_pic'] = $user['profile_pic'];
        // Redirect to the welcome screen
        header('Location: welcome.php');
        exit;
    } else {
        echo "Invalid login credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - click.ba</title>
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
    <h1>Login</h1>
    <form method="POST" style="width: 50%; margin: 0 auto; box-sizing: border-box;">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" style="width: 100%;">Login</button>
        <a href="register.php">Forgot password? Sign Up</a>
    </form>
</body>
</html>
