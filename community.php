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
// Get the community posts based on the filter
$community = select_community($connect, $filter, $user_id);

// Function to get like count
function getLikeCount($connect, $post_id) {
    $like_query = "SELECT COUNT(*) as like_count FROM `like` WHERE `post_id` = $post_id";
    $result_like = mysqli_query($connect, $like_query);
    $like_data = mysqli_fetch_assoc($result_like);
    return $like_data['like_count'];
}

// Handle like button click
if (isset($_POST['like'])) {
    $post_id = $_POST['post_id'];
    // Check if the user already liked the post
    $check_like = "SELECT * FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
    $result_check = mysqli_query($connect, $check_like);

    if (mysqli_num_rows($result_check) > 0) { 
        $delete_like = "DELETE FROM `like` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
        mysqli_query($connect, $delete_like);
    } else { 
        $insert_like = "INSERT INTO `like` (user_id, post_id) VALUES ('$user_id', '$post_id')";
        mysqli_query($connect, $insert_like);
    }
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
// insert comment
if (isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($connect, $_POST['text']);
    if (!empty($comment_text)) {
        $insert_comment = "INSERT INTO `comment` VALUES (NULL,'$user_id', '$post_id', '$comment_text')";
        mysqli_query($connect, $insert_comment);
    }
}
//delete comments
if (isset($_POST['delete'])) {
    $comment_id = $_POST['comment_id'];
    $delete_comment = "DELETE FROM `comment` WHERE `comment_id` = '$comment_id' AND `user_id` = '$user_id'";
    mysqli_query($connect, $delete_comment);
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
            <?php foreach ($community as $data) { ?>
            <!-- post -->
            <article class="post-card">
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
                    <p><?php echo $data['description']; ?></p>
                    <!-- file download -->
                    <?php
                    if (!empty($data['files'])) {
                        $file_name = basename($data['files']);
                        echo '<p>' . $file_name . " ".'<a href="' . $data['files'] . '" download>
                        <i class="fa-solid fa-file-export" style="color:#080a74;"></i></a>'.'</p>';
                    }
                    //imge
                    $image_paths = explode(',', $data['images']);
                    foreach ($image_paths as $image_path) {
                        if (!empty($image_path)) {
                            echo '<img src="' . $image_path . '" alt="post image">';
                        }
                    }
                    ?>
                </div>
                <!-- like/comment -->
                <div class="post-react">
                    <form action="" method="POST" class="reaction-form">
                        <input type="hidden" name="post_id" value="<?php echo $data['post_id']; ?>">
                        <button type="submit" name="like">
                            <i class="fa-solid fa-heart"></i> <?php echo getLikeCount($connect, $data['post_id']); ?>
                        </button>
                    </form>
                
                    <button class="comment-btn">
                        <i class="fa-regular fa-comment"></i><?php echo getCommentCount($connect, $data['post_id']); ?>
                    </button>
                </div>
                <!-- Comment Section (Hidden Initially) -->
                <form action="" method="POST" class="comment-form">
                    <input type="hidden" name="post_id" value="<?php echo $data['post_id']; ?>">
                    <div class="comment-input-container">
                        <textarea class="text" name="text" placeholder="Write a comment.." required></textarea>
                        <button type="submit" name="comment" class="send-icon">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
                <!-- list comment -->
                <div class="comments-list">

                    <?php $comments = getComments($connect, $data['post_id']); ?>
                    <?php if (!empty($comments)) { ?>
                    <?php foreach ($comments as $comment) { ?>
                    <div class="comment">
                        <a href="profile.php?user_id=<?php echo $comment['user_id']; ?>">
                            <img src="./img/<?php echo $comment['image']; ?>" alt="user image">
                        </a>
                        <a href="profile.php?user_id=<?php echo $comment['user_id']; ?>">
                            <p><strong><?php echo $comment['name']; ?>:</strong> </a></p>
                            <p ><?php echo $comment['text']; ?></p>
                        

                        <!-- delete comment -->
                        <?php if ($comment['user_id'] == $user_id) { ?>
                            <form action="" method="POST" class="delete-comment-form">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                <button type="submit" name="delete" class="delete-icon">
                                    <i class="fa-solid fa-trash" ></i>
                                </button>
                            </form>
                            <?php } ?>

                    </div>
                    <?php } ?>
                    <?php } else{ ?>
                        <?php echo "No comments yet"; }?>
                </div>
            </article>
            <?php } ?>
        </main>
    </div>
</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Hide all comments lists and comment forms on page load
    document.querySelectorAll(".comments-list, .comment-form").forEach(element => {
        element.style.display = "none";
    });

    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function () {
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
});


</script>

</html>
