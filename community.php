<?php
// include'nav.php';
include 'connection.php';
$user_id = $_SESSION['user_id'];

// Function to select_community
function select_community($connect){
    $select_community = "SELECT `community`.*, `users`.`name`, `users`.`image` FROM `community`
                         JOIN `users` ON `community`.`user_id` = `users`.`user_id`";
    $result_community = mysqli_query($connect, $select_community);
    return mysqli_fetch_all($result_community, MYSQLI_ASSOC);
}
$community = select_community($connect);

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
//comments
function getComments($connect, $post_id) {
    $comment_query = "SELECT `comment`.*, `users`.`name`, `users`.`image` FROM `comment`
                      JOIN `users` ON `comment`.`user_id` = `users`.`user_id`
                      WHERE `post_id` = $post_id ORDER BY `created_at` ASC";
    $result_comment = mysqli_query($connect, $comment_query);
    return mysqli_fetch_all($result_comment, MYSQLI_ASSOC);
}
if (isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($connect, $_POST['text']);
    if (!empty($comment_text)) {
        $insert_comment = "INSERT INTO `comment` VALUES (NULL,'$user_id', '$post_id', '$comment_text')";
        mysqli_query($connect, $insert_comment);
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
    <!-- header / buttons -->
    <header class="header">
        <h1>Our Community</h1>
    </header>
    <section class="buttons">
        <form action="" method="POST">
            <button>
                <a href="#">add<i class="fa-solid fa-plus"></i></a>
            </button>
            <button type="submit">
                All Posts
            </button>
            <button name="filter" type="submit">
                My Posts
            </button>
        </form>
    </section>
    <!-- main content -->
    <main class="content">
        <!-- loop -->
        <?php foreach ($community as $data) { ?>
        <div class="profile">
            <div class="profile-image">
                <img src="./img/<?php echo $data['image']; ?>" alt="user profile">
            </div>
            <div class="profile-name">
                <h2><?php echo $data['name']; ?></h2>
            </div>
        </div>
        <div class="post">
            <p><?php echo $data['description']; ?></p>
        </div>
        <div class="post-image">
            <img src="./img/<?php echo $data['images']; ?>" alt="post image">
        </div>
        <!-- like/comment -->
        <div class="post-react">
            <form action="" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $data['post_id']; ?>">
                <button type="submit" name="like">
                    <i class="fa-solid fa-heart"></i> <?php echo getLikeCount($connect, $data['post_id']); ?>
                </button>
            </form>
        </div>
         <!-- Comment Section -->
         <div class="comments-section">
            <form action="" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $data['post_id']; ?>">
                <textarea name="text" placeholder="Write a comment.." required></textarea>
                <button type="submit" name="comment"><i class="fa-solid fa-comment"></i> </button>
            </form>
            <div class="comments-list">
                <?php $comments = getComments($connect, $data['post_id']); ?>
                <?php foreach ($comments as $comment) { ?>
                    <div class="comment">
                        <img src="./img/<?php echo $comment['image']; ?>" alt="user image">
                        <p><strong><?php echo $comment['name']; ?>:</strong> <?php echo $comment['text']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </main>
</div>
</body>

</html>