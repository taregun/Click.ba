<?php
session_start();
include('includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user ID
    $result = likePost($post_id, $user_id);


    // Get updated post likes
    $posts = getAllPosts();
    $likes = 0;
    foreach ($posts as $post) {
        if ($post['id'] === $post_id) {
            $likes = $post['likes'];
            break;
        }
    }

    echo json_encode(["success" => true, "likes" => $likes]);
    exit;
}

echo json_encode(["success" => false]);
?>
