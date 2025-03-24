<?php
include "connection.php";

// Check if we're viewing a specific user's profile (from community page)
if (isset($_GET['user_id'])) {
    $viewed_user_id = mysqli_real_escape_string($connect, $_GET['user_id']);
} else {
    // Default to logged-in user's profile
    $viewed_user_id = $_SESSION['user_id'];
}

// Fetch user data with zone information
$select_user = "SELECT `users`.*, `zone`.`zone_name` 
                FROM `users` 
                LEFT JOIN `zone` ON `users`.`zone_id` = `zone`.`zone_id` 
                WHERE `users`.`user_id`='$viewed_user_id'";
$run_select = mysqli_query($connect, $select_user);
$fetch = mysqli_fetch_assoc($run_select);

// Check if the current user is viewing their own profile
$is_own_profile = ($_SESSION['user_id'] == $viewed_user_id);
// Handle form submission for adding business
if (isset($_POST['add_business'])) {
    $company_name = mysqli_real_escape_string($connect, $_POST['company_name']);
    $company_type = mysqli_real_escape_string($connect, $_POST['company_type']);
    $contact_info = $_POST['contact_info']; // This will be an array

    // Convert the contact info array to a comma-separated string
    $contact_info_str = implode(", ", $contact_info);

    // Handle file upload
    $portfolio_path = '';
    if (isset($_FILES['portfolio']) && $_FILES['portfolio']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = './files/';
        
        $file_name = basename($_FILES['portfolio']['name']);
        $file_tmp = $_FILES['portfolio']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validate file type
        $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        if (in_array($file_ext, $allowed_extensions)) {
            // Generate unique filename
            $unique_name = uniqid() . '_' . $file_name;
            $target_path = $upload_dir . $unique_name;
            
            if (move_uploaded_file($file_tmp, $target_path)) {
                $portfolio_path = $target_path;
            } else {
                echo "<script>alert('Error uploading portfolio file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX, PPT, PPTX are allowed.');</script>";
        }
    }

    // Update role_id to 2 and include portfolio path
    $update_query = "UPDATE `users` 
                     SET `company_name`='$company_name', `company_type`='$company_type', 
                         `contact_info`='$contact_info_str', `role_id`=2, `portfolio`='$portfolio_path' 
                     WHERE `user_id`='$user_id'";
    $run_update = mysqli_query($connect, $update_query);

    if ($run_update) {
        header("Location: profile.php");
        exit();
    } 
}

// Handle form submission for updating password
if (isset($_POST['update_password'])) {
    $current_password = mysqli_real_escape_string($connect, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($connect, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($connect, $_POST['confirm_password']);

    // Fetch the current password from the database
    $select_password = "SELECT `password` FROM `users` WHERE `user_id`='$user_id'";
    $run_select_password = mysqli_query($connect, $select_password);
    $fetch_password = mysqli_fetch_assoc($run_select_password);

    // Verify the current password
    if (password_verify($current_password, $fetch_password['password'])) {
        // Check if the new password matches the confirmation
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_password_query = "UPDATE `users` SET `password`='$hashed_password' WHERE `user_id`='$user_id'";
            $run_update_password = mysqli_query($connect, $update_password_query);

            if ($run_update_password) {
                echo "<script>alert('Password updated successfully.');</script>";
            } else {
                echo "<script>alert('Failed to update password.');</script>";
            }
        } else {
            echo "<script>alert('New password and confirmation do not match.');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/profile.css">
    <title>Profile</title>

</head>
<body>
    <div class="container">
        <div class="profile">
            <?php if (!empty($fetch['image'])){ ?>
                <div class="profile-pic">
                    <img src="img/<?php echo $fetch['image']; ?>" alt="Profile Picture">
                </div>
            <?php } ?>
            <div class="profile-info">
                <h1>Name: <?php echo $fetch['name']; ?></h1>
                <p>Email: <?php echo $fetch['email']; ?></p>
                <p>Phone Number: <?php echo $fetch['phone']; ?></p>

                <!-- Display Age if it exists -->
                <?php if (!empty($fetch['age'])){ ?>
                    <p>Age: <?php echo $fetch['age']; ?></p>
                <?php } ?>

                <!-- Display Address if it exists -->
                <?php if (!empty($fetch['location'])){ ?>
                    <p>Address: <?php echo $fetch['location']; ?></p>
                <?php } ?>

                <!-- Display Zone if it exists -->
                <?php if (!empty($fetch['zone_name'])){ ?>
                    <p>Zone: <?php echo $fetch['zone_name']; ?></p>
                <?php } ?>

                <!-- Display Job Title if it exists -->
                <?php if (!empty($fetch['job_title'])){ ?>
                    <p>Job Title: <?php echo $fetch['job_title']; ?></p>
                <?php } ?>

                <!-- Display Company Name if it exists -->
                <?php if (!empty($fetch['company_name'])){ ?>
                    <p>Company Name: <?php echo $fetch['company_name']; ?></p>
                <?php } ?>

                <!-- Display Company Type if it exists -->
                <?php if (!empty($fetch['company_type'])){ ?>
                    <p>Company Type: <?php echo $fetch['company_type']; ?></p>
                <?php } ?>
                
                <!-- Display Contact Info if it exists -->
                <?php if (!empty($fetch['contact_info'])){ ?>
                    <p>Contact Info: <?php echo $fetch['contact_info']; ?></p>
                <?php } ?>

                <!-- Display Portfolio if it exists -->
                <?php if (!empty($fetch['portfolio'])){ ?>
                    <p>Portfolio: <a href="<?php echo $fetch['portfolio']; ?>" target="_blank">View Portfolio</a></p>
                <?php } ?>
                
                <?php if ($is_own_profile){ ?>
                    <!-- Only show edit options if it's the user's own profile -->
                     <a href="edit_profile.php">Edit Profile</a>
                     <!-- Add Your Business Button -->
                      <?php if ($fetch['role_id'] != 2){ ?>
                        <button id="openModalBtn">Add Your Business</button>
                        <?php } ?>
                        <!-- Update Password Button -->
                         <button id="openPasswordModalBtn">Update Password</button>
                         <?php } ?>
                        </div>
                    </div>
                </div>
                

                <!-- Only show the modals if it's the user's own profile -->
                 <?php if ($is_own_profile){ ?>
    <!-- Pop-up Modal for Adding Business -->
     <div id="businessModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Your Business</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="company_name">Company Name:</label>
                <input type="text" name="company_name" id="company_name" required>
                <label for="company_type">Company Type:</label>
                <input type="text" name="company_type" id="company_type" required>
                <!-- Portfolio File Upload -->
                 <label for="portfolio">Upload Portfolio (PDF/DOC/PPT):</label>
                 <input type="file" name="portfolio" id="portfolio" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                 <!-- Contact Info Fields -->
                  <div id="contactInfoContainer">
                    <label for="contact_info">Contact Info:</label>
                    <input type="text" name="contact_info[]" required>
                </div>
                <!-- Button to Add More Contact Info Fields -->
                 <button type="button" id="addContactInfoBtn">Add Another Contact Info</button>
                 <input type="submit" name="add_business" value="Submit Business">
                </form>
            </div>
        </div>

    <!-- Pop-up Modal for Updating Password -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Password</h2>
            <form action="" method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <input type="submit" name="update_password" value="Update Password">
            </form>
        </div>
    </div>
    <?php } ?>

    <!-- JavaScript for Modals and Dynamic Fields -->
    <script>
        // Get the modals
        const businessModal = document.getElementById("businessModal");
        const passwordModal = document.getElementById("passwordModal");

        // Get the buttons that open the modals
        const openModalBtn = document.getElementById("openModalBtn");
        const openPasswordModalBtn = document.getElementById("openPasswordModalBtn");

        // Get the <span> elements that close the modals
        const closeBtns = document.querySelectorAll(".close");

        // Open the business modal when the button is clicked
        if (openModalBtn) {
            openModalBtn.addEventListener("click", () => {
                businessModal.style.display = "block";
            });
        }

        // Open the password modal when the button is clicked
        if (openPasswordModalBtn) {
            openPasswordModalBtn.addEventListener("click", () => {
                passwordModal.style.display = "block";
            });
        }

        // Close the modals when the close button is clicked
        closeBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                businessModal.style.display = "none";
                passwordModal.style.display = "none";
            });
        });

        // Close the modals when clicking outside of them
        window.addEventListener("click", (event) => {
            if (event.target === businessModal || event.target === passwordModal) {
                businessModal.style.display = "none";
                passwordModal.style.display = "none";
            }
        });

        // Add More Contact Info Fields
        const addContactInfoBtn = document.getElementById("addContactInfoBtn");
        const contactInfoContainer = document.getElementById("contactInfoContainer");

        if (addContactInfoBtn) {
            addContactInfoBtn.addEventListener("click", () => {
                const newInput = document.createElement("input");
                newInput.type = "text";
                newInput.name = "contact_info[]";
                newInput.required = true;
                contactInfoContainer.appendChild(newInput);
            });
        }
    </script>
</body>
</html>
