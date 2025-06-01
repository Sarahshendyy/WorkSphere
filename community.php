<?php

// 1. Establish Database Connection
// IMPORTANT: Replace 'connect.php' with your actual database connection file.
// This file should define the $connect variable.
include 'nav.php'; // Or your actual DB connection script (e.g., db_connection.php)

// 2. Get User ID from session
$user_id=$_SESSION['user_id'];// Assuming user_id is stored in session after login

// 3. Define Helper Functions (these need $connect and sometimes $user_id)

// Function to get like count
function getLikeCount($connect, $post_id) {
    if (!$connect || !$post_id) return 0;
    $post_id_safe = mysqli_real_escape_string($connect, $post_id);
    $like_query = "SELECT COUNT(*) as like_count FROM `like` WHERE `post_id` = '$post_id_safe'";
    $result_like = mysqli_query($connect, $like_query);
    if ($result_like) {
        $like_data = mysqli_fetch_assoc($result_like);
        return $like_data['like_count'] ?? 0;
    }
    return 0;
}

// Function to get comment count
function getCommentCount($connect, $post_id) {
    if (!$connect || !$post_id) return 0;
    $post_id_safe = mysqli_real_escape_string($connect, $post_id);
    $comment_query = "SELECT COUNT(*) as comment_count FROM `comment` WHERE `post_id` = '$post_id_safe'";
    $result_comment = mysqli_query($connect, $comment_query);
    if ($result_comment) {
        $comment_data = mysqli_fetch_assoc($result_comment);
        return $comment_data['comment_count'] ?? 0;
    }
    return 0;
}

// Function to get comments
function getComments($connect, $post_id) {
    if (!$connect || !$post_id) return [];
    $post_id_safe = mysqli_real_escape_string($connect, $post_id);
    $comment_query = "SELECT `comment`.*, `users`.`name`, `users`.`image` FROM `comment`
                    JOIN `users` ON `comment`.`user_id` = `users`.`user_id`
                    WHERE `post_id` = '$post_id_safe' ORDER BY `comment_id` DESC";
    $result_comment = mysqli_query($connect, $comment_query);
    if ($result_comment) {
        return mysqli_fetch_all($result_comment, MYSQLI_ASSOC);
    }
    return [];
}


