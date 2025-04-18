<?php
session_start();
include('includes/functions.php');

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    die("User not specified. Please <a href='login.php'>login</a> first.");
}

// Define the path for your JSON files
$postsFile = 'data/posts.json';
$usersFile = 'data/users.json';

// Get the user ID from the URL or session
$userId = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id']; // Keep user_id as string
// Default to logged-in user if no user_id is provided

// Get posts by user
$posts = getPostsByUser($userId);

// Get user details for the channel page
$user = getUserById($userId);
if (!$user) {
    die("User not found.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Channel - <?php echo htmlspecialchars($user['name']); ?></title>
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
    <h1>Welcome to the Channel of <?php echo htmlspecialchars($user['name']); ?></h1>

    <div class="center-content">

        <!-- Display the profile picture of the user whose posts we are showing -->
        <?php if (isset($user['profile_pic']) && $user['profile_pic']): ?>
            <img src="images/<?php echo htmlspecialchars($user['profile_pic']); ?>" class="profile-pic" alt="Profile Picture">
        <?php endif; ?>

        <?php if ($posts): ?>
            <h2>Posts</h2>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <li class="post">
                        <!-- Display the profile image of the post's author -->
                        <img src="images/<?php echo isset($post['icon']) ? htmlspecialchars($post['icon']) : 'blankProfile.png'; ?>" class="profile-pic" alt="Post Profile Picture">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

                        <!-- Display post image if available -->
                        <?php if (!empty($post['image'])): ?>
                            <img src="images/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>

                        <div style='display: flex; justify-content: space-between; align-items: center;'>
                            <p><b>Likes:</b> <span id='likes-<?= htmlspecialchars($post['id']) ?>'><?= $post['likes'] ?></span></p>
                                <span class='like-btn' data-post-id='<?= htmlspecialchars($post['id']) ?>' 
                            style='cursor: pointer; font-size: 32px; margin-left: 10px; vertical-align: middle;'>👍</span>
                                <span class='dislike-btn' data-post-id='<?= htmlspecialchars($post['id']) ?>' 
                            style='cursor: pointer; font-size: 32px; margin-left: 10px; vertical-align: middle;'>👎</span>
                        </div>

                        <p><small>By <?php echo htmlspecialchars($user['name']); ?> on <?php echo htmlspecialchars($post['date']); ?></small></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No posts available from this user.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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