<?php
include 'connection.php';
$user_id = $_SESSION['user_id'];

// Function to select_community 
function select_community($connect, $filter, $user_id) {
    if ($filter == 'my_posts') {
        $select_posts = "SELECT `community`.*, `users`.`name`, `users`.`image` FROM `community`
                JOIN `users` ON `community`.`user_id` = `users`.`user_id`
                WHERE `community`.`user_id` = '$user_id'
                ORDER BY `community`.`post_id` DESC";
    } else {
        $select_posts = "SELECT `community`.*, `users`.`name`, `users`.`image` FROM `community`
                JOIN `users` ON `community`.`user_id` = `users`.`user_id`
                ORDER BY `community`.`post_id` DESC";
    }
    $result_post = mysqli_query($connect, $select_posts);
    return mysqli_fetch_all($result_post, MYSQLI_ASSOC);
}

// Determine the filter
$filter = isset($_POST['filter']) ? $_POST['filter'] : 'all_posts';

$community = select_community($connect, $filter, $user_id);

// Function to get like count 
function getLikeCount($connect, $post_id) {
    $like_query = "SELECT COUNT(*) as like_count FROM `like` WHERE `post_id` = $post_id";
    $result_like = mysqli_query($connect, $like_query);
    $like_data = mysqli_fetch_assoc($result_like);
    return $like_data['like_count'];
}

// hna 3l4an 7war el refresh f est5dmna api w ajax
// **LIKE/UNLIKE HANDLING (API ENDPOINT)**
if (isset($_POST['action']) && $_POST['action'] == 'like') {
    $post_id = $_POST['post_id'];

    $check_like = "SELECT * FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
    $result_check = mysqli_query($connect, $check_like);

    if (mysqli_num_rows($result_check) > 0) {
        $delete_like = "DELETE FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
        mysqli_query($connect, $delete_like);
        $liked = false; 
    } else {
        $insert_like = "INSERT INTO `like` (user_id, post_id) VALUES ('$user_id', '$post_id')";
        mysqli_query($connect, $insert_like);
        $liked = true; 
    }

    $new_like_count = getLikeCount($connect, $post_id); 

    // Return a JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'like_count' => $new_like_count, 'liked' => $liked, 'post_id' => $post_id]);
    exit; 
}

// Function to get comment count 
function getCommentCount($connect, $post_id) {
    $comment_query = "SELECT COUNT(*) as comment_count FROM `comment` WHERE `post_id` = $post_id";
    $result_comment = mysqli_query($connect, $comment_query);
    $comment_data = mysqli_fetch_assoc($result_comment);
    return $comment_data['comment_count'];
}

