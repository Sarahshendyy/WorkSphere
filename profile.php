<?php
include "nav.php";

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

    // Handle portfolio upload (logic similar to edit_profile)
    $portfolio = '';
    if (isset($_FILES['portfolio']) && $_FILES['portfolio']['name']) {
        $portfolio_name = $_FILES['portfolio']['name'];
        $portfolio_tmp = $_FILES['portfolio']['tmp_name'];
        $portfolio_path = "./files/" . basename($portfolio_name);

        // Validate file type
        $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'jpg', 'png'];
        $file_ext = strtolower(pathinfo($portfolio_name, PATHINFO_EXTENSION));
        if (in_array($file_ext, $allowed_extensions)) {
            if (move_uploaded_file($portfolio_tmp, $portfolio_path)) {
                $portfolio = $portfolio_name;
            } else {
                echo "<script>alert('Error uploading portfolio file.');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type. Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG');</script>";
        }
    }

    // Update role_id to 2 and include portfolio name (not path, for consistency)
    $update_query = "UPDATE `users` 
    SET `company_name`='$company_name', `company_type`='$company_type', 
        `contact_info`='$contact_info_str', `role_id`=2, `portfolio`='$portfolio' 
    WHERE `user_id`='$viewed_user_id'";
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
    $select_password = "SELECT `password` FROM `users` WHERE `user_id`='$viewed_user_id'";
    $run_select_password = mysqli_query($connect, $select_password);
    $fetch_password = mysqli_fetch_assoc($run_select_password);

    // Verify the current password
    if (password_verify($current_password, $fetch_password['password'])) {
        // Check if the new password matches the confirmation
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $update_password_query = "UPDATE `users` SET `password`='$hashed_password' WHERE `user_id`='$viewed_user_id'";
            $run_update_password = mysqli_query($connect, $update_password_query);

            if ($run_update_password) {
                echo "<script>alert('Password updated successfully.');</script>";
            } 
        } 
    } 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" 
    integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./css/profile.css">
    <title>Profile</title>

</head>
<body>
   <div class="container">
   <div class="back-button-wrapper" style="display: flex; gap: 10px; align-items: center;">
    <button class="back-button" onclick="window.location.href='edit_profile.php'" title="Edit Profile" aria-label="Edit Profile">
        <i class="fas fa-user-edit icon"></i>
    </button>

    <?php if ($is_own_profile) { ?>
        <button id="openPasswordModalBtn" class="back-button" title="Update Password" aria-label="Update Password">
            <i class="fas fa-key icon"></i>
        </button>
    <?php } ?>
