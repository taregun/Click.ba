<?php
session_start();
include('includes/functions.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch all posts
$posts = getAllPosts();

// Filter posts to show only those written by the logged-in user
$user_posts = array_filter($posts, function($post) use ($user_id) {
    return $post['author'] === $user_id;
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome - click.ba</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Center the content */
        .center-content {
            text-align: center;
            margin: 0 auto;
        }

        /* Style the profile picture */
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        /* Post style */
        .post {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .post img {
            max-width: 100%;
            height: auto;
        }

        .post h3 {
            margin: 0;
        }

        .post p {
            font-size: 16px;
        }

        .likes {
            margin-top: 10px;
            font-size: 18px;
        }

        .like-btn {
            cursor: pointer;
            font-size: 24px;
            color: #007BFF;
        }
    </style>
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

    <div class="center-content">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>

        <?php
        // Display profile picture if available
        if (isset($_SESSION['profile_pic']) && $_SESSION['profile_pic']) {
            echo '<img src="images/' . htmlspecialchars($_SESSION['profile_pic']) . '" class="profile-pic" alt="Profile Picture">';
        }

        // Display user's posts
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

                // Show likes and like button
                $likes = isset($post['likes']) ? $post['likes'] : 0;  // Default to 0 if likes are not set
                echo "<div class='likes'>";
                echo "<span id='likes-" . htmlspecialchars($post['id']) . "'>" . $likes . " likes</span>";

                // Like button (This should trigger the liking functionality)
                echo "<span class='like-btn' data-post-id='" . htmlspecialchars($post['id']) . "'>👍</span>";
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
        // Add the like button functionality (AJAX call to like the post)
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
    </script>

</body>
</html>
