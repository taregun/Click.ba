<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();  // Start the session to get the logged-in user's ID

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'You must be logged in to like or unlike a post.']);
        exit;
    }

    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];  // Use the logged-in user's ID from the session
    $filePath = './data/posts.json';

    // Read the JSON data
    $data = json_decode(file_get_contents($filePath), true);

    if ($data === null) {
        echo json_encode(['success' => false, 'error' => 'Failed to read JSON file.']);
        exit;
    }

    // Find the post and check if the user has already liked it
    $postFound = false;
    $userLiked = false;

    foreach ($data as &$post) {
        if ($post['id'] === $postId) {
            $postFound = true;

            // Check if the user has already liked the post
            if (in_array($userId, $post['liked_by'])) {
                // If the user has already liked it, unlike it
                $userLiked = true;
                // Remove the user from the liked_by array and decrease the like count
                $post['liked_by'] = array_diff($post['liked_by'], [$userId]);
                $post['likes'] -= 1;
            } else {
                // If the user hasn't liked the post, add them and increase likes
                $post['liked_by'][] = $userId;
                $post['likes'] += 1;
            }
            break;
        }
    }

    if (!$postFound) {
        echo json_encode(['success' => false, 'error' => 'Post not found.']);
        exit;
    }

    // If the user liked or unliked the post, send the response
    echo json_encode(['success' => true, 'likes' => $post['likes'], 'action' => $userLiked ? 'unliked' : 'liked']);
    
    // Write the updated data back to the JSON file
    if (!file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => false, 'error' => 'Failed to update JSON file.']);
    }
}
?>
