<?php
define('USER_FILE', 'data/users.json');  // Path to users data file
define('POST_FILE', 'data/posts.json');  // Path to posts data file

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function checkPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

function getAllUsers() {
    if (!file_exists(USER_FILE)) {
        return [];
    }
    return json_decode(file_get_contents(USER_FILE), true) ?: [];
}

function getUserByEmail($email) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}

function saveUser($user) {
    $users = getAllUsers();
    $users[] = $user;
    file_put_contents(USER_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function savePost($title, $content, $user_id, $image = null) {
    $posts = getAllPosts();
    $post_id = uniqid();
    $date = date('Y-m-d H:i:s');
    $user = getUserById($user_id);

    $newPost = [
        'id' => $post_id,
        'title' => $title,
        'content' => $content,
        'author' => $user_id,
        'icon' => $user['profile_pic'] ?? 'default.png',
        'date' => $date,
        'image' => $image,
        'likes' => 0
    ];

    $posts[] = $newPost;
    file_put_contents(POST_FILE, json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getAllPosts() {
    if (!file_exists(POST_FILE)) {
        return [];
    }

    $posts = json_decode(file_get_contents(POST_FILE), true) ?: [];
    
    // Ensure each post has a 'likes' field
    foreach ($posts as &$post) {
        if (!isset($post['likes'])) {
            $post['likes'] = 0;
        }
    }

    return $posts;
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

// Function to like a post
function likePost($post_id, $user_id) {
    $posts = getAllPosts();
    
    foreach ($posts as &$post) {
        if ($post['id'] === $post_id) {
            // Initialize liked_by if not set
            if (!isset($post['liked_by'])) {
                $post['liked_by'] = [];
            }
            
            // Check if the user already liked the post
            if (in_array($user_id, $post['liked_by'])) {
                return "You have already liked this post!";
            }
            
            // Add user to liked_by and increase likes count
            $post['liked_by'][] = $user_id;
            $post['likes'] += 1;
            
            // Save updated posts
            file_put_contents(POST_FILE, json_encode($posts, JSON_PRETTY_PRINT));
            return "Post liked successfully!";
        }
    }
    return "Post not found!";
}

?>
