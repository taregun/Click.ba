<?php
// Define file paths for users and posts
define('USER_FILE', 'data/users.json');  // Path to users data file
define('POST_FILE', 'data/posts.json');  // Path to posts data file

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to check if password is correct
function checkPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

// Function to get all users from the file
function getAllUsers() {
    if (!file_exists(USER_FILE)) {
        return [];
    }

    $data = file_get_contents(USER_FILE);
    return json_decode($data, true) ?: [];
}

// Function to get a user by email
function getUserByEmail($email) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

// Function to save a new user to the file
function saveUser($user) {
    $users = getAllUsers();
    $users[] = $user;
    file_put_contents(USER_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

// Function to save posts
function savePost($title, $content, $user_id, $image = null) {
    $postsFile = 'data/posts.json';
    
    // Read existing posts
    $posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    // Generate unique ID
    $post_id = uniqid();

    // Get current timestamp
    $date = date('Y-m-d H:i:s');

    // Fetch user details
    $user = getUserById($user_id);

    // Create new post data
    $newPost = [
        'id' => $post_id,
        'title' => $title,
        'content' => $content,
        'author' => $user_id,
        'icon' => $user['profile_pic'] ?? 'default.png', // Keep profile pic
        'date' => $date,
        'image' => $image // Store the uploaded image filename
    ];

    // Add new post to array
    $posts[] = $newPost;

    // Save back to JSON file
    file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));
}


// Function to get all posts
function getAllPosts() {
    
    
    if (!file_exists(POST_FILE)) {
        error_log("test", 3, "errors.log");
        return [];
    }
    
    $data = file_get_contents(POST_FILE);
    return json_decode($data, true) ?: [];
}

function getUserById($id) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return $user;
        }
    }
    return null;
}

?>
