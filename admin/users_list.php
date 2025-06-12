<?php

// include '../mail.php';
include "sidebar.php";



// Handle "Delete" action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // First get user details before deleting
    $user_query = "SELECT `name`, `email` FROM `users` WHERE `user_id` = $user_id";
    $user_result = mysqli_query($connect, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $name = $user_data['name'];
    $email = $user_data['email'];
    
    $delete_user = "DELETE FROM `users` WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $delete_user)) {
        // Send email notification
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
            <div style='background-color: #071739; padding: 20px; text-align: center; color: #E3C39D;'>
                <h1 style='margin: 0; font-size: 2rem;'>Account Status Update: <span style='color: #A68868;'>Deleted</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fff; color: #071739;'>
                <p>Dear <span style='color: #A68868;'>$name</span>,</p>
                <p>This is to formally notify you that your WorkSphere account has been permanently deleted from our system.</p>
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>All your account data has been removed from our platform</li>
                </ul>
                <p>If you believe this action was taken in error, please contact our support team immediately as we may be able to restore your account within a limited time frame.</p>
                <p>We appreciate the time you spent with WorkSphere.</p>
                <p>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 10px; text-align: center; color: #fff;'>
                <p>For urgent matters, please contact:</p>
                <p>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #A68868;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Deleted';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'User Deleted Successfully',
            'text' => 'The user account has been permanently removed from the system.'
        ];
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Deletion Failed',
            'text' => 'Failed to delete user: ' . mysqli_error($connect)
        ];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle "Hold" action
if (isset($_GET['action']) && $_GET['action'] == 'hold' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // First get user details
    $user_query = "SELECT `name`, `email` FROM `users` WHERE `user_id` = $user_id";
    $user_result = mysqli_query($connect, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $name = $user_data['name'];
    $email = $user_data['email'];
    
    $update_status = "UPDATE `users` SET `action` = 'hold' WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $update_status)) {
        // Send email notification
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
            <div style='background-color: #071739; padding: 20px; text-align: center; color: #E3C39D;'>
                <h1 style='margin: 0; font-size: 2rem;'>Account Status Update: <span style='color: #A68868;'>On Hold</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fff; color: #071739;'>
                <p>Dear <span style='color: #A68868;'>$name</span>,</p>
                <p>We regret to inform you that your WorkSphere account has been temporarily placed on hold by our administration team.</p>
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>You won't be able to access certain platform features</li>
                    <li>You'll receive further communication about next steps</li>
                </ul>
                <p>If you believe this action was taken in error or would like to appeal this decision, please contact our support team immediately.</p>
                <p style='color: #E3C39D;'>We hope to resolve this matter soon.</p>
                <p>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 10px; text-align: center; color: #fff;'>
                <p>For support or questions, please contact:</p>
                <p>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #A68868;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Placed On Hold';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Account Placed on Hold',
            'text' => 'The user account has been successfully placed on hold.'
        ];
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Hold Failed',
            'text' => 'Failed to place account on hold: ' . mysqli_error($connect)
        ];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle "Unhold" action
if (isset($_GET['action']) && $_GET['action'] == 'unhold' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // First get user details
    $user_query = "SELECT `name`, `email` FROM `users` WHERE `user_id` = $user_id";
    $user_result = mysqli_query($connect, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    $name = $user_data['name'];
    $email = $user_data['email'];
    
    $update_status = "UPDATE `users` SET `action` = 'active' WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $update_status)) {
        // Send email notification
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
            <div style='background-color: #071739; padding: 20px; text-align: center; color: #E3C39D;'>
                <h1 style='margin: 0; font-size: 2rem;'>Account Status Update: <span style='color: #A68868;'>Reactivated</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fff; color: #071739;'>
                <p>Dear <span style='color: #A68868;'>$name</span>,</p>
                <p>We're pleased to inform you that your WorkSphere account has been reactivated and all restrictions have been lifted.</p>
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>Full access to all platform features has been restored</li>
                    <li>You can continue using WorkSphere as normal</li>
                </ul>
                <p>We appreciate your patience and understanding during this process.</p>
                <p style='color: #E3C39D;'>Welcome back to WorkSphere!</p>
                <p>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 10px; text-align: center; color: #fff;'>
                <p>For any questions, please contact:</p>
                <p>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #A68868;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Reactivated';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Account Reactivated',
            'text' => 'The user account has been successfully reactivated.'
        ];
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Reactivation Failed',
            'text' => 'Failed to reactivate account: ' . mysqli_error($connect)
        ];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Check if it's a search or filter request
