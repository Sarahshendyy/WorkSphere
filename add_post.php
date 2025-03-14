<?php
include "connection.php";
$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $description = htmlspecialchars(mysqli_real_escape_string($connect, $_POST['description']));
    $images = isset($_FILES['images']) ? $_FILES['images'] : null;
    $file = isset($_FILES['file']) ? $_FILES['file'] : null;

    $image_paths = [];
    if ($images) {
        foreach ($images['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($images['name'][$key]);
            $image_path = './img/' . $image_name;
            move_uploaded_file($tmp_name, $image_path);
            $image_paths[] = $image_path;
        }
    }

    if ($images && $file) {
        // Both images and file are uploaded
        $file_path = './img/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $file_path);
        $image_paths_str = implode(',', $image_paths);

        $insert_both = "INSERT INTO community VALUES (NULL,'$user_id', '$description', '$image_paths_str', '$file_path')";
        $run_insert_both = mysqli_query($connect, $insert_both);

    } elseif ($images) {
        // Only images are uploaded
        $image_paths_str = implode(',', $image_paths);

        $insert_images = "INSERT INTO community VALUES (NULL,'$user_id', '$description', '$image_paths_str', NULL)";
        $run_insert_images = mysqli_query($connect, $insert_images);

    } elseif ($file) {
        // Only file is uploaded
        $file_path = './img/' . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $file_path);

        $insert_file = "INSERT INTO community VALUES (NULL,'$user_id', '$description', NULL, '$file_path')";
        $run_insert_file = mysqli_query($connect, $insert_file);

    } else {
        // Neither images nor file is uploaded
        $insert_none = "INSERT INTO community VALUES (NULL,'$user_id', '$description', NULL, NULL)";
        $run_insert_none = mysqli_query($connect, $insert_none);
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/add-post.css">
    <title>Add Post</title>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <textarea name="description" placeholder="Description"></textarea>
        <input type="file" name="images[]" multiple>
        <input type="file" name="file">
        <button type="submit" name="submit">Submit</button>
    </form>
</body>
</html>