<?php
// include "connection.php";

include '../mail.php';

// Handle Add Admin
if (isset($_POST['add_admin'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $phone = mysqli_real_escape_string($connect, $_POST['phone']);
    $password = password_hash('Aa.12345', PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email = "SELECT * FROM `users` WHERE `email` = '$email'";
    $email_result = mysqli_query($connect, $check_email);
    
    if (mysqli_num_rows($email_result) > 0) {
        $_SESSION['delete_status'] = 'error';
        $_SESSION['delete_message'] = 'Email already exists';
    } 
    // Check phone number length
    elseif (strlen($phone) != 11) {
        $_SESSION['delete_status'] = 'error';
        $_SESSION['delete_message'] = 'Phone number must be 11 digits';
    }
    else {
        $insert_query = "INSERT INTO `users` 
                        (`name`, `email`, `phone`, `password`, `role_id`, `created_at`) 
                        VALUES 
                        ('$name', '$email', '$phone', '$password', '4', NOW())";

        if (mysqli_query($connect, $insert_query)) {
            $_SESSION['delete_status'] = 'success';
            $_SESSION['delete_message'] = 'Admin added successfully';
            $massage1 = " 
            <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
                <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                    <h1>Welcome to the WorkSphere Admin Team, <span style='color: #fda521;'>$name</span>!</h1>
                </div>
                <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                    <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$name</span>,</p>
                    <p style='color: #00000a;'>We're excited to welcome you as a new administrator to the WorkSphere platform!</p>
                    

                    
                    
                    <p style='color: #00000a;'><strong>Important First Steps:</strong></p>
                    <ol>
                        <li style='color: #00000a'>Change your password immediately after first login</li>
                        
                        <li style='color: #00000a'>Familiarize yourself with the Admin Dashboard</li>
                    </ol>
                    
                    <p style='color: #00000a;'><strong>Your Administrator Privileges:</strong></p>
                    <ul>
                        <li style='color: #00000a'>Full access to user management system</li>
                        <li style='color: #00000a'>Ability to moderate content and resolve disputes</li>
                        <li style='color: #00000a'>Access to platform analytics and reports</li>
                        <li style='color: #00000a'>System configuration capabilities</li>
                    </ul>
                    
                    <p style='color: #00000a;'>For security reasons, please keep your login credentials confidential and enable two-factor authentication.</p>
                    
                    <p style='color: #fda521;'>We're excited to have you on the team!</p>
                    <p style='color: #00000a;'>Best regards,<br>The WorkSphere Leadership Team</p>
                </div>
                <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                    <p style='color: #fffffa;'>For admin support or urgent matters, please contact:</p>
                    <p style='color: #fffffa;'>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #fda521;'>admin-support@worksphere04@gmail.com</a></p>
                    <p style='color: #fffffa;'>Phone: [Your Admin Support Phone Number]</p>
                </div>
            </body>";

                $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Welcome Aboard';
                $mail->Body=($massage1);
                $mail->send();

        } else {
            $_SESSION['delete_status'] = 'error';
            $_SESSION['delete_message'] = 'Failed to add admin: ' . mysqli_error($connect);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle "Delete" action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $delete_user = "DELETE FROM `users` WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $delete_user)) {
        $_SESSION['delete_status'] = 'success';
        $_SESSION['delete_message'] = 'User deleted successfully';
    } else {
        $_SESSION['delete_status'] = 'error';
        $_SESSION['delete_message'] = 'Failed to delete user: ' . mysqli_error($connect);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if it's a search request
if (isset($_POST['search']) && !empty($_POST['text'])) {
    $text = mysqli_real_escape_string($connect, $_POST['text']);

    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`phone`
    FROM `users`
    WHERE `users`.`role_id` = '4' AND (`users`.`name` LIKE '%$text%' OR `users`.`email` LIKE '%$text%' OR `users`.`phone` LIKE '%$text%')";

} else {
    // Default query to fetch all users with role_id = 4
    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`phone`
    FROM `users`
    WHERE `users`.`role_id` = '4'";
}

$run_select = mysqli_query($connect, $select_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/users-list.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title">Admins List</h1>
                <div class="controls-container">
                    <div class="search-wrapper">
                        <input type="text" id="searchText" class="form-control" placeholder="Search by name, email or phone...">
                    </div>
                    <button id="addAdminBtn" class="btn btn-primary">Add Admin</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <?php
                            $counter = 1;
                            if (mysqli_num_rows($run_select) > 0) {
                                foreach ($run_select as $row) {
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?action=delete&id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm delete-btn">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No users found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Add Admin Button Click Handler
            $('#addAdminBtn').click(function () {
                Swal.fire({
                    title: 'Add New Admin',
                    html: `
                        <form id="addAdminForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       pattern="[0-9]{11}" title="Please enter exactly 11 digits" required>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add Admin',
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#name').value.trim();
                        const email = Swal.getPopup().querySelector('#email').value.trim();
                        const phone = Swal.getPopup().querySelector('#phone').value.trim();

                        if (!name) {
                            Swal.showValidationMessage('Name is required');
                            return false;
                        }
                        if (!email) {
                            Swal.showValidationMessage('Email is required');
                            return false;
                        }
                        if (!/^\S+@\S+\.\S+$/.test(email)) {
                            Swal.showValidationMessage('Enter a valid email');
                            return false;
                        }
                        if (!phone) {
                            Swal.showValidationMessage('Phone is required');
                            return false;
                        }
                        if (phone.length !== 11) {
                            Swal.showValidationMessage('Phone must be exactly 11 digits');
                            return false;
                        }
                        if (!/^\d+$/.test(phone)) {
                            Swal.showValidationMessage('Phone must contain only numbers');
                            return false;
                        }

                        return { name, email, phone };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Checking email availability...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // First check if email exists via AJAX
                        $.ajax({
                            url: 'check_email.php',
                            type: 'POST',
                            data: { email: result.value.email },
                            success: function(emailResponse) {
                                if (emailResponse.exists) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'This email is already registered'
                                    });
                                } else {
                                    // Proceed with admin creation
                                    $.ajax({
                                        url: window.location.href,
                                        type: 'POST',
                                        data: {
                                            add_admin: true,
                                            name: result.value.name,
                                            email: result.value.email,
                                            phone: result.value.phone
                                        },
                                        success: function () {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Admin Added Successfully',
                                                showConfirmButton: false,
                                                timer: 1500
                                            }).then(() => {
                                                window.location.reload();
                                            });
                                        },
                                        error: function (xhr, status, error) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Failed to add admin: ' + error
                                            });
                                        }
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Could not verify email availability'
                                });
                            }
                        });
                    }
                });
            });

            // Dynamic search
            $("#searchText").on("input", function () {
                var searchText = $(this).val();
                $.ajax({
                    url: "admins_list.php",
                    type: "POST",
                    data: { text: searchText, search: true },
                    success: function (data) {
                        var results = $(data).find("#usersTable").html();
                        $("#usersTable").html(results);
                    }
                });
            });

            // SweetAlert confirmation for delete buttons
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                var deleteUrl = $(this).attr('href');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });

            <?php if (isset($_SESSION['delete_status'])): ?>
                Swal.fire({
                    icon: '<?php echo $_SESSION['delete_status']; ?>',
                    title: '<?php echo $_SESSION['delete_message']; ?>',
                    showConfirmButton: false,
                    timer: 2000
                });
                <?php
                unset($_SESSION['delete_status']);
                unset($_SESSION['delete_message']);
                ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>