</div>

    <div class="profile">
        <!-- New: Left column for profile picture and password button -->
        <div class="profile-image-section">
            <?php if (!empty($fetch['image'])){ ?>
                <div class="profile-pic">
                    <img src="./img/<?php echo $fetch['image']; ?>" alt="Profile Picture">
                </div>
            <?php } ?>

        </div>

        <!-- Right column for profile information -->
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($fetch['name']); ?></h1>

            <?php
            // Helper function to generate a standard info item paragraph
            function render_info_item($class_name, $label, $value) {
                if (!isset($value) || $value === '') return ''; // Check for null or empty string
                return '<p class="info-item ' . htmlspecialchars($class_name) . '"><strong>' . htmlspecialchars($label) . ':</strong> ' . htmlspecialchars($value) . '</p>';
            }

            // Helper function specifically for the portfolio link
            function render_portfolio_item($class_name, $label, $filename) {
                if (empty($filename)) return '';
                return '<p class="info-item ' . htmlspecialchars($class_name) . '"><strong>' . htmlspecialchars($label) . ':</strong> <a href="./files/' . htmlspecialchars($filename) . '" target="_blank">View Portfolio</a></p>';
            }

            $info_groups = [];

            // Group 1: Email & Phone
            $email_html = render_info_item('email', 'Email', $fetch['email'] ?? null);
            $phone_html = render_info_item('phone', 'Phone', $fetch['phone'] ?? null);
            if ($email_html || $phone_html) {
                $info_groups[] = [$email_html, $phone_html];
            }

            // Group 2: Age & Address (only if it's the user's own profile)
            if ($is_own_profile) {
                $age_html = render_info_item('age', 'Age', $fetch['age'] ?? null);
                $location_html = render_info_item('location', 'Address', $fetch['location'] ?? null);
                if ($age_html || $location_html) {
                    $info_groups[] = [$age_html, $location_html];
                }
            }
            
            // Group 3: Zone (only if own profile and not empty) & Job Title
            $zone_html = '';
            if ($is_own_profile && !empty($fetch['zone_name'])) {
                 $zone_html = render_info_item('zone', 'Zone', $fetch['zone_name']);
            }
            $job_title_html = render_info_item('job', 'Job Title', $fetch['job_title'] ?? null);
            if ($zone_html || $job_title_html) {
                $info_groups[] = [$zone_html, $job_title_html];
            }

            // Group 4: Company Name & Company Type
            $company_name_html = render_info_item('company', 'Company', $fetch['company_name'] ?? null);
            $company_type_html = render_info_item('type', 'Type', $fetch['company_type'] ?? null);
            if ($company_name_html || $company_type_html) {
                $info_groups[] = [$company_name_html, $company_type_html];
            }

            // Group 5: Contact Info & Portfolio
            $portfolio_html = render_portfolio_item('portfolio', 'Portfolio', $fetch['portfolio'] ?? null);
            $contact_info_html = render_info_item('contact', 'Contact Info', $fetch['contact_info'] ?? null);
            if ($contact_info_html || $portfolio_html) {
                $info_groups[] = [$portfolio_html,$contact_info_html];
            }

            // Render all groups
            foreach ($info_groups as $group) {
                $item1_html = $group[0] ?? '';
                $item2_html = $group[1] ?? '';

                // Only create an info-pair div if at least one item in the group exists
                if ($item1_html || $item2_html) {
                    echo '<div class="info-pair">';
                    echo $item1_html; // Will render nothing if empty
                    echo $item2_html; // Will render nothing if empty
                    echo '</div>';
                }
            }
            ?>
            
           <?php if ($is_own_profile) { ?>
    <div class="profile-actions">
        <?php if ($fetch['role_id'] != 2) { ?>
            <button id="openModalBtn" class="btn btn-add">Add Your Business</button>
        <?php } ?>
        <?php if ($fetch['role_id'] == 1 || $fetch['role_id'] == 2) { ?>
            <button id="listWorkspaceBtn" class="btn btn-add" onclick="window.location.href='./workspace/listing_workspaces.php'">
                List Your Workspace
            </button>
        <?php } ?>
    </div>
<?php } ?>





                <!-- Only show the modals if it's the user's own profile -->
                 <?php if ($is_own_profile){ ?>
    <!-- Pop-up Modal for Adding Business -->
     <div id="businessModal" class="modal">
        <div class="modal-content">
            <span class="close">×</span>
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
            <span class="close">×</span>
            <h2>Update Password</h2>
            <form action="" method="post" id="updatePasswordForm">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
                <span id="currentPasswordError" class="error-message"></span>

                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
                <span id="newPasswordError" class="error-message"></span>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <span id="confirmPasswordError" class="error-message"></span>

                <input type="submit" name="update_password" value="Update Password">
            </form>
        </div>
    </div>
    <?php } ?>

    <!-- JavaScript for Modals and Dynamic Fields -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Modal Handling ---

        // Get the modals
        const businessModal = document.getElementById("businessModal");
        const passwordModal = document.getElementById("passwordModal");

        // Get the buttons that open the modals
        const openModalBtn = document.getElementById("openModalBtn");
        const openPasswordModalBtn = document.getElementById("openPasswordModalBtn");

        // Get all elements that can close modals (close buttons)
        const closeBtns = document.querySelectorAll(".modal .close");

        // Helper Function to Open Modal
        function openModal(modal) {
            if (modal) {
                modal.classList.add("show"); // Add class to trigger animation
                // Optional: Focus the first input field in the modal
                const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
                if (firstInput) {
                    // Timeout helps ensure the modal is visible before focusing
                    setTimeout(() => firstInput.focus(), 50);
                }
            }
        }

        // Helper Function to Close Modal
        function closeModal(modal) {
            if (modal) {
                modal.classList.remove("show"); // Remove class
            }
        }

        // Event listener for opening the business modal
        if (openModalBtn) {
            openModalBtn.addEventListener("click", (e) => {
                e.preventDefault(); // Prevent potential default button behavior
                openModal(businessModal);
            });
        }

        // Event listener for opening the password modal
        if (openPasswordModalBtn) {
            openPasswordModalBtn.addEventListener("click", (e) => {
                e.preventDefault(); // Prevent potential default button behavior
                openModal(passwordModal);
            });
        }

        // Event listener for closing modals via close buttons
        closeBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                // Find the parent modal element and close it
                const modal = btn.closest('.modal'); // .closest finds the nearest ancestor matching the selector
                closeModal(modal);
            });
        });

        // Event listener for closing modals by clicking outside the modal content
        window.addEventListener("click", (event) => {
            // Check if the click target is the modal overlay itself (has .modal and .show)
            if (event.target.classList.contains('modal') && event.target.classList.contains('show')) {
                closeModal(event.target); // event.target is the modal overlay in this case
            }
        });

        // Event listener for closing modals with the Escape key
        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(closeModal); // Close any open modals
            }
        });


        // --- Add More Contact Info Fields ---
        const addContactInfoBtn = document.getElementById("addContactInfoBtn");
    const contactInfoContainer = document.getElementById("contactInfoContainer");

    if (addContactInfoBtn && contactInfoContainer) { // Check if both elements exist
        addContactInfoBtn.addEventListener("click", () => {
            // Create a new input element
            const newInput = document.createElement("input");
            newInput.type = "text";
            newInput.name = "contact_info[]";
            newInput.placeholder = "Enter contact info"; // Add placeholder text
            newInput.required = true;

            // Apply styles to match existing inputs
            newInput.style.marginTop = "10px";
            newInput.style.width = "100%";
            newInput.style.padding = "10px";
            newInput.style.border = "1px solid #ccc";
            newInput.style.borderRadius = "5px";

            // Append the new input to the container
            contactInfoContainer.appendChild(newInput);
        });
    }


        // --- Password Validation ---

        const updatePasswordForm = document.getElementById('updatePasswordForm');
        // Check if the form exists before adding listeners
        if (updatePasswordForm) {
            const currentPasswordInput = document.getElementById('current_password');
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            const currentPasswordError = document.getElementById('currentPasswordError');
            const newPasswordError = document.getElementById('newPasswordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            // Function to clear all password errors
            const clearPasswordErrors = () => {
                if (currentPasswordError) currentPasswordError.textContent = '';
                if (newPasswordError) newPasswordError.textContent = '';
                if (confirmPasswordError) confirmPasswordError.textContent = '';
            };

            // Clear errors when the password modal is opened
            if (openPasswordModalBtn && passwordModal) {
                 openPasswordModalBtn.addEventListener('click', clearPasswordErrors);
                 // Also clear errors if closed without submitting (e.g., clicking X or outside)
                 passwordModal.addEventListener('transitionend', (event) => {
                    // Check if the transition was for opacity and it's now 0 (closing)
                    if (event.propertyName === 'opacity' && !passwordModal.classList.contains('show')) {
                        clearPasswordErrors();
                        // Optionally reset form fields
                        // updatePasswordForm.reset();
                    }
                 });
            }


            // Real-time input validation
            updatePasswordForm.addEventListener('input', function(event) {
                const target = event.target;

                // Current Password Validation (Basic check for emptiness)
                if (target === currentPasswordInput) {
                    if (!target.value.trim()) {
                        currentPasswordError.textContent = 'Current password is required.';
                    } else {
                        currentPasswordError.textContent = '';
                    }
                }

                // New Password Validation
                if (target === newPasswordInput) {
                    const passwordValue = target.value;
                    newPasswordError.textContent = ''; // Clear previous error first

                    if (!passwordValue) {
                        newPasswordError.textContent = 'New password is required.';
                    } else if (passwordValue.length < 8) {
                        newPasswordError.textContent = 'Password must be at least 8 characters long.';
                    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(passwordValue)) {
                        // Simplified regex: checks for at least one lowercase, one uppercase, one digit
                        newPasswordError.textContent = 'Must include lowercase, uppercase, and a digit.';
                    }

                    // Also validate confirm password whenever new password changes
                    if (confirmPasswordInput.value) {
                        if (passwordValue !== confirmPasswordInput.value) {
                            confirmPasswordError.textContent = 'Passwords do not match.';
                        } else {
                            confirmPasswordError.textContent = '';
                        }
                    }
                }

                // Confirm Password Validation
                if (target === confirmPasswordInput) {
                    if (newPasswordInput.value !== target.value) {
                        confirmPasswordError.textContent = 'Passwords do not match.';
                    } else {
                        confirmPasswordError.textContent = '';
                    }
                }
            });

            // Final validation before submitting
            updatePasswordForm.addEventListener('submit', function(event) {
                let isValid = true; // Assume valid initially

                // Re-validate all fields on submit
                if (!currentPasswordInput.value.trim()) {
                    currentPasswordError.textContent = 'Current password is required.';
                    isValid = false;
                } else {
                     currentPasswordError.textContent = ''; // Clear if valid now
                }


                const newPasswordValue = newPasswordInput.value;
                 newPasswordError.textContent = ''; // Clear previous error first
                 if (!newPasswordValue) {
                    newPasswordError.textContent = 'New password is required.';
                    isValid = false;
                } else if (newPasswordValue.length < 8) {
                    newPasswordError.textContent = 'Password must be at least 8 characters long.';
                     isValid = false;
                 } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(newPasswordValue)) {
                     newPasswordError.textContent = 'Must include lowercase, uppercase, and a digit.';
                     isValid = false;
                 }


                confirmPasswordError.textContent = ''; // Clear previous error first
                 if (!confirmPasswordInput.value) {
                     confirmPasswordError.textContent = 'Please confirm the new password.';
                     isValid = false;
                 } else if (newPasswordValue !== confirmPasswordInput.value) {
                     confirmPasswordError.textContent = 'Passwords do not match.';
                     isValid = false;
                 }


                // Prevent form submission if any validation failed
                if (!isValid) {
                    event.preventDefault();
                }
                // If valid, the form will submit normally.
            });
        } // End if(updatePasswordForm)

    }); // End DOMContentLoaded
</script>
</body>
</html>