// Function to get comments 
function getComments($connect, $post_id) {
    $comment_query = "SELECT `comment`.*, `users`.`name`, `users`.`image` FROM `comment`
                    JOIN `users` ON `comment`.`user_id` = `users`.`user_id`
                    WHERE `post_id` = $post_id ORDER BY `comment_id` DESC";
    $result_comment = mysqli_query($connect, $comment_query);
    return mysqli_fetch_all($result_comment, MYSQLI_ASSOC);
}
// hna 3l4an 7war el refresh f est5dmna api w ajax
// **INSERT COMMENT HANDLING (API ENDPOINT)**
if (isset($_POST['action']) && $_POST['action'] == 'comment') {
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($connect, $_POST['text']);

    if (!empty($comment_text)) {
        $insert_comment = "INSERT INTO `comment` VALUES (NULL,'$user_id', '$post_id', '$comment_text')";
        mysqli_query($connect, $insert_comment);

        // Fetch the newly added comment (along with user data)
        $new_comment_query = "SELECT `comment`.*, `users`.`name`, `users`.`image` FROM `comment`
                               JOIN `users` ON `comment`.`user_id` = `users`.`user_id`
                               WHERE `comment`.`user_id` = '$user_id' AND `comment`.`post_id` = '$post_id' AND `comment`.`text` = '$comment_text'
                               ORDER BY `comment_id` DESC LIMIT 1"; // Get the last inserted
        $result_new_comment = mysqli_query($connect, $new_comment_query);
        $new_comment = mysqli_fetch_assoc($result_new_comment);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'comment' => $new_comment, 'post_id' => $post_id]); //Return new comment
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Comment text cannot be empty.']);
        exit;
    }
}
// hna 3l4an 7war el refresh f est5dmna api w ajax
// **DELETE COMMENT HANDLING (API ENDPOINT)**
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $comment_id = $_POST['comment_id'];

    $delete_comment = "DELETE FROM `comment` WHERE `comment_id` = '$comment_id' AND `user_id` = '$user_id'";
    mysqli_query($connect, $delete_comment);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'comment_id' => $comment_id]);
    exit;
}
// hna 3l4an 7war el refresh f est5dmna api w ajax
// **DELETE POST HANDLING (API ENDPOINT)**
if (isset($_POST['action']) && $_POST['action'] == 'delete_post') {
    $post_id = $_POST['post_id'];
    
    $check_ownership_query = "SELECT `user_id` FROM `community` WHERE `post_id` = '$post_id'";
    $result_ownership = mysqli_query($connect, $check_ownership_query);
    // hna byms7 kol 7aga lma yms7 el post 3l4an el likes w el cooments ttms7 kman f el db 
    if ($result_ownership && mysqli_num_rows($result_ownership) > 0) {
        $post_data = mysqli_fetch_assoc($result_ownership);
        if ($post_data['user_id'] == $user_id) {
            $delete_likes = "DELETE FROM `like` WHERE `post_id` = '$post_id'";
            mysqli_query($connect, $delete_likes);

            $delete_comments = "DELETE FROM `comment` WHERE `post_id` = '$post_id'";
            mysqli_query($connect, $delete_comments);

            // Also, delete any files associated with the post from the server.

            $delete_post = "DELETE FROM `community` WHERE `post_id` = '$post_id'";
            mysqli_query($connect, $delete_post);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'post_id' => $post_id]);
            exit;
        } else {
            // User does not own the post
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'You do not have permission to delete this post.']);
            exit;
        }
    } else {
        // Post not found or database error
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Post not found or database error.']);
        exit;
    }
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
            <form action="" method="POST">
                <a href="add_post.php" class="button-link">
                    <button type="button">Add <i class="fa-solid fa-plus"></i></button>
                </a>
                <button type="submit" name="filter" value="all_posts">All Posts</button>
                <button type="submit" name="filter" value="my_posts">My Posts</button>
            </form>
        </section>
        <!-- main content -->
        <main class="content">
            <?php foreach ($community as $data) {
              // Check if the user has liked the post for each post
            $post_id = $data['post_id']; 
            $check_like_query = "SELECT * FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
            $new_like_count = mysqli_query($connect, $check_like_query);

            $heartColor = (mysqli_num_rows($new_like_count) > 0) ? 'red' : '';
            ?>
            <!-- post -->
            <article class="post-card" data-post-id="<?php echo $data['post_id']; ?>">
                <!-- delete post -->
                <?php if ($data['user_id'] == $user_id) { ?>
                <button class="delete-post-btn" data-post-id="<?php echo $data['post_id']; ?>">
                    <i class="fa-solid fa-trash"></i> Delete Post
                </button>
                <?php } ?>
                <!-- profile -->
                <div class="profile">
                    <div class="profile-image">
                        <a href="profile.php?user_id=<?php echo $data['user_id']; ?>">
                            <img src="./img/<?php echo $data['image']; ?>" alt="user profile">
                        </a>
                    </div>
                    <div class="profile-name">
                        <a href="profile.php?user_id=<?php echo $data['user_id']; ?>">
                            <h2><?php echo $data['name']; ?></h2>
                        </a>
                    </div>
                </div>
                <!-- description -->
                <div class="post-content">
                    <p><?php echo htmlspecialchars($data['description']); ?></p>
                    <!-- file download -->
                    <?php
                    if (!empty($data['files'])) {
                        $file_name = basename($data['files']);
                        echo '<p>' . htmlspecialchars($file_name) . " ".'<a href="' . htmlspecialchars($data['files']) . '" download>
                        <i class="fa-solid fa-file-export" style="color:#080a74;"></i></a>'.'</p>';
                    }
                    //imge
                    $image_paths = explode(',', $data['images']);
                    foreach ($image_paths as $image_path) {
                        if (!empty($image_path)) {
                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="post image">';
                        }
                    }
                    ?>
                </div>
                <!-- like/comment -->
                <div class="post-react">


                    <button class="like-button">
                        <i class="fa-solid fa-heart" style="color: <?php echo $heartColor; ?>;"></i>
                        <span class="like-count"><?php echo getLikeCount($connect, $data['post_id']); ?></span>
                    </button>


                    <button class="comment-btn">
                        <i class="fa-regular fa-comment"></i><span
                            class="comment-count"><?php echo getCommentCount($connect, $data['post_id']); ?></span>
                    </button>
                </div>
                <!-- Comment Section (Hidden Initially) -->
                <div class="comment-form">
                    <div class="comment-input-container">
                        <textarea class="text" name="text" placeholder="Write a comment.." required></textarea>
                        <button class="send-icon comment-submit" data-post-id="<?php echo $data['post_id']; ?>">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <!-- list comment -->
                <div class="comments-list">
                    <?php $comments = getComments($connect, $data['post_id']); ?>
                    <?php if (!empty($comments)) { ?>
                    <?php foreach ($comments as $comment) { ?>
                    <div class="comment" data-comment-id="<?php echo $comment['comment_id']; ?>">
                        <a href="profile.php?user_id=<?php echo $comment['user_id']; ?>">
                            <img src="./img/<?php echo htmlspecialchars($comment['image']); ?>" alt="user image">
                        </a>
                        <div class="comment-content">
                            <a href="profile.php?user_id=<?php echo $comment['user_id']; ?>">
                                <p><strong><?php echo htmlspecialchars($comment['name']); ?>:</strong></p>
                            </a>
                            <p><?php echo htmlspecialchars($comment['text']); ?></p>
                        </div>

                        <!-- delete comment -->
                        <?php if ($comment['user_id'] == $user_id) { ?>
                        <button class="delete-icon delete-comment-btn"
                            data-comment-id="<?php echo $comment['comment_id']; ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <?php } ?>
                    </div>
                    <?php } ?>
                    <?php } else { ?>
                    <p class="no-comments">No comments yet</p>
                    <?php } ?>
                </div>

            </article>
            <?php } ?>
        </main>
    </div>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {

    // Hide all comments lists and comment forms on page load
    document.querySelectorAll(".comments-list, .comment-form").forEach(element => {
        element.style.display = "none";
    });

    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postCard = this.closest(".post-card");
            const commentsList = postCard.querySelector(".comments-list");
            const commentForm = postCard.querySelector(".comment-form");

            // Toggle visibility
            if (commentsList.style.display === "none") {
                commentsList.style.display = "block";
                commentForm.style.display = "block"; // Ensure text area appears
            } else {
                commentsList.style.display = "none";
                commentForm.style.display = "none"; // Hide both when clicked again
            }
        });
    });

    // LIKE Button Click Handler
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission / page reload
            const postId = this.closest('.post-card').dataset.postId; // get post id
            const likeCountSpan = this.querySelector('.like-count');
            const heartIcon = this.querySelector('i'); // get heart icon
            const buttonItself = this; // Store the button element

            // Send AJAX request
            fetch('', { // URL: empty string because we're submitting to the same page
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=like&post_id=' + encodeURIComponent(
                        postId) // Data sent to PHP
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeCountSpan.textContent = data
                            .like_count; // Update the like count

                        // Toggle the heart icon based on like status
                        if (data.liked) {
                            heartIcon.style.color = 'red'; // change icon
                        } else {
                            heartIcon.style.color = 'black'; // change icon
                        }
                    } else {
                        alert('Error liking post.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        });
    });


    // COMMENT SUBMIT Button Click Handler
    document.querySelectorAll('.comment-submit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission / page reload
            const postId = this.dataset.postId;
            const postCard = this.closest('.post-card'); // Find the parent post-card
            const commentText = postCard.querySelector('.text').value; // Get the comment text
            const commentsList = postCard.querySelector(
                '.comments-list'); // Get the comment list

            // Send AJAX request
            fetch('', { // URL: empty string because we're submitting to the same page
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=comment&post_id=' + encodeURIComponent(postId) +
                        '&text=' + encodeURIComponent(commentText) // Data sent to PHP
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Create new comment element
                        const newCommentDiv = document.createElement('div');
                        newCommentDiv.classList.add('comment');
                        newCommentDiv.dataset.commentId = data.comment
                            .comment_id; // Add comment ID

                        newCommentDiv.innerHTML = `
                        <a href="profile.php?user_id=${data.comment.user_id}">
                            <img src="./img/${data.comment.image}" alt="user image">
                        </a>
                        <div class="comment-content">
                            <a href="profile.php?user_id=${data.comment.user_id}">
                                <p><strong>${data.comment.name}:</strong></p>
                            </a>
                            <p>${data.comment.text}</p>
                        </div>
                        <button class="delete-icon delete-comment-btn" data-comment-id="${data.comment.comment_id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;

                        commentsList.prepend(newCommentDiv); // Add to comment list

                        // Clear the textarea
                        postCard.querySelector('.text').value = '';

                        // Add event listener for the newly added delete button
                        newCommentDiv.querySelector('.delete-comment-btn').addEventListener(
                            'click',
                            function() {
                                deleteComment(this.dataset.commentId, newCommentDiv);
                            });


                    } else {
                        alert('Error adding comment: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        });
    });


    // DELETE COMMENT Function (Used by both initial comments and new comments)
    function deleteComment(commentId, commentElement) {
        fetch('', { // URL: empty string because we're submitting to the same page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=delete&comment_id=' + encodeURIComponent(commentId) // Data sent to PHP
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentElement.remove(); // Remove comment from the UI
                } else {
                    alert('Error deleting comment.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
    }


    // Attach event listeners to existing delete buttons on page load
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentElement = this.closest('.comment'); // Find the comment div to remove
            deleteComment(commentId, commentElement); // Call the delete function
        });
    });
        // DELETE POST Button Click Handler
    document.querySelectorAll('.delete-post-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const postId = this.dataset.postId;
            const postCard = this.closest('.post-card');

            if (confirm('Are you sure you want to delete this post?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=delete_post&post_id=' + encodeURIComponent(postId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the post from the UI
                        postCard.remove();
                    } else {
                        alert('Error deleting post: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            }
        });
    });
});
</script>

</html>
