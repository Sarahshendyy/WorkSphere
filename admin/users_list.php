<?php
include "connection.php";

// Handle "Hold" action
if (isset($_GET['action']) && $_GET['action'] == 'hold' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $update_status = "UPDATE `users` SET `action` = 'hold' WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $update_status)) {
        echo "<script>alert('User status updated to Hold.');</script>";
    } else {
        echo "<script>alert('Failed to update user status.');</script>";
    }
}

// Handle "Un hold" action
if (isset($_GET['action']) && $_GET['action'] == 'unhold' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $update_status = "UPDATE `users` SET `action` = 'active' WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $update_status)) {
        echo "<script>alert('User status updated to Active.');</script>";
    } else {
        echo "<script>alert('Failed to update user status.');</script>";
    }
}

// Handle "Delete" action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $delete_user = "DELETE FROM `users` WHERE `user_id` = $user_id";
    if (mysqli_query($connect, $delete_user)) {
        echo "<script>alert('User deleted successfully.');</script>";
    } else {
        echo "<script>alert('Failed to delete user.');</script>";
    }
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
                                                <a href="?action=delete&id=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
        });
    </script>
</body>
</html>