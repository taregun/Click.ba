<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('includes/functions.php');

// Handle search request
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Determine sorting order
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'desc';

// Get the user ID from the URL (if provided)
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

// Get posts based on the user ID (if any)
if ($userId) {
    // Fetch posts by this user
    $posts = getPostsByUser($userId);
} else {
    // Get all posts as usual
    $posts = getAllPosts();
}

// Filter posts by title if search query is provided
if ($searchQuery) {
    $posts = array_filter($posts, function ($post) use ($searchQuery) {
        return stripos($post['title'], $searchQuery) !== false;
    });
}

// Sort posts based on the selected option
switch ($sortOrder) {
    case 'asc':
        usort($posts, fn($a, $b) => strtotime($a['date']) - strtotime($b['date'])); // Oldest to Newest
        break;
    case 'desc':
        usort($posts, fn($a, $b) => strtotime($b['date']) - strtotime($a['date'])); // Newest to Oldest
        break;
    case 'random':
        shuffle($posts); // Random order
        break;
    case 'alpha':
        usort($posts, fn($a, $b) => strcmp($a['title'], $b['title'])); // Alphabetical Order (A to Z)
        break;
}

// Define the next sorting order in the cycle
$nextSortOrder = match ($sortOrder) {
    'asc' => 'desc',
    'desc' => 'random',
    'random' => 'alpha',
    'alpha' => 'asc',
    default => 'desc',
};
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="images/gmailprof.png" type="image/png">
    <title>Home - Click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        $(document).on('click', '.like-btn', function() {
            var postId = $(this).data('post-id');
            
            $.post("like_post.php", { post_id: postId }, function(response) {
                if (response.success) {
                    $("#likes-" + postId).text(response.likes);
                } else {
                    alert(response.error || "Failed to like or unlike the post.");
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ": " + errorThrown);
                alert("Error occurred while liking or unliking the post.");
            });
        });

        $(document).on('click', '.dislike-btn', function() {
            var postId = $(this).data('post-id');
    
            $.post("dislike_post.php", { post_id: postId }, function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $("#likes-" + postId).text(data.likes);
                    } else {
                        alert(data.error || "Failed to dislike the post.");
                    }
                } catch (e) {
                    console.error("Error parsing response: " + e.message);
                    alert("Error occurred while disliking the post.");
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ": " + errorThrown);
                alert("Error occurred while disliking the post.");
            });
});


    </script>

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
    <h1>Welcome to click.ba</h1>

    <form action="" method="get" style="text-align:center; margin-top: 20px; width: 50%; max-width: 500px;">
        <button type="submit" name="sort" value="<?= $nextSortOrder ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            Sort: <?= match ($sortOrder) {
                'asc' => 'Oldest to Newest',
                'desc' => 'Newest to Oldest',
                'random' => 'Random',
                'alpha' => 'Alphabetical (A to Z)',
                default => 'Newest to Oldest'
            } ?>
        </button>
        <input type="text" name="search" placeholder="Search by title" value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
        <button type="submit" style="width: 100%; padding: 10px;">Search</button>
    </form>

    <?php if ($posts): foreach ($posts as $post): ?>
        <?php 
        $user = getUserById($post['author']);
        if (!$user) continue;
        ?>

        <div class='post'>
            <img src='images/<?= htmlspecialchars($user["profile_pic"] ?? "blankProfile.png") ?>' alt='Profile Picture' class='profile-pic'>
            <h3><?= htmlspecialchars($post['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            <?php if (!empty($post['image'])): ?>
                <img src='images/<?= htmlspecialchars($post['image']) ?>' alt='Post Image' class='post-image'>
            <?php endif; ?>
            <p><small>By <?= htmlspecialchars($user['name'] ?? 'Anonymous') ?> on <?= htmlspecialchars($post['date']) ?></small></p>
            
            <!-- Likes display and Like button aligned on the same line -->
            <div style='display: flex; justify-content: space-between; align-items: center;'>
                <p><b>Likes:</b> <span id='likes-<?= htmlspecialchars($post['id']) ?>'><?= $post['likes'] ?></span></p>
                <span class='like-btn' data-post-id='<?= htmlspecialchars($post['id']) ?>' 
                      style='cursor: pointer; font-size: 32px; margin-left: 10px; vertical-align: middle;'>👍</span>
                <span class='dislike-btn' data-post-id='<?= htmlspecialchars($post['id']) ?>' 
                    style='cursor: pointer; font-size: 32px; margin-left: 10px; vertical-align: middle;'>👎</span>

            </div>
        </div>
    <?php endforeach; else: ?>
        <p>No posts available.</p>
    <?php endif; ?>
</body>
</html>
