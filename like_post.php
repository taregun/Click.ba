<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'You must be logged in to like a post.']);
        exit;
    }

    $postId = $_POST['post_id'];
    $userId = $_SESSION['user_id'];
    $filePath = './data/posts.json';

    $data = json_decode(file_get_contents($filePath), true);

    if ($data === null) {
        echo json_encode(['success' => false, 'error' => 'Failed to read JSON file.']);
        exit;
    }

    $postFound = false;

    foreach ($data as &$post) {
        if ($post['id'] === $postId) {
            $postFound = true;

            // Initialize like tracking if missing
            if (!isset($post['liked_by'])) $post['liked_by'] = [];
            if (!isset($post['likes'])) $post['likes'] = 0;

            // If the user hasn’t liked the post yet, add them
            if (!in_array($userId, $post['liked_by'])) {
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

    // Save updated data
    if (file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true, 'likes' => $post['likes']]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update JSON file.']);
    }
}
?>