// 4. AJAX Action Handling
// This block executes if 'action' is found in POST data, sends JSON, and exits.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'error' => 'Invalid action or missing parameters.']; // Default

    // Check if user is logged in for actions that require it
    if (!$user_id && in_array($_POST['action'], ['like', 'comment', 'delete', 'delete_post'])) {
        echo json_encode(['success' => false, 'error' => 'User not logged in. Please log in.']);
        exit;
    }
    if (!$connect) { // Check for DB connection
        echo json_encode(['success' => false, 'error' => 'Database connection error.']);
        exit;
    }

    $action = $_POST['action'];

    // **LIKE/UNLIKE HANDLING**
    if ($action == 'like' && isset($_POST['post_id'])) {
        $post_id = mysqli_real_escape_string($connect, $_POST['post_id']);
        $check_like = "SELECT * FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
        $result_check = mysqli_query($connect, $check_like);

        if ($result_check && mysqli_num_rows($result_check) > 0) {
            $delete_like = "DELETE FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
            mysqli_query($connect, $delete_like);
            $liked = false;
        } else {
            $insert_like = "INSERT INTO `like` (user_id, post_id) VALUES ('$user_id', '$post_id')";
            mysqli_query($connect, $insert_like);
            $liked = true;
        }
        $new_like_count = getLikeCount($connect, $post_id);
        echo json_encode(['success' => true, 'like_count' => $new_like_count, 'liked' => $liked, 'post_id' => $post_id]);
        exit;
    }
    // **INSERT COMMENT HANDLING**
    elseif ($action == 'comment' && isset($_POST['post_id'], $_POST['text'])) {
        $post_id = mysqli_real_escape_string($connect, $_POST['post_id']);
        $comment_text = trim($_POST['text']); // Trim spaces
        $escaped_comment_text = mysqli_real_escape_string($connect, $comment_text);


        if (!empty($comment_text)) {
            // Specify column names for clarity and safety
            $insert_comment = "INSERT INTO `comment` (user_id, post_id, text) VALUES ('$user_id', '$post_id', '$escaped_comment_text')";
            if (mysqli_query($connect, $insert_comment)) {
                $new_comment_id = mysqli_insert_id($connect); // Get ID of the new comment

                $new_comment_query = "SELECT `comment`.*, `users`.`name`, `users`.`image` FROM `comment`
                                   JOIN `users` ON `comment`.`user_id` = `users`.`user_id`
                                   WHERE `comment`.`comment_id` = '$new_comment_id'";
                $result_new_comment = mysqli_query($connect, $new_comment_query);
                $new_comment_data = mysqli_fetch_assoc($result_new_comment);

                if ($new_comment_data) {
                    echo json_encode(['success' => true, 'comment' => $new_comment_data, 'post_id' => $post_id]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to retrieve newly added comment.']);
                }
            } else {
                 echo json_encode(['success' => false, 'error' => 'Failed to add comment to database: ' . mysqli_error($connect)]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Comment text cannot be empty.']);
        }
        exit;
    }
    // **DELETE COMMENT HANDLING**
    elseif ($action == 'delete' && isset($_POST['comment_id'])) {
        $comment_id = mysqli_real_escape_string($connect, $_POST['comment_id']);
        // Ensure user owns the comment (or is an admin - admin logic not shown here)
        $delete_comment = "DELETE FROM `comment` WHERE `comment_id` = '$comment_id' AND `user_id` = '$user_id'";
        if (mysqli_query($connect, $delete_comment)) {
            if (mysqli_affected_rows($connect) > 0) {
                echo json_encode(['success' => true, 'comment_id' => $comment_id]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Comment not found or you do not have permission to delete it.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Error deleting comment: ' . mysqli_error($connect)]);
        }
        exit;
    }
    // **DELETE POST HANDLING**
    elseif ($action == 'delete_post' && isset($_POST['post_id'])) {
        $post_id = mysqli_real_escape_string($connect, $_POST['post_id']);
        
        $check_ownership_query = "SELECT `user_id`, `files`, `images` FROM `community` WHERE `post_id` = '$post_id'";
        $result_ownership = mysqli_query($connect, $check_ownership_query);

        if ($result_ownership && mysqli_num_rows($result_ownership) > 0) {
            $post_data = mysqli_fetch_assoc($result_ownership);
            if ($post_data['user_id'] == $user_id) { // Check if current user owns the post
                mysqli_begin_transaction($connect);
                try {
                    // Delete associated likes
                    $delete_likes = "DELETE FROM `like` WHERE `post_id` = '$post_id'";
                    mysqli_query($connect, $delete_likes);

                    // Delete associated comments
                    $delete_comments = "DELETE FROM `comment` WHERE `post_id` = '$post_id'";
                    mysqli_query($connect, $delete_comments);

                    // **IMPORTANT**: Delete any files associated with the post from the server.
                    // Example (you need to adapt this to your file storage structure):
                    // if (!empty($post_data['files']) && file_exists($post_data['files'])) {
                    //     unlink($post_data['files']);
                    // }
                    // if (!empty($post_data['images'])) {
                    //     $image_paths_array = explode(',', $post_data['images']);
                    //     foreach ($image_paths_array as $imgPath) {
                    //         $trimmed_path = trim($imgPath);
                    //         if (!empty($trimmed_path) && file_exists($trimmed_path)) {
                    //             unlink($trimmed_path);
                    //         }
                    //     }
                    // }

                    // Delete the post itself
                    $delete_post = "DELETE FROM `community` WHERE `post_id` = '$post_id'";
                    mysqli_query($connect, $delete_post);

                    mysqli_commit($connect);
                    echo json_encode(['success' => true, 'post_id' => $post_id]);
                } catch (mysqli_sql_exception $exception) {
                    mysqli_rollback($connect);
                    echo json_encode(['success' => false, 'error' => 'Database error during post deletion. ' . $exception->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this post.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Post not found or database error on lookup.']);
        }
        exit;
    }

    // If action was set but not matched by specific handlers
    echo json_encode($response); // Output the default error response
    exit;
}


//5. Select Community Posts
// Function to select_community (used for page rendering)
function select_community($connect, $filter, $current_user_id_for_query) {
    if (!$connect) return [];
    
    $safe_user_id = $current_user_id_for_query ? mysqli_real_escape_string($connect, $current_user_id_for_query) : null;

    if ($filter == 'my_posts') {
        if (!$safe_user_id) return []; // Can't show "my_posts" if no user is logged in
        $select_posts_sql = "SELECT `community`.*, `users`.`name`, `users`.`image` FROM `community`
                JOIN `users` ON `community`.`user_id` = `users`.`user_id`
                WHERE `community`.`user_id` = '$safe_user_id'
                ORDER BY `community`.`post_id` DESC";
    } else { // 'all_posts' or default
        $select_posts_sql = "SELECT `community`.*, `users`.`name`, `users`.`image` FROM `community`
                JOIN `users` ON `community`.`user_id` = `users`.`user_id`
                ORDER BY `community`.`post_id` DESC";
    }
    $result_post = mysqli_query($connect, $select_posts_sql);
    if ($result_post) {
        return mysqli_fetch_all($result_post, MYSQLI_ASSOC);
    }
    return []; // Return empty array on error or no results
}

// Determine the filter for page rendering (only if it's not an AJAX request)
$filter = 'all_posts'; // Default filter
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter']) && !isset($_POST['action'])) {
    // This condition ensures 'filter' is only processed for non-AJAX form submissions
    $filter = $_POST['filter'];
} elseif (isset($_GET['filter'])) { // Allow filtering via GET for direct links
    $filter = $_GET['filter'];
}


$community_posts = [];
if ($connect) { // Ensure $connect is valid before calling select_community
    $community_posts = select_community($connect, $filter, $user_id); // $user_id is the logged-in user
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lancelot&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./css/community.css">
    <title>Community</title>
</head>

<body>
    <div class="container">
        <!-- header -->
        <header class="header">
            <h1>Our Community</h1>
        </header>
        <!-- buttons section -->
        <section class="buttons">
            <!--
                It's generally better to use GET for filter links for shareability/bookmarking.
                If using POST for filtering, the form should target the current page.
            -->
            <form action="community.php" method="POST">
                <a href="add_post.php" class="button-link">
                    <button type="button">Add <i class="fa-solid fa-plus"></i></button>
                </a>
                <button type="submit" name="filter" value="all_posts" <?php echo ($filter == 'all_posts' ? 'class="active-filter"' : ''); ?>>All Posts</button>
                <button type="submit" name="filter" value="my_posts" <?php echo ($filter == 'my_posts' ? 'class="active-filter"' : ''); ?>>My Posts</button>
            </form>
        </section>
        <!-- main content -->
        <main class="content">
            <?php if (empty($community_posts) && $connect): ?>
                <p class="no-posts">No posts to display for this filter.</p>
            <?php elseif (!$connect): ?>
                <p class="no-posts">Error: Could not connect to the database to load posts.</p>
            <?php endif; ?>

            <?php foreach ($community_posts as $data) {
              // Check if the current logged-in user has liked this specific post
              $post_id_for_like_check = $data['post_id'];
              $is_liked_by_current_user = false;
              if ($user_id && $connect) { // Only check if user is logged in and DB is connected
                  $check_like_query = "SELECT `like_id` FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id_for_like_check' LIMIT 1";
                  $result_check_like = mysqli_query($connect, $check_like_query);
                  if ($result_check_like && mysqli_num_rows($result_check_like) > 0) {
                      $is_liked_by_current_user = true;
                  }
              }
              $heartColor = $is_liked_by_current_user ? 'red' : 'black'; // Or 'grey' or '' for default
            ?>
            <!-- post -->
            <article class="post-card" data-post-id="<?php echo htmlspecialchars($data['post_id']); ?>">
                <!-- delete post button -->
                <?php if ($user_id && $data['user_id'] == $user_id) { // Show delete button if logged-in user is the post owner ?>
                <button class="delete-post-btn" data-post-id="<?php echo htmlspecialchars($data['post_id']); ?>">
                    <i class="fa-solid fa-trash"></i> Delete Post
                </button>
                <?php } ?>
                <!-- profile -->
                <div class="profile">
                    <div class="profile-image">
                        <a href="profile.php?user_id=<?php echo htmlspecialchars($data['user_id']); ?>">
                            <img src="./img/<?php echo htmlspecialchars($data['image'] ?: 'default-profile.png'); ?>" alt="user profile">
                        </a>
                    </div>
                    <div class="profile-name">
                        <a href="profile.php?user_id=<?php echo htmlspecialchars($data['user_id']); ?>">
                            <h2><?php echo htmlspecialchars($data['name']); ?></h2>
                        </a>
                    </div>
                </div>
                <!-- description -->
                <div class="post-content">
                    <p><?php echo nl2br(htmlspecialchars($data['description'])); ?></p>
                    <!-- file download -->
                    <?php
                    if (!empty($data['files'])) {
                        $file_name = basename($data['files']); // Get only the filename
                        echo '<p class="post-file-download">File: ' . htmlspecialchars($file_name) . " ".'<a href="' . htmlspecialchars($data['files']) . '" download>
                        <i class="fa-solid fa-file-export" style="color:#080a74;"></i> Download</a>'.'</p>';
                    }
                    // Images associated with the post
                    if (!empty($data['images'])) {
                        $image_paths_array = explode(',', $data['images']);
                        echo '<div class="post-images-container">';
                        foreach ($image_paths_array as $image_path) {
                            $trimmed_path = trim($image_path);
                            if (!empty($trimmed_path)) {
                                echo '<img src="' . htmlspecialchars($trimmed_path) . '" alt="post image" class="post-image">';
                            }
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                <!-- like/comment -->
                <div class="post-react">
                    <button class="like-button">
                        <i class="fa-heart <?php echo $is_liked_by_current_user ? 'fa-solid' : 'fa-regular'; ?>" style="color: <?php echo $heartColor; ?>;"></i>
                        <span class="like-count"><?php echo getLikeCount($connect, $data['post_id']); ?></span>
                    </button>

                    <button class="comment-btn">
                        <i class="fa-regular fa-comment"></i>
                        <span class="comment-count"><?php echo getCommentCount($connect, $data['post_id']); ?></span>
                    </button>
                </div>
                <!-- Comment Form (Hidden Initially by JS) -->
                <div class="comment-form" style="display: none;">
                    <div class="comment-input-container">
                        <textarea class="text" name="text" placeholder="Write a comment.." ></textarea> <!-- `required` is good but handled by JS/server -->
                        <button type="button" class="send-icon comment-submit" data-post-id="<?php echo htmlspecialchars($data['post_id']); ?>">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <!-- list comment (Hidden Initially by JS or CSS) -->
                <div class="comments-list" style="display: none;">
                    <?php $comments_for_post = getComments($connect, $data['post_id']); ?>
                    <?php if (empty($comments_for_post)): ?>
                        <p class="no-comments">No comments yet. Be the first to comment!</p>
                    <?php else: ?>
                        <?php foreach ($comments_for_post as $comment): ?>
                        <div class="comment" data-comment-id="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                            <a href="profile.php?user_id=<?php echo htmlspecialchars($comment['user_id']); ?>" class="comment-user-avatar">
                                <img src="./img/<?php echo htmlspecialchars($comment['image'] ?: 'default-profile.png'); ?>" alt="user image">
                            </a>
                            <div class="comment-content">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($comment['user_id']); ?>">
                                    <p><strong><?php echo htmlspecialchars($comment['name']); ?>:</strong></p>
                                </a>
                                <p><?php echo nl2br(htmlspecialchars($comment['text'])); ?></p>
                            </div>
                            <!-- delete comment button -->
                            <?php if ($user_id && $comment['user_id'] == $user_id) { // Show delete if logged-in user is comment owner ?>
                            <button class="delete-icon delete-comment-btn" data-comment-id="<?php echo htmlspecialchars($comment['comment_id']); ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <?php } ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </article>
            <?php } // End foreach ($community_posts as $data) ?>
        </main>
    </div>
    <script src="./js/community.js"></script>
</body>
</html>