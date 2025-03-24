<?php
session_start();
include('includes/functions.php'); // Ensure this file contains necessary functions like saveUser and hashPassword

$error_message = ""; // Variable to store error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input (basic example)
    if (empty($name) || empty($email) || empty($password)) {
        $error_message = "Please fill all fields.";
    } else {
        // Check if email already exists in users.json
        $users = json_decode(file_get_contents(USER_FILE), true);
        foreach ($users as $user) {
            if ($user['email'] == $email) {
                $error_message = "This email is already registered. If you forgot your password, you can reset it.";
                break;
            }
        }
    }

    if (empty($error_message)) {
        // Handle profile picture upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $target_dir = "images/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = array("jpg", "jpeg", "png", "gif");

            if (!in_array($file_type, $allowed_types)) {
                $error_message = "Invalid image format.";
            } elseif (!move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $error_message = "Error uploading image.";
            } else {
                $profile_pic = basename($_FILES["profile_pic"]["name"]);
            }
        } else {
            $profile_pic = null;
        }

        if (empty($error_message)) {
            // Hash password before saving
            $hashed_password = hashPassword($password);

            // Create user and save to users.json
            $new_user = [
                'id' => uniqid(),  
                'name' => $name,
                'email' => $email,
                'password' => $hashed_password,
                'profile_pic' => $profile_pic
            ];

            // Load existing users from users.json
            $users[] = $new_user;
            file_put_contents(USER_FILE, json_encode($users, JSON_PRETTY_PRINT));

            // Log the user in after registration
            $_SESSION['user_id'] = $new_user['id'];
            $_SESSION['user_name'] = $new_user['name'];
            $_SESSION['profile_pic'] = $new_user['profile_pic'];

            // Redirect to homepage or profile page
            header("Location: index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - click.ba</title>
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
    <h1>Sign Up</h1> 
    <form method="POST" enctype="multipart/form-data" style="width: 50%; margin: 0 auto; box-sizing: border-box;">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <p>Add profile picture here:</p>
        <input type="file" name="profile_pic"><br>
        <button type="submit" style="width: 100%;">Sign Up</button>
        
        <!-- Show error message under the form -->
        <?php if (!empty($error_message)) : ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
