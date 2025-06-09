<?php
// include "connection.php";

include '../mail.php';
include "sidebar.php";

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
    <title>Admins List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #071739;
            --secondary-color: #4B6382;
            --info-color: #A4B5C4;
            --light-color: #CDD5DB;
            --accent-warm: #A68868;
            --accent-light: #E3C39D;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'DM Sans', sans-serif;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        .main-content.expanded {
            margin-left: 70px;
        }
        .table-container {
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table-container h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
        }
        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 12px;
            border: none;
        }
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--light-color);
        }
        .table tbody tr:hover {
            background-color: var(--light-color);
        }
        .table a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        .table a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            min-width: 100px;
        }
        .status-active { background-color: var(--secondary-color); color: white; }
        .controls-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            .controls-container {
                flex-direction: column;
                align-items: stretch;
            }
            .table-container {
                padding: 10px;
            }
        }
        .action-buttons a.btn {
            border-radius: 20px !important;
            padding: 6px 18px !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            margin-right: 6px;
            transition: all 0.2s;
            box-shadow: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .action-buttons .btn-danger {
            background-color: #d9534f !important;
            color: white !important;
            border: none !important;
        }
        .action-buttons .btn-danger:hover {
            background-color: #b52a1d !important;
            color: #fff !important;
        }
        .search-wrapper input {
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid var(--light-color);
            width: 100%;
            max-width: 300px;
            transition: all 0.3s ease;
        }
        .search-wrapper input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(75, 99, 130, 0.1);
        }
        #addAdminBtn {
            border-radius: 20px;
            padding: 10px 28px;
            font-weight: 600;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            transition: all 0.2s;
            box-shadow: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        #addAdminBtn:hover, #addAdminBtn:focus {
            background-color: var(--secondary-color);
            color: #fff;
        }
         .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: var(--primary-color);
            padding: 20px;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .toggle-sidebar {
            background: none;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .toggle-sidebar:hover {
            color: var(--accent-light);
        }

        .sidebar.collapsed .sidebar-header h3,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .toggle-sidebar {
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1002;
                background-color: var(--primary-color);
                border-radius: 50%;
                width: 40px;
                height: 40px;
            }

            .toggle-sidebar.collapsed {
                left: 20px;
            }

            .controls-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper input {
                max-width: 100%;
            }

            .sort-dropdown select {
                width: 100%;
            }
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin-top: 30px;
        }

        .nav-item {
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .nav-link.active {
            background-color: var(--accent-warm);
            color: white;
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <h2><i class="fas fa-user-shield"></i> Admins List</h2>
                <div class="controls-container">
                    <div class="search-wrapper">
                        <input type="text" id="searchText" class="form-control" placeholder="Search by name, email or phone...">
                </div>
                <button id="addAdminBtn" class="btn btn-primary" style="border-radius: 20px; padding: 8px 24px; font-weight: 500;"><i class="fas fa-user-plus"></i> Add Admin</button>
            </div>
                <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminsTable">
                            <?php
                            $counter = 1;
                            if (mysqli_num_rows($run_select) > 0) {
                                foreach ($run_select as $row) {
                                    $status_class = 'status-badge status-active';
                                    echo '<tr>';
                                    echo '<td>' . $counter++ . '</td>';
                                    echo '<td><a href="../profile.php?user_id=' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['name']) . '</a></td>';
                                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['phone']) . '</td>';
                                    echo '<td><span class="' . $status_class . '">Active</span></td>';
                                    echo '<td>';
                                    echo '<div class="action-buttons">';
                                    echo '<a href="?action=delete&id=' . htmlspecialchars($row['user_id']) . '" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center">No admins found</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/sidebar.js"></script>
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
                                <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{11}" title="Please enter exactly 11 digits" required>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add Admin',
                    cancelButtonText: 'Cancel',
                    focusConfirm: false,
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    },
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
                        var results = $(data).find("#adminsTable").html();
                        $("#adminsTable").html(results);
                    }
                });
            });

            // SweetAlert2 confirmation for delete buttons
            $(document).on('click', '.btn-danger', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).attr('href');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This admin will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-warm').trim() || '#A68868',
                    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--light-color').trim() || '#CDD5DB',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });
        });
    </script>
    <style>
        .swal2-popup {
            font-family: 'DM Sans', sans-serif;
            border-radius: 10px;
        }
        .swal2-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        .swal2-html-container {
            color: var(--secondary-color);
        }
        .swal2-confirm {
            background-color: var(--accent-warm) !important;
            color: white !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 20px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm:hover {
            background-color: var(--accent-light) !important;
            color: var(--primary-color) !important;
        }
        .swal2-cancel {
            background-color: var(--light-color) !important;
            color: var(--primary-color) !important;
            border: none !important;
            border-radius: 5px !important;
            padding: 10px 20px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        .swal2-cancel:hover {
            background-color: var(--info-color) !important;
            color: white !important;
        }
    </style>
</body>
</html>