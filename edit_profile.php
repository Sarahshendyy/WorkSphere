<?php
include "connection.php";

$user_id = $_SESSION['user_id'];

// Fetch user data with zone information
$select_user = "SELECT `users`.*, `zone`.`zone_name` 
                FROM `users` 
                LEFT JOIN `zone` ON `users`.`zone_id` = `zone`.`zone_id` 
                WHERE `users`.`user_id`='$user_id'";
$run_select = mysqli_query($connect, $select_user);
$fetch = mysqli_fetch_assoc($run_select);

// Fetch all zones for the dropdown
$select_zones = "SELECT * FROM `zone`";
$run_zones = mysqli_query($connect, $select_zones);
$zones = mysqli_fetch_all($run_zones, MYSQLI_ASSOC);

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $age = mysqli_real_escape_string($connect, $_POST['age']);
    $address = mysqli_real_escape_string($connect, $_POST['address']);
    $job_title = mysqli_real_escape_string($connect, $_POST['job_title']);
    $zone_id = mysqli_real_escape_string($connect, $_POST['zone_id']);

    // Handle image upload
    if ($_FILES['image']['name']) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "img/" . basename($image_name);

        // Move uploaded file to the img directory
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Update image path 
            $update_profile = "UPDATE `users` 
                               SET `name`='$name', `email`='$email', `age`='$age', `location`='$address', 
                                   `job_title`='$job_title', `zone_id`='$zone_id', `image`='$image_name' 
                               WHERE `user_id`='$user_id'";
        } else {
            echo "Failed to upload image.";
        }
    } else {
        // If no new image is uploaded, keep the existing image
        $update_profile = "UPDATE `users` 
                           SET `name`='$name', `email`='$email', `age`='$age', `location`='$address', 
                               `job_title`='$job_title', `zone_id`='$zone_id' 
                           WHERE `user_id`='$user_id'";
    }

    $run_update = mysqli_query($connect, $update_profile);

    if ($run_update) {
        header("Location: profile.php");
        
    } else {
        echo "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/edit_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Edit Profile</title>
</head>
<body>


<div class="container">
    <div class="back-button-wrapper">
        <a href="profile.php" class="back-button" title="Back to Profile">
            <i class="fas fa-times"></i>
        </a>
    </div>

        <div class="profile">
            <!-- Form starts BEFORE the profile picture section if pic is part of the form -->
            <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                <div class="profile-pic-container">
                    <div class="profile-pic">
                        <!-- Give the image an ID for the preview -->
                        <img src="./img/<?php echo $fetch['image']; ?>" alt="Profile Picture" id="profile-pic-preview">
                    </div>
                    <!-- Hidden File Input -->
                    <input type="file" name="image" id="profile-image-input" accept="image/png, image/jpeg, image/gif">
                    <!-- Clickable Camera Icon Label -->
                    <label for="profile-image-input" name="image" class="camera-icon-label" title="Change profile picture">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
            <div class="profile-info">
                <h1>Edit Profile</h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <label class="labels" for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?php echo $fetch['name']; ?>" required>
                    <br>
                    <label class="labels" for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo $fetch['email']; ?>" required>
                    <br>
                    <label class="labels" for="age">Age:</label>
                    <input type="number" name="age" id="age" value="<?php echo !empty($fetch['age']) ? $fetch['age'] : ''; ?>">
                    <br>
                    <label class="labels" for="address">Address:</label>
                    <input type="text" name="address" id="address" value="<?php echo !empty($fetch['address']) ? $fetch['address'] : ''; ?>">
                    <br>
                    <label  class="labels" for="zone_id">Zone:</label>
                    <select name="zone_id" id="zone_id" required>
                        <?php foreach ($zones as $zone){ ?>
                            <option value="<?php echo $zone['zone_id']; ?>" 
                                <?php echo ($zone['zone_id'] == $fetch['zone_id']) ? 'selected' : ''; ?>>
                                <?php echo $zone['zone_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <br>

                    <label class="labels" for="job-title">Job-Title:</label>
                    <input type="text" name="job_title" id="job_title" value="<?php echo !empty($fetch['job_title']) ? $fetch['job_title'] : ''; ?>">
                    <!-- <br>
                    <label for="image">Profile Picture:</label>
                    <input type="file" name="image" id="image"> -->
                    <br>

                    <input type="submit" name="update" value="Update Profile">
                </form>
            </div>
        </div>
    </div>
</body>
</html>