$filter_role = isset($_POST['filter_role']) ? $_POST['filter_role'] : '';
$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
$order_by = '';
if ($sort === 'bookings_asc') {
    $order_by = ' ORDER BY total_bookings ASC';
} else {
    $order_by = ' ORDER BY total_bookings DESC';
}

if (isset($_POST['search']) && !empty($_POST['text'])) {
    $text = mysqli_real_escape_string($connect, $_POST['text']);
    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`phone`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4' AND (`users`.`name` LIKE '%$text%' OR `users`.`email` LIKE '%$text%')
    $order_by";
} elseif (!empty($filter_role)) {
    // Filter by role
    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`phone`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4' AND `role`.`role_name` = '$filter_role'
    $order_by";
} else {
    // Default query to fetch all users
    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `users`.`phone`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4'
    $order_by";
}

$run_select = mysqli_query($connect, $select_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
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
            transition: margin-left 0.3s ease, padding 0.3s ease;
            min-height: 100vh;
            width: 100%;
            box-sizing: border-box;
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
            padding: 8px 6px;
            font-size: 0.97rem;
            min-width: 90px;
            border: none;
        }
        .table tbody td {
            padding: 8px 6px;
            font-size: 0.97rem;
            min-width: 90px;
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
        .status-hold { background-color: var(--accent-warm); color: white; }
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
        @media (max-width: 991px) {
            .main-content,
            .main-content.expanded {
                margin-left: 0 !important;
                padding: 10px;
            }
        }
        .container-fluid {
            width: 100%;
            padding: 0;
        }
        .table-responsive {
            overflow-x: auto;
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
        .action-buttons .btn-warning {
            background-color: var(--accent-warm) !important;
            color: white !important;
            border: none !important;
        }
        .action-buttons .btn-warning:hover {
            background-color: var(--accent-light) !important;
            color: var(--primary-color) !important;
        }
        .action-buttons .btn-success {
            background-color: var(--secondary-color) !important;
            color: white !important;
            border: none !important;
        }
        .action-buttons .btn-success:hover {
            background-color: var(--info-color) !important;
            color: var(--primary-color) !important;
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
            <h2><i class="fas fa-users"></i> Users List</h2>
            <div class="controls-container">
                <div class="search-wrapper">
                    <input type="text" id="searchText" class="form-control" placeholder="Search by name or email...">
                </div>
                <div class="sort-dropdown">
                    <select class="form-select" id="sortSelect">
                        <option value="bookings_desc">Bookings (High to Low)</option>
                        <option value="bookings_asc">Bookings (Low to High)</option>
                    </select>
                </div>
            </div>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bookings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTable">
                            <?php
                            $counter = 1;
                            if (mysqli_num_rows($run_select) > 0) {
                                foreach ($run_select as $row) {
                                    $status_class = $row['action'] == 'hold' ? 'status-badge status-hold' : 'status-badge status-active';
                                    echo '<tr>';
                                    echo '<td>' . $counter++ . '</td>';
                                    echo '<td><a href="profile.php?user_id=' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['name']) . '</a></td>';
                                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['role_name']) . '</td>';
                                    echo '<td>' . (($row['role_id'] == '1' || $row['role_id'] == '2') ? $row['total_bookings'] : '-') . '</td>';
                                    echo '<td><span class="' . $status_class . '">' . htmlspecialchars($row['action']) . '</span></td>';
                                    echo '<td>';
                                    echo '<div class="action-buttons">';
                                    if ($row['action'] == 'hold') {
                                        echo '<a href="?action=unhold&id=' . htmlspecialchars($row['user_id']) . '" class="btn btn-success btn-sm"><i class="fas fa-play"></i> Unhold</a> ';
                                    } else {
                                        echo '<a href="?action=hold&id=' . htmlspecialchars($row['user_id']) . '" class="btn btn-warning btn-sm"><i class="fas fa-pause"></i> Hold</a> ';
                                    }
                                    echo '<a href="?action=delete&id=' . htmlspecialchars($row['user_id']) . '" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">No users found</td></tr>';
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
            // Show SweetAlert notifications if any
            <?php if (isset($_SESSION['swal'])) { ?>
                Swal.fire({
                    icon: '<?php echo $_SESSION['swal']['icon']; ?>',
                    title: '<?php echo $_SESSION['swal']['title']; ?>',
                    text: '<?php echo isset($_SESSION['swal']['text']) ? $_SESSION['swal']['text'] : ''; ?>',
                    showConfirmButton: false,
                    timer: 2000,
                    background: '#fff',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        content: 'swal2-html-container'
                    }
                });
                <?php unset($_SESSION['swal']); ?>
            <?php } ?>

            // Search and sort functionality
            function fetchUsers() {
                var searchText = $("#searchText").val();
                var sortValue = $("#sortSelect").val();
                $.ajax({
                    url: "users_list.php",
                    type: "POST",
                    data: { search: true, text: searchText, sort: sortValue },
                    success: function (data) {
                        var results = $(data).find("#usersTable").html();
                        $("#usersTable").html(results);
                    }
                });
            }
            $("#searchText").on("input", fetchUsers);
            $("#sortSelect").on("change", fetchUsers);

            // Enhanced SweetAlert2 confirmation for delete buttons
            $(document).on('click', '.btn-danger', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).attr('href');
                Swal.fire({
                    title: 'Delete User Account?',
                    text: "This action will permanently delete the user and all associated data. This cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-warm').trim() || '#A68868',
                    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--light-color').trim() || '#CDD5DB',
                    confirmButtonText: 'Yes, delete permanently',
                    cancelButtonText: 'Cancel',
                    background: '#fff',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        content: 'swal2-html-container',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting User...',
                            html: 'Please wait while we process your request',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: '#fff',
                            customClass: {
                                popup: 'swal2-popup',
                                title: 'swal2-title',
                                content: 'swal2-html-container'
                            }
                        });
                        window.location.href = deleteUrl;
                    }
                });
            });

            // Enhanced SweetAlert2 confirmation for hold/unhold buttons
            $(document).on('click', '.btn-success, .btn-warning', function(e) {
                e.preventDefault();
                var actionUrl = $(this).attr('href');
                var isUnhold = $(this).hasClass('btn-success');
                var actionText = isUnhold ? 'activate' : 'place on hold';
                var actionTitle = isUnhold ? 'Reactivate User Account' : 'Place User Account on Hold';
                var actionDescription = isUnhold ? 
                    'This will restore full access to the user account.' : 
                    'This will temporarily restrict access to the user account.';
                
                Swal.fire({
                    title: actionTitle,
                    text: actionDescription,
                    icon: isUnhold ? 'success' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isUnhold ? 
                        getComputedStyle(document.documentElement).getPropertyValue('--secondary-color').trim() || '#4B6382' : 
                        getComputedStyle(document.documentElement).getPropertyValue('--accent-warm').trim() || '#A68868',
                    cancelButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--light-color').trim() || '#CDD5DB',
                    confirmButtonText: isUnhold ? 'Yes, reactivate' : 'Yes, place on hold',
                    cancelButtonText: 'Cancel',
                    background: '#fff',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        content: 'swal2-html-container',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: isUnhold ? 'Reactivating Account...' : 'Placing Account on Hold...',
                            html: 'Please wait while we process your request',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: '#fff',
                            customClass: {
                                popup: 'swal2-popup',
                                title: 'swal2-title',
                                content: 'swal2-html-container'
                            }
                        });
                        window.location.href = actionUrl;
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
        .sort-dropdown select {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid var(--light-color);
            background-color: white;
            color: var(--primary-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .sort-dropdown select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 2px rgba(75, 99, 130, 0.1);
        }
        .controls-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        @media (max-width: 768px) {
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
    </style>
</body>
</html>
