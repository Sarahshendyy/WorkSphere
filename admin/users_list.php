<?php

include '../mail.php';

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
        <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
            <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                <h1>Account Status Update: <span style='color: #fda521;'>On Hold</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$name</span>,</p>
                <p style='color: #00000a;'>We regret to inform you that your WorkSphere account has been temporarily placed on hold by our administration team.</p>
                
                <p style='color: #00000a;'><strong>What this means:</strong></p>
                <ul>
                    <li style='color: #00000a'>You won't be able to access certain platform features</li>
                    <li style='color: #00000a'>You'll receive further communication about next steps</li>
                </ul>
                
                <p style='color: #00000a;'>If you believe this action was taken in error or would like to appeal this decision, please contact our support team immediately.</p>
                
                <p style='color: #fda521;'>We hope to resolve this matter soon.</p>
                <p style='color: #00000a;'>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                <p style='color: #fffffa;'>For support or questions, please contact:</p>
                <p style='color: #fffffa;'>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #fda521;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Placed On Hold';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'User status updated to Hold'];
    } else {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Failed to update user status'];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle "Un hold" action
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
        <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
            <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                <h1>Account Status Update: <span style='color: #fda521;'>Reactivated</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$name</span>,</p>
                <p style='color: #00000a;'>We're pleased to inform you that your WorkSphere account has been reactivated and all restrictions have been lifted.</p>
                
                <p style='color: #00000a;'><strong>What this means:</strong></p>
                <ul>
                    <li style='color: #00000a'>Full access to all platform features has been restored</li>
                    <li style='color: #00000a'>You can continue using WorkSphere as normal</li>
                </ul>
                
                <p style='color: #00000a;'>We appreciate your patience and understanding during this process.</p>
                
                <p style='color: #fda521;'>Welcome back to WorkSphere!</p>
                <p style='color: #00000a;'>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                <p style='color: #fffffa;'>For any questions, please contact:</p>
                <p style='color: #fffffa;'>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #fda521;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Reactivated';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'User status updated to Active'];
    } else {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Failed to update user status'];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

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
        <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
            <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                <h1>Account Status Update: <span style='color: #fda521;'>Deleted</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$name</span>,</p>
                <p style='color: #00000a;'>This is to formally notify you that your WorkSphere account has been permanently deleted from our system.</p>
                
                <p style='color: #00000a;'><strong>What this means:</strong></p>
                <ul>
                    <li style='color: #00000a'>All your account data has been removed from our platform</li>
                   
                </ul>
                
                <p style='color: #00000a;'>If you believe this action was taken in error, please contact our support team immediately as we may be able to restore your account within a limited time frame.</p>
                
                <p style='color: #00000a;'>We appreciate the time you spent with WorkSphere.</p>
                <p style='color: #00000a;'>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                <p style='color: #fffffa;'>For urgent matters, please contact:</p>
                <p style='color: #fffffa;'>Email: <a href='mailto:admin-support@worksphere04@gmail.com' style='color: #fda521;'>admin-support@worksphere04@gmail.com</a></p>
            </div>
        </body>";
        
        $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Account Has Been Deleted';
        $mail->Body = $message;
        $mail->send();
        
        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'User deleted successfully'];
    } else {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Failed to delete user'];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
// Check if it's a search or filter request
$filter_role = isset($_POST['filter_role']) ? $_POST['filter_role'] : '';
//the search 

if (isset($_POST['search']) && !empty($_POST['text'])) {
    $text = mysqli_real_escape_string($connect, $_POST['text']);

    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4' AND (`users`.`name` LIKE '%$text%' OR `users`.`email` LIKE '%$text%')";

} elseif (!empty($filter_role)) {
    // Filter by role

    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4' AND `role`.`role_name` = '$filter_role'";

} else {
    // Default query to fetch all users
    $select_users = "SELECT `users`.`user_id`, `users`.`name`, `users`.`email`, `role`.`role_id`,`role`.`role_name`, 
           (SELECT COUNT(*) FROM `bookings` WHERE `bookings`.`user_id` = `users`.`user_id`) AS total_bookings,
           `users`.`action`
    FROM `users`
    JOIN `role` ON `users`.`role_id` = `role`.`role_id`
    WHERE `users`.`role_id` != '4'";
}

$run_select = mysqli_query($connect, $select_users);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <!-- Link Bootstrap (keep it for grid and base styles) -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Link Your Custom CSS -->
    <link rel="stylesheet" href="./css/users-list.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title">Users List</h1>

                <!-- Controls Container -->
                <div class="controls-container">
                    <div class="search-wrapper">
                         <input type="text" id="searchText" class="form-control" placeholder="Search by name or email...">
                    </div>
                   <div class="filter-wrapper">
                        <select id="filterRole" class="form-select"> 
                            <option value="">All Roles</option>
                            <option value="Employee">Employee</option>
                            <option value="Company">Company</option>
                            <option value="Workspace-owner">Workspace-owner</option>
                        </select>
                    </div>
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
                                    $status_class = $row['action'] == 'hold' ? 'status-hold' : 'status-active';
                            ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td>
                                            <a href="../profile.php?user_id=<?php echo $row['user_id']; ?>">
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['role_name']); ?></td>
                                        <td>
                                            <?php
                                            // Display total bookings only for users with role_id 1 or 2
                                            if ($row['role_id'] == '1' || $row['role_id'] == '2') {
                                                echo $row['total_bookings'];
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                         <td><span class="status <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['action']); ?></span></td>
                                        <td>
                                             <div class="action-buttons"> <?php // Wrapper for buttons ?>
                                                <?php if ($row['action'] == 'hold') { ?>
                                                    <a href="?action=unhold&id=<?php echo $row['user_id']; ?>" class="btn btn-success btn-sm">Unhold</a>
                                                <?php } else { ?>
                                                    <a href="?action=hold&id=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-sm">Hold</a>
                                                <?php } ?>
                                                <a href="?action=delete&id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm delete-btn">Delete</a>
                                             </div>
                                        </td>
                                    </tr>
                            <?php
                                }
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
            // Show SweetAlert notifications from PHP
            <?php if (isset($_SESSION['swal'])): ?>
                Swal.fire({
                    icon: '<?php echo $_SESSION['swal']['icon']; ?>',
                    title: '<?php echo $_SESSION['swal']['title']; ?>',
                    showConfirmButton: false,
                    timer: 2000
                });
                <?php unset($_SESSION['swal']); ?>
            <?php endif; ?>

            // Dynamic search
            $("#searchText").on("input", function () {
                var searchText = $(this).val();
                var filterRole = $("#filterRole").val();
                $.ajax({
                    url: "users_list.php",
                    type: "POST",
                    data: { text: searchText, search: true, filter_role: filterRole },
                    success: function (data) {
                        var results = $(data).find("#usersTable").html();
                        $("#usersTable").html(results);
                    }
                });
            });

            // Filter by role
            $("#filterRole").on("change", function () {
                var filterRole = $(this).val();
                var searchText = $("#searchText").val();
                $.ajax({
                    url: "users_list.php",
                    type: "POST",
                    data: { filter_role: filterRole, search: true, text: searchText },
                    success: function (data) {
                        var results = $(data).find("#usersTable").html();
                        $("#usersTable").html(results);
                    }
                });
            });

            // SweetAlert confirmation for delete buttons
            $(document).on('click', '.btn-danger', function(e) {
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

            // SweetAlert confirmation for hold/unhold buttons
            $(document).on('click', '.btn-success, .btn-warning', function(e) {
                e.preventDefault();
                var actionUrl = $(this).attr('href');
                var actionText = $(this).hasClass('btn-success') ? 'unhold' : 'hold';
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Are you sure you want to ${actionText} this user?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `Yes, ${actionText}`
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = actionUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>