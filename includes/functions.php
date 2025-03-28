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
        'likes' => 0,
        "liked_by" => [],
        "disliked_by" => []

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

// Fetch all posts by a specific user
function getPostsByUser($userId) {
    $posts = getAllPosts(); // Get all posts from the JSON file
    $userPosts = [];

    // Loop through all posts and add those that match the user_id
    foreach ($posts as $post) {
        if ($post['author'] == $userId) {
            $userPosts[] = $post;
        }
    }

    return $userPosts;
}

function toggleLikePost($post_id, $user_id) {
    $posts = getAllPosts();
    
    foreach ($posts as &$post) {
        if ($post['id'] === $post_id) {
            // Initialize liked_by and disliked_by arrays if not set
            if (!isset($post['liked_by'])) {
                $post['liked_by'] = [];
            }
            if (!isset($post['disliked_by'])) {
                $post['disliked_by'] = [];
            }
            
            // Check if the user already liked or disliked the post
            if (in_array($user_id, $post['liked_by'])) {
                // User has liked the post, so unlike it
                $post['liked_by'] = array_diff($post['liked_by'], [$user_id]);
                $post['likes'] -= 1;
                return 'unliked';
            }
            
            if (in_array($user_id, $post['disliked_by'])) {
                // User has disliked the post, so undo the dislike (remove from disliked_by)
                $post['disliked_by'] = array_diff($post['disliked_by'], [$user_id]);
                $post['likes'] += 1;
                return 'undisliked';
            }
            
            // If the user hasn't liked or disliked the post, toggle the action (like or dislike)
            $post['liked_by'][] = $user_id;
            $post['likes'] += 1;
            return 'liked';
        }
    }
    return "Post not found!";
}

?>
