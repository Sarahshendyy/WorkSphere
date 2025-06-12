<?php
// include "../mail.php";
include "sidebar.php";

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header("Location: login.php");
    exit();
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $workspace_id = $_GET['id'];
    $delete_workspace = "DELETE FROM `workspaces` WHERE `workspace_id` = $workspace_id";
    if (mysqli_query($connect, $delete_workspace)) {
        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Success!',
            'text' => 'Workspace deleted successfully'
        ];
    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Error',
            'text' => 'Failed to delete workspace: ' . mysqli_error($connect)
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle "Hold Workspace" action
if (isset($_GET['action']) && $_GET['action'] == 'hold' && isset($_GET['id'])) {
    $workspace_id = $_GET['id'];

    // First get workspace and owner details
    $query = "SELECT w.*, u.name AS owner_name, u.email 
              FROM workspaces w 
              JOIN users u ON w.user_id = u.user_id 
              WHERE w.workspace_id = $workspace_id";
    $result = mysqli_query($connect, $query);
    $workspace_data = mysqli_fetch_assoc($result);

    $name = $workspace_data['owner_name'];
    $email = $workspace_data['email'];
    $workspace_name = $workspace_data['name'];

    $update_status = "UPDATE workspaces SET availability = 3 WHERE workspace_id = $workspace_id";
    if (mysqli_query($connect, $update_status)) {
        // Send email notification
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
            <div style='background-color: #071739; padding: 20px; text-align: center; color: #E3C39D;'>
                <h1 style='margin: 0; font-size: 2rem;'>Workspace Status Update: <span style='color: #A68868;'>On Hold</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fff; color: #071739;'>
                <p>Dear <span style='color: #A68868;'>$name</span>,</p>
                <p>We regret to inform you that your workspace <strong>$workspace_name</strong> has been temporarily placed on hold by our administration team.</p>
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>The workspace won't be available for bookings</li>
                    <li>You'll receive further communication about next steps</li>
                </ul>
                <p>If you believe this action was taken in error or would like to appeal this decision, please contact our support team immediately.</p>
                <p style='color: #E3C39D;'>We hope to resolve this matter soon.</p>
                <p>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 10px; text-align: center; color: #fff;'>
                <p>For support or questions, please contact:</p>
                <p>Email: <a href='mailto:admin-support@worksphere50@gmail.com' style='color: #A68868;'>admin-support@worksphere50@gmail.com</a></p>
            </div>
        </body>";

        $mail->setFrom('worksphere50@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Workspace Has Been Placed On Hold';
        $mail->Body = $message;
        $mail->send();

        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'Workspace status updated to Hold'];
    } else {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Failed to update workspace status'];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle "Unhold Workspace" action
if (isset($_GET['action']) && $_GET['action'] == 'unhold' && isset($_GET['id'])) {
    $workspace_id = $_GET['id'];

    // First get workspace and owner details
    $query = "SELECT w.*, u.name AS owner_name, u.email 
    FROM workspaces w 
    JOIN users u ON w.user_id = u.user_id 
    WHERE w.workspace_id = $workspace_id";
    $result = mysqli_query($connect, $query);
    $workspace_data = mysqli_fetch_assoc($result);

    $name = $workspace_data['owner_name'];
    $email = $workspace_data['email'];
    $workspace_name = $workspace_data['name'];

    $update_status = "UPDATE workspaces SET availability = 2 WHERE workspace_id = $workspace_id"; // Assuming 1 means 'active'
    if (mysqli_query($connect, $update_status)) {
        // Send email notification
        $message = "
        <body style='font-family: DM Sans, Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
            <div style='background-color: #071739; padding: 20px; text-align: center; color: #E3C39D;'>
                <h1 style='margin: 0; font-size: 2rem;'>Workspace Status Update: <span style='color: #4B6382;'>Reactivated</span></h1>
            </div>
            <div style='padding: 20px; background-color: #fff; color: #071739;'>
                <p>Dear <span style='color: #A68868;'>$name</span>,</p>
                <p>We're pleased to inform you that your workspace <strong>$workspace_name</strong> has been reactivated and is now available for bookings.</p>
                <p><strong>What this means:</strong></p>
                <ul>
                    <li>The workspace is now available for bookings</li>
                    <li>All restrictions have been lifted</li>
                </ul>
                <p>We appreciate your patience and understanding during this process.</p>
                <p style='color: #E3C39D;'>Your workspace is now active again!</p>
                <p>Best regards,<br>The WorkSphere Admin Team</p>
            </div>
            <div style='background-color: #4B6382; padding: 10px; text-align: center; color: #fff;'>
                <p>For any questions, please contact:</p>
                <p>Email: <a href='mailto:admin-support@worksphere50@gmail.com' style='color: #A68868;'>admin-support@worksphere50@gmail.com</a></p>
            </div>
        </body>";

        $mail->setFrom('worksphere50@gmail.com', 'WorkSphere');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your WorkSphere Workspace Has Been Reactivated';
        $mail->Body = $message;
        $mail->send();

        $_SESSION['swal'] = ['icon' => 'success', 'title' => 'Workspace status updated to Active'];
    } else {
        $_SESSION['swal'] = ['icon' => 'error', 'title' => 'Failed to update workspace status'];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get all workspaces with owner and zone information
$workspaces_query = "SELECT 
    w.workspace_id,
    w.name,
    w.created_at,
    w.Availability,
    z.zone_name, 
    u.name AS owner_name,
    COALESCE(MIN(r.`p/hr`), 0) AS starting_price,
    COALESCE(AVG(rv.rating), 0) AS avg_rating
FROM `workspaces` w
LEFT JOIN `rooms` r ON w.`workspace_id` = r.`workspace_id`
LEFT JOIN `zone` z ON w.`zone_id` = z.`zone_id`
LEFT JOIN `users` u ON w.`user_id` = u.`user_id`
LEFT JOIN `bookings` b ON r.`room_id` = b.`room_id`
LEFT JOIN `reviews` rv ON b.`booking_id` = rv.`booking_id`
GROUP BY w.workspace_id";

$workspaces_result = mysqli_query($connect, $workspaces_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workspaces Management</title>
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

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-2 { background-color: var(--secondary-color); color: white; }
        .status-1 { background-color: var(--accent-light); color: var(--primary-color); }
        .status-0 { background-color: var(--accent-warm); color: white; }

        .table thead th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
        }

        .table tbody tr:hover {
            background-color: var(--light-color);
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
        }

        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
            border-radius: 20px;
            padding: 8px 15px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-warning {
            background-color: var(--accent-light);
            border-color: var(--accent-light);
            color: var(--primary-color);
        }

        .btn-warning:hover {
            background-color: var(--accent-warm);
            border-color: var(--accent-warm);
            color: white;
        }

        .btn-danger {
            background-color: var(--accent-warm);
            border-color: var(--accent-warm);
        }

        .btn-danger:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .search-wrapper {
            margin-bottom: 20px;
        }

        .search-wrapper input {
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid var(--light-color);
        }

        .controls-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* SweetAlert2 Custom Styles */
        .swal2-popup {
            font-family: 'DM Sans', sans-serif !important;
            border-radius: 10px !important;
        }

        .swal2-title {
            color: var(--primary-color) !important;
            font-weight: 600 !important;
        }

        .swal2-html-container {
            color: var(--secondary-color) !important;
        }

        .swal2-confirm {
            background-color: var(--accent-warm) !important;
            color: white !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .swal2-confirm:hover {
            background-color: var(--accent-light) !important;
            color: var(--primary-color) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .swal2-cancel {
            background-color: var(--light-color) !important;
            color: var(--primary-color) !important;
            border: none !important;
            padding: 10px 20px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .swal2-cancel:hover {
            background-color: var(--info-color) !important;
            color: white !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
        }

        .swal2-icon {
            border-width: 3px !important;
        }

        .swal2-icon.swal2-warning {
            border-color: var(--accent-warm) !important;
            color: var(--accent-warm) !important;
        }

        .swal2-icon.swal2-success {
            border-color: var(--secondary-color) !important;
            color: var(--secondary-color) !important;
        }

        .swal2-icon.swal2-error {
            border-color: var(--accent-warm) !important;
            color: var(--accent-warm) !important;
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
            <h2><i class="fas fa-building"></i> Workspaces Management</h2>
            
            <div class="table-container">
                <div class="controls-container">
                    <div class="search-wrapper">
                        <input type="text" id="searchText" class="form-control" placeholder="Search workspaces...">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Zone</th>
                                <th>Starting Price</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($workspaces_result) > 0) {
                                while ($row = mysqli_fetch_assoc($workspaces_result)) {
                                    $status_class = '';
                                    $status_text = '';
                                    
                                    switch ($row['Availability']) {
                                        case 2:
                                            $status_class = 'status-2';
                                            $status_text = 'Active';
                                            break;
                                        case 1:
                                            $status_class = 'status-1';
                                            $status_text = 'Pending';
                                            break;
                                        case 3:
                                            $status_class = 'status-0';
                                            $status_text = 'On Hold';
                                            break;
                                        case 0:
                                            $status_class = 'status-0';
                                            $status_text = 'Inactive';
                                            break;
                                        default:
                                            $status_class = 'status-1';
                                            $status_text = 'Unknown';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['workspace_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['zone_name']); ?></td>
                                        <td><?php echo number_format($row['starting_price'], 2); ?> EGP/hr</td>
                                        <td>
                                            <?php 
                                            $rating = round($row['avg_rating'], 1);
                                            echo $rating > 0 ? $rating . ' <i class="fas fa-star text-warning"></i>' : 'No ratings';
                                            ?>
                                        </td>
                                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                        <td class="action-buttons">
                                            <?php if ($row['Availability'] == 1) { ?>
                                                <a href="workspace_approval.php?ws_id=<?php echo $row['workspace_id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php } else { ?>
                                                <a href="../workspace_details.php?ws_id=<?php echo $row['workspace_id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            <?php } ?>
                                            <?php if ($row['Availability'] == 2) { ?>
                                                <a href="?action=hold&id=<?php echo $row['workspace_id']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-pause"></i> Hold
                                                </a>
                                            <?php } else if ($row['Availability'] == 3) { ?>
                                                <a href="?action=unhold&id=<?php echo $row['workspace_id']; ?>" class="btn btn-success btn-sm">
                                                    <i class="fas fa-play"></i> Activate
                                                </a>
                                            <?php } ?>
                                            <a href="?action=delete&id=<?php echo $row['workspace_id']; ?>" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="8" class="text-center">No workspaces found</td></tr>';
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
                $(document).ready(function() {
            // Show SweetAlert notifications if any
            <?php if (isset($_SESSION['swal'])) { ?>
                Swal.fire({
                    icon: '<?php echo $_SESSION['swal']['icon']; ?>',
                    title: '<?php echo $_SESSION['swal']['title']; ?>',
                    text: '<?php echo isset($_SESSION['swal']['text']) ? $_SESSION['swal']['text'] : ''; ?>',
                    showConfirmButton: false,
                    timer: 1500
                });
                <?php unset($_SESSION['swal']); ?>
            <?php } ?>

            // Search functionality
            $("#searchText").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Delete confirmation with enhanced feedback
            $(document).on('click', '.btn-danger', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).attr('href');
                
                Swal.fire({
                    title: 'Delete Workspace?',
                    text: "This action cannot be undone. All associated data will be permanently deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--accent-warm)',
                    cancelButtonColor: 'var(--secondary-color)',
                    confirmButtonText: 'Yes, delete it',
                    background: '#fff',
                    customClass: {
                        title: 'text-primary',
                        content: 'text-secondary',
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait while we delete the workspace',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Perform the delete
                        window.location.href = deleteUrl;
                    }
                });
            });


            // Hold confirmation
            $(document).on('click', '.btn-warning', function(e) {
                e.preventDefault();
                const holdUrl = $(this).attr('href');
                
                Swal.fire({
                    title: 'Place Workspace on Hold?',
                    text: "This workspace will be temporarily unavailable for bookings.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--accent-warm)',
                    cancelButtonColor: 'var(--secondary-color)',
                    confirmButtonText: 'Yes, place on hold',
                    background: '#fff',
                    customClass: {
                        title: 'text-primary',
                        content: 'text-secondary',
                        confirmButton: 'btn btn-warning',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = holdUrl;
                    }
                });
            });

            // Activate confirmation
            $(document).on('click', '.btn-success', function(e) {
                e.preventDefault();
                const activateUrl = $(this).attr('href');
                
                Swal.fire({
                    title: 'Reactivate Workspace?',
                    text: "This workspace will become available for bookings again.",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--secondary-color)',
                    cancelButtonColor: 'var(--info-color)',
                    confirmButtonText: 'Yes, reactivate',
                    background: '#fff',
                    customClass: {
                        title: 'text-primary',
                        content: 'text-secondary',
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = activateUrl;
                    }
                });
            });

            // // Delete confirmation
            // $(document).on('click', '.btn-danger', function(e) {
            //     e.preventDefault();
            //     const deleteUrl = $(this).attr('href');
                
            //     Swal.fire({
            //         title: 'Delete Workspace?',
            //         text: "This action cannot be undone. All associated data will be permanently deleted.",
            //         icon: 'error',
            //         showCancelButton: true,
            //         confirmButtonColor: 'var(--accent-warm)',
            //         cancelButtonColor: 'var(--secondary-color)',
            //         confirmButtonText: 'Yes, delete it',
            //         background: '#fff',
            //         customClass: {
            //             title: 'text-primary',
            //             content: 'text-secondary',
            //             confirmButton: 'btn btn-danger',
            //             cancelButton: 'btn btn-secondary'
            //         }
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             window.location.href = deleteUrl;
            //         }
            //     });
            // });
        });
    </script>
</body>

</html>
