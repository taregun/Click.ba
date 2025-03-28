<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();  // Start the session to get the logged-in user's ID

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'You must be logged in to dislike or undo dislike.']);
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

    // Find the post and check if the user has already disliked it
    $postFound = false;
    $userDisliked = false;

    foreach ($data as &$post) {
        if ($post['id'] === $postId) {
            $postFound = true;

            // Check if the user has already disliked the post
            if (in_array($userId, $post['disliked_by'])) {
                // If the user has already disliked it, undo the dislike
                $userDisliked = true;
                // Remove the user from the disliked_by array and increase the like count
                $post['disliked_by'] = array_diff($post['disliked_by'], [$userId]);
                $post['likes'] += 1; // If you want to increase the like count when undoing dislike
            } else {
                // If the user hasn't disliked the post, add them and decrease likes
                $post['disliked_by'][] = $userId;
                $post['likes'] -= 1;
            }
            break;
        }
    }

    if (!$postFound) {
        echo json_encode(['success' => false, 'error' => 'Post not found.']);
        exit;
    }

    // If the user disliked or un-disliked the post, send the response
    echo json_encode(['success' => true, 'likes' => $post['likes'], 'action' => $userDisliked ? 'undisliked' : 'disliked']);
    
    // Write the updated data back to the JSON file
    if (!file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => false, 'error' => 'Failed to update JSON file.']);
    }
}
?>
