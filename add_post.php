<?php
include "connection.php";
$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $images = isset($_FILES['images']) ? $_FILES['images'] : null;
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    $image_paths = [];
    $image_paths_str = null; // Initialize to null
    
    if ($images && is_array($images['tmp_name']) && count($images['tmp_name']) > 0) {  
        foreach ($images['tmp_name'] as $key => $tmp_name) {
            if (!empty($tmp_name)) { 
                $image_name = basename($images['name'][$key]);
                $image_path = './img/' . $image_name;
                move_uploaded_file($tmp_name, $image_path);
                $image_paths[] = $image_path;
            }
        }
        $image_paths_str = !empty($image_paths) ? implode(',', $image_paths) : null;
    }

    $file_path = null; 
    if ($file && !empty($file['tmp_name'])) { // Check if a file was actually uploaded
        $file_path = './files/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $file_path);
    }


    if ($image_paths_str !== null && $file_path !== null) {
        // Both images and file are uploaded
        $insert_both = "INSERT INTO community VALUES (NULL,'$user_id', '$description', '$image_paths_str', '$file_path')";
        $run_insert_both = mysqli_query($connect, $insert_both);

    } elseif ($image_paths_str !== null) {
        // Only images are uploaded
        $insert_images = "INSERT INTO community VALUES (NULL,'$user_id', '$description', '$image_paths_str', NULL)";
        $run_insert_images = mysqli_query($connect, $insert_images);

    } elseif ($file_path !== null) {
        // Only file is uploaded
        $insert_file = "INSERT INTO community VALUES (NULL,'$user_id', '$description', NULL, '$file_path')";
        $run_insert_file = mysqli_query($connect, $insert_file);

    } else {
        // Neither images nor file is uploaded
        $insert_none = "INSERT INTO community VALUES (NULL,'$user_id', '$description', NULL, NULL)";
        $run_insert_none = mysqli_query($connect, $insert_none);
    }

    header  ('Location: community.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/add-post.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"> <!-- Ensure Font Awesome is linked -->
    <title>Add Post</title>
</head>
<body>
    <div class="container">
        <h1>Create a New Post</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <textarea name="description" placeholder="Write your description here..." required></textarea>
            <div class="input-group image-upload">
                <label for="images">
                <i class="fas fa-image"></i> Add Images:
                </label>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>
            <div class="input-group file-upload">
                <label for="file">
                <i class="fas fa-file"></i> Add File (e.g., PDF, DOC):
                </label>
                <input type="file" id="file" name="file">
            </div>
            <button type="submit" name="submit">Submit Post</button>
        </form>
    </div>
</body>
</html>
