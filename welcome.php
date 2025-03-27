<?php
session_start();
include('includes/functions.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$posts = getAllPosts();

$user_posts = array_filter($posts, function($post) use ($user_id) {
    return $post['author'] === $user_id;
});
?>

<!DOCTYPE html>
<html>
<head>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <p>.</p>
    <title>Welcome - click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .center-content { text-align: center; margin: 0 auto; }
        .profile-pic { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; }
        .post { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .post img { max-width: 100%; height: auto; }
        .post h3 { margin: 0; }
        .post p { font-size: 16px; }
        .likes { margin-top: 10px; font-size: 18px; }
        .like-btn, .dislike-btn { cursor: pointer; font-size: 24px; margin: 0 10px; }
        .like-btn { color: #007BFF; }
        .dislike-btn { color: #FF0000; }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="center-content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>

        <?php
        if (isset($_SESSION['profile_pic']) && $_SESSION['profile_pic']) {
            echo '<img src="images/' . htmlspecialchars($_SESSION['profile_pic']) . '" class="profile-pic" alt="Profile Picture">';
        }

        if ($user_posts) {
            foreach ($user_posts as $post) {
                $profilePic = isset($post['icon']) ? $post['icon'] : 'blankProfile.png';
                $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Anonymous';

                echo "<div class='post'>";
                echo "<img src='images/" . htmlspecialchars($profilePic) . "' alt='Profile Picture' class='profile-pic'>";
                echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
                echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";

                if (!empty($post['image'])) {
                    echo "<img src='images/" . htmlspecialchars($post['image']) . "' alt='Post Image' class='post-image'>";
                }

                $likes = isset($post['likes']) ? $post['likes'] : 0;
                $dislikes = isset($post['dislikes']) ? $post['dislikes'] : 0;

                echo "<div class='likes'>";
                echo "<span id='likes-" . htmlspecialchars($post['id']) . "'>" . $likes . " likes</span>";
                echo "<span class='like-btn' data-post-id='" . htmlspecialchars($post['id']) . "'>👍</span>";

                echo "<span id='dislikes-" . htmlspecialchars($post['id']) . "'>" . $dislikes . " dislikes</span>";
                echo "<span class='dislike-btn' data-post-id='" . htmlspecialchars($post['id']) . "'>👎</span>";

                echo "</div>";

                echo "<p><small>By " . htmlspecialchars($userName) . " on " . htmlspecialchars($post['date']) . "</small></p>";
                echo "</div>";
            }
        } else {
            echo "<p>You have not posted anything yet.</p>";
        }
        ?>
    </div>

    <script>
        $(document).on('click', '.like-btn', function() {
            var postId = $(this).data('post-id');

            $.post("like_post.php", { post_id: postId }, function(response) {
                if (response.success) {
                    $("#likes-" + postId).text(response.likes + " likes");
                } else {
                    alert(response.error || "Failed to like the post.");
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ": " + errorThrown);
                alert("Error occurred while liking the post.");
            });
        });

        $(document).on('click', '.dislike-btn', function() {
            var postId = $(this).data('post-id');

            $.post("dislike_post.php", { post_id: postId }, function(response) {
                if (response.success) {
                    $("#dislikes-" + postId).text(response.dislikes + " dislikes");
                } else {
                    alert(response.error || "Failed to dislike the post.");
                }
            }, "json").fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ": " + errorThrown);
                alert("Error occurred while disliking the post.");
            });
        });
    </script>

</body>
</html>
