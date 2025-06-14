<?php
include "connection.php";

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch user data with zone information
$select_user = "SELECT `users`.*, `zone`.`zone_name` 
                FROM `users` 
                LEFT JOIN `zone` ON `users`.`zone_id` = `zone`.`zone_id` 
                WHERE `users`.`user_id`='$user_id'";
$run_select = mysqli_query($connect, $select_user);
$fetch = mysqli_fetch_assoc($run_select);

// Fetch all zones for dropdown
$select_zones = "SELECT * FROM `zone`";
$run_zones = mysqli_query($connect, $select_zones);
$zones = mysqli_fetch_all($run_zones, MYSQLI_ASSOC);

if (isset($_POST['update'])) {
    // Initialize update fields array
    $update_fields = [];
    
    // Process each field only if it was submitted
    if (isset($_POST['name'])) {
        $name = mysqli_real_escape_string($connect, $_POST['name']);
        $update_fields[] = "`name`='$name'";
    }
    
    if (isset($_POST['email'])) {
        $email = mysqli_real_escape_string($connect, $_POST['email']);
        $update_fields[] = "`email`='$email'";
    }
    
    if (isset($_POST['age'])) {
        $age = mysqli_real_escape_string($connect, $_POST['age']);
        $update_fields[] = "`age`='$age'";
    }
    
    if (isset($_POST['address'])) {
        $address = mysqli_real_escape_string($connect, $_POST['address']);
        $update_fields[] = "`location`='$address'";
    }
    
    if (isset($_POST['job_title'])) {
        $job_title = mysqli_real_escape_string($connect, $_POST['job_title']);
        $update_fields[] = "`job_title`='$job_title'";
    }
    
    if (isset($_POST['zone_id'])) {
        $zone_id = mysqli_real_escape_string($connect, $_POST['zone_id']);
        $update_fields[] = "`zone_id`='$zone_id'";
    }
    
    // Handle company-specific fields if user is a company (role_id == 2)
    if ($fetch['role_id'] == 2) {
        if (isset($_POST['company_name'])) {
            $company_name = mysqli_real_escape_string($connect, $_POST['company_name']);
            $update_fields[] = "`company_name`='$company_name'";
        }
        
        if (isset($_POST['contact_info'])) {
            $contact_info = mysqli_real_escape_string($connect, $_POST['contact_info']);
            $update_fields[] = "`contact_info`='$contact_info'";
        }
        
        if (isset($_POST['company_type'])) {
            $company_type = mysqli_real_escape_string($connect, $_POST['company_type']);
            $update_fields[] = "`company_type`='$company_type'";
        }
        
        // Handle portfolio file upload
        if (isset($_FILES['portfolio']) && $_FILES['portfolio']['name']) {
            $portfolio_name = $_FILES['portfolio']['name'];
            $portfolio_tmp = $_FILES['portfolio']['tmp_name'];
            $portfolio_path = "./files/" . basename($portfolio_name);
            if (move_uploaded_file($portfolio_tmp, $portfolio_path)) {
                $update_fields[] = "`portfolio`='$portfolio_name'";
            }
        }
    }
    
    // Handle profile image upload
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "./img/" . basename($image_name);
        if (move_uploaded_file($image_tmp, $image_path)) {
            $update_fields[] = "`image`='$image_name'";
        } else {
            echo "Failed to upload image.";
        }
    }
    
    // Only proceed with update if there are fields to update
    if (!empty($update_fields)) {
        $update_profile = "UPDATE `users` SET " . implode(', ', $update_fields) . " WHERE `user_id`='$user_id'";
        $run_update = mysqli_query($connect, $update_profile);
        
        if ($run_update) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Failed to update profile: " . mysqli_error($connect);
        }
    } else {
        // No fields were updated, redirect back
        header("Location: profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Profile</title>
<link rel="stylesheet" href="./css/edit_profile.css" />
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
/>
<script>
// Preview image before upload
document.getElementById('profile-image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-pic-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>
</head>
<body>

<div class="container">
  <div class="back-button-wrapper">
    <button class="back-button" onclick="window.history.back()">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <div class="profile">
    <form action="edit_profile.php" method="post" enctype="multipart/form-data">

      <div class="profile-pic-container">
        <div class="profile-pic">
          <img src="./img/<?php echo htmlspecialchars($fetch['image'] ?: 'default.png'); ?>" alt="Profile Picture" id="profile-pic-preview" />
        </div>
        <input type="file" name="image" id="profile-image-input" accept="image/png, image/jpeg, image/gif" />
        <label for="profile-image-input" class="camera-icon-label" title="Change profile picture">
          <i class="fas fa-camera"></i>
        </label>
      </div>

      <div class="profile-info">
        <h1>Edit Profile</h1>

        <div class="input-row">
          <div class="input-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($fetch['name']); ?>" />
          </div>
          <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($fetch['email']); ?>" />
          </div>
        </div>

        <div class="input-row">
          <div class="input-group">
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" value="<?php echo htmlspecialchars($fetch['age'] ?? ''); ?>" />
          </div>
          <div class="input-group">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($fetch['location'] ?? ''); ?>" />
          </div>
        </div>

        <div class="input-row">
          <div class="input-group">
            <label for="zone_id">Zone:</label>
            <select name="zone_id" id="zone_id">
              <?php foreach ($zones as $zone) { ?>
                <option value="<?php echo $zone['zone_id']; ?>" <?php echo ($zone['zone_id'] == $fetch['zone_id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($zone['zone_name']); ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="input-group">
            <label for="job_title">Job Title:</label>
            <input type="text" name="job_title" id="job_title" value="<?php echo htmlspecialchars($fetch['job_title'] ?? ''); ?>" />
          </div>
        </div>

        <?php if ($fetch['role_id'] == 2) { ?>
          <div class="input-row">
            <div class="input-group">
              <label for="company_name">Company Name:</label>
              <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($fetch['company_name'] ?? ''); ?>" />
            </div>
            <div class="input-group">
              <label for="contact_info">Contact Info:</label>
              <input type="text" name="contact_info" id="contact_info" value="<?php echo htmlspecialchars($fetch['contact_info'] ?? ''); ?>" />
            </div>
          </div>

          <div class="input-row">
            <div class="input-group">
              <label for="company_type">Company Type:</label>
              <input type="text" name="company_type" id="company_type" value="<?php echo htmlspecialchars($fetch['company_type'] ?? ''); ?>" />
            </div>
            <div class="input-group">
              <label for="portfolio">Portfolio (PDF, DOC, etc):</label>
              <input type="file" name="portfolio" id="portfolio" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.png" />
              <?php if (!empty($fetch['portfolio'])): ?>
                <a href="./files/<?php echo htmlspecialchars($fetch['portfolio']); ?>" target="_blank" class="portfolio-link">View Current Portfolio</a>
              <?php endif; ?>
            </div>
          </div>
        <?php } ?>

        <input type="submit" name="update" value="Update Profile" />
      </div>
    </form>
  </div>
</div>

</body>
</html>