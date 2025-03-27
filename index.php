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
if ($sortOrder === 'asc') {
    usort($posts, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']); // Oldest to Newest
    });
} elseif ($sortOrder === 'desc') {
    usort($posts, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']); // Newest to Oldest
    });
} elseif ($sortOrder === 'random') {
    shuffle($posts); // Random order
} elseif ($sortOrder === 'alpha') {
    usort($posts, function ($a, $b) {
        return strcmp($a['title'], $b['title']); // Alphabetical Order (A to Z)
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
        $(document).on('click', '.like-btn', function() {
            var postId = $(this).data('post-id');
            
            $.post("like_post.php", { post_id: postId }, function(response) {
                if (response.success) {
                    $("#likes-" + postId).text(response.likes);
                } else {
                    alert(response.error || "Failed to like the post.");
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ": " + errorThrown);
                alert("Error occurred while liking the post.");
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
        <button type="submit" name="sort" value="<?= $sortOrder === 'asc' ? 'desc' : ($sortOrder === 'random' ? 'asc' : ($sortOrder === 'alpha' ? 'desc' : 'alpha')) ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
            <?php
            if ($sortOrder === 'asc') {
                echo 'Sort: Oldest to Newest';
            } elseif ($sortOrder === 'desc') {
                echo 'Sort: Newest to Oldest';
            } elseif ($sortOrder === 'alpha') {
                echo 'Sort: Alphabetical (A to Z)';
            } else {
                echo 'Sort: Random';
            }
            ?>
        </button>
        <input type="text" name="search" placeholder="Search by title" value="<?= htmlspecialchars($searchQuery) ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;">
        <button type="submit" style="width: 100%; padding: 10px;">Search</button>
    </form>

    <?php
    if ($posts):
        foreach ($posts as $post) {
            $user = getUserById($post['author']);
            if (!$user) {
                echo "<p>Author not found for post: " . htmlspecialchars($post['title']) . "</p>";
                continue;
            }

            $profilePic = isset($user['profile_pic']) ? $user['profile_pic'] : 'blankProfile.png';
            $userName = isset($user['name']) ? $user['name'] : 'Anonymous';
            $likes = isset($post['likes']) ? $post['likes'] : 0;
            $userId = $user['id']; // Assuming the user has an 'id' field

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
