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
    $posts = array_filter($posts, function($post) use ($searchQuery) {
        return stripos($post['title'], $searchQuery) !== false; // Case-insensitive search
    });
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Click.ba</title>
    <link rel="stylesheet" href="css/style.css">
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
    
    <?php
    if ($posts):
        // Reverse posts if sorting order is descending (oldest to newest)
        if ($sortOrder === 'desc') {
            $posts = array_reverse($posts);
        }

        // Generate the new sorting URL
        $newSortOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
        $sortButtonText = $sortOrder === 'asc' ? 'Sort: Oldest to Newest ' : 'Sort: Newest to Oldest';


        // Search form (adjusted to make button and input the same width)
        echo "<form action='' method='get' style='text-align:center; margin-top: 20px; width: 50%; max-width: 500px;'>
            <button type='submit' name='sort' value='$newSortOrder' style='width: 100%; padding: 10px; margin-bottom: 10px;'>$sortButtonText</button>
            <input type='text' name='search' placeholder='Search by title' value='" . htmlspecialchars($searchQuery) . "' style='width: 100%; padding: 10px; margin-bottom: 10px;'>
            <button type='submit' style='width: 100%; padding: 10px;'>Search</button>
        </form>";

      

        foreach ($posts as $post) {
            $user = getUserById($post['author']);
            if (!$user) {
                echo "<p>Author not found for post: " . htmlspecialchars($post['title']) . "</p>";
                continue; // Skip posts with no user found
            }

            $profilePic = isset($user['profile_pic']) ? $user['profile_pic'] : 'blankProfile.png';
            $userName = isset($user['name']) ? $user['name'] : 'Anonymous';

            echo "<div class='post'>";
            echo "<img src='images/" . htmlspecialchars($profilePic) . "' alt='Profile Picture' class='profile-pic'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";

            if (!empty($post['image'])) {
                echo "<img src='images/" . htmlspecialchars($post['image']) . "' alt='Post Image' class='post-image'>";
            }

            echo "<p><small>By " . htmlspecialchars($userName) . " on " . htmlspecialchars($post['date']) . "</small></p>";
            echo "</div>";
        }
    else:
        echo "<p>No posts available.</p>";
    endif;
    ?>
</body>
</html>
