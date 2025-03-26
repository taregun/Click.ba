<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('includes/functions.php');

// Handle search request
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Determine sorting order
$sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'asc' : 'desc';

// Get all posts
$posts = getAllPosts();

// Filter posts by title if search query is provided
if ($searchQuery) {
    $posts = array_filter($posts, function ($post) use ($searchQuery) {
        return stripos($post['title'], $searchQuery) !== false;
    });
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Home - Click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".like-btn").click(function() {
                var postId = $(this).data("post-id");

                $.post("like_post.php", { post_id: postId }, function(response) {
                    if (response.success) {
                        $("#likes-" + postId).text(response.likes);
                    } else {
                        alert(response.error || "Failed to like the post.");
                    }
                }, "json");
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
        <button type="submit" name="sort" value="<?= $sortOrder === 'asc' ? 'desc' : 'asc' ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <?= $sortOrder === 'asc' ? 'Sort: Oldest to Newest' : 'Sort: Newest to Oldest' ?>
        </button>
        <input type="text" name="search" placeholder="Search by title" value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
        <button type="submit" style="width: 100%; padding: 10px;">Search</button>
    </form>

    <?php
    if ($posts):
        if ($sortOrder === 'desc') {
            $posts = array_reverse($posts);
        }

        foreach ($posts as $post) {
            $user = getUserById($post['author']);
            if (!$user) {
                echo "<p>Author not found for post: " . htmlspecialchars($post['title']) . "</p>";
                continue;
            }

            $profilePic = isset($user['profile_pic']) ? $user['profile_pic'] : 'blankProfile.png';
            $userName = isset($user['name']) ? $user['name'] : 'Anonymous';
            $likes = isset($post['likes']) ? $post['likes'] : 0;

            echo "<div class='post'>";
            echo "<img src='images/" . htmlspecialchars($profilePic) . "' alt='Profile Picture' class='profile-pic'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";

            if (!empty($post['image'])) {
                echo "<img src='images/" . htmlspecialchars($post['image']) . "' alt='Post Image' class='post-image'>";
            }

            echo "<p><small>By " . htmlspecialchars($userName) . " on " . htmlspecialchars($post['date']) . "</small></p>";
            
            // Likes display and Like button aligned on the same line
            echo "<div style='display: flex; justify-content: space-between; align-items: center;'>
                    <p><b>Likes:</b> <span id='likes-" . htmlspecialchars($post['id']) . "'>" . $likes . "</span></p>
                    <span class='like-btn' data-post-id='" . htmlspecialchars($post['id']) . "' 
                          style='cursor: pointer; font-size: 32px; margin-left: 10px; vertical-align: middle;'>👍</span>
                  </div>";

            echo "</div>";
        }
    else:
        echo "<p>No posts available.</p>";
    endif;
    ?>
</body>
</html>
