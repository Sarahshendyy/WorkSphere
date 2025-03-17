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

// Handle form submission for adding business
if (isset($_POST['add_business'])) {
    $company_name = mysqli_real_escape_string($connect, $_POST['company_name']);
    $company_type = mysqli_real_escape_string($connect, $_POST['company_type']);
    $contact_info = $_POST['contact_info']; // This will be an array

    // Convert the contact info array to a comma-separated string
    $contact_info_str = implode(", ", $contact_info);

    // Update role_id to 2 
    $update_query = "UPDATE `users` 
                     SET `company_name`='$company_name', `company_type`='$company_type', 
                         `contact_info`='$contact_info_str', `role_id`=2 
                     WHERE `user_id`='$user_id'";
    $run_update = mysqli_query($connect, $update_query);

    if ($run_update) {
        header("Location: profile.php");
        exit();
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
            <?php if (!empty($fetch['image'])): ?>
                <div class="profile-pic">
                    <img src="img/<?php echo $fetch['image']; ?>" alt="Profile Picture">
                </div>
            <?php endif; ?>
            <div class="profile-info">
                <h1>Name: <?php echo $fetch['name']; ?></h1>
                <p>Email: <?php echo $fetch['email']; ?></p>
                <p>Phone Number: <?php echo $fetch['phone']; ?></p>

                <!-- Display Age if it exists -->
                <?php if (!empty($fetch['age'])): ?>
                    <p>Age: <?php echo $fetch['age']; ?></p>
                <?php endif; ?>

                <!-- Display Address if it exists -->
                <?php if (!empty($fetch['location'])): ?>
                    <p>Address: <?php echo $fetch['location']; ?></p>
                <?php endif; ?>

                <!-- Display Zone if it exists -->
                <?php if (!empty($fetch['zone_name'])): ?>
                    <p>Zone: <?php echo $fetch['zone_name']; ?></p>
                <?php endif; ?>

                <!-- Display Job Title if it exists -->
                <?php if (!empty($fetch['job_title'])): ?>
                    <p>Job Title: <?php echo $fetch['job_title']; ?></p>
                <?php endif; ?>

                <!-- Display Company Name if it exists -->
                <?php if (!empty($fetch['company_name'])): ?>
                    <p>Company Name: <?php echo $fetch['company_name']; ?></p>
                <?php endif; ?>

                <!-- Display Company Type if it exists -->
                <?php if (!empty($fetch['company_type'])): ?>
                    <p>Company Type: <?php echo $fetch['company_type']; ?></p>
                <?php endif; ?>

                <!-- Display Contact Info if it exists -->
                <?php if (!empty($fetch['contact_info'])): ?>
                    <p>Contact Info: <?php echo $fetch['contact_info']; ?></p>
                <?php endif; ?>

                <a href="edit_profile.php">Edit Profile</a>

                <!-- Add Your Business Button -->
                <?php if ($fetch['role_id'] != 2): ?>
                    <button id="openModalBtn">Add Your Business</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pop-up Modal -->
    <div id="businessModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Your Business</h2>
            <form action="" method="post">
                <label for="company_name">Company Name:</label>
                <input type="text" name="company_name" id="company_name" required>

                <label for="company_type">Company Type:</label>
                <input type="text" name="company_type" id="company_type" required>

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

    <!-- JavaScript for Modal and Dynamic Fields -->
    <script>
        // Get the modal
        const modal = document.getElementById("businessModal");

        // Get the button that opens the modal
        const openModalBtn = document.getElementById("openModalBtn");

        // Get the <span> element that closes the modal
        const closeBtn = document.querySelector(".close");

        // Open the modal when the button is clicked
        if (openModalBtn) {
            openModalBtn.addEventListener("click", () => {
                modal.style.display = "block";
            });
        }

        // Close the modal when the close button is clicked
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // Close the modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });

        // Add More Contact Info Fields
        const addContactInfoBtn = document.getElementById("addContactInfoBtn");
        const contactInfoContainer = document.getElementById("contactInfoContainer");

        addContactInfoBtn.addEventListener("click", () => {
            const newInput = document.createElement("input");
            newInput.type = "text";
            newInput.name = "contact_info[]";
            newInput.required = true;
            contactInfoContainer.appendChild(newInput);
        });
    </script>
</body>
</html>