<?php
include "connection.php";

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
        $_SESSION['delete_status'] = 'success';
        $_SESSION['delete_message'] = 'Workspace deleted successfully';
    } else {
        $_SESSION['delete_status'] = 'error';
        $_SESSION['delete_message'] = 'Failed to delete workspace: ' . mysqli_error($connect);
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
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            width: 100%;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .summary-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-2 {
            background-color: #4caf50;
            color: white;
        }
        .status-1 {
            background-color: #ffcc00;
            color: black;
        }
        .status-0 {
            background-color: #ff4d4d;
            color: white;
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <h2><i class="fas fa-building"></i> Workspaces Management</h2>

    <!-- Summary Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="summary-card text-center">
                <h5>Total Workspaces</h5>
                <h3><?php echo mysqli_num_rows($workspaces_result); ?></h3>
                <p class="text-muted">Available spaces</p>
            </div>
        </div>
    </div>

    <!-- Workspaces Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Workspace Name</th>
                        <th>Owner</th>
                        <th>Zone</th>
                        <th>Starting Price</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Reset pointer to beginning in case we need to use the result again
                    mysqli_data_seek($workspaces_result, 0);
                    while ($workspace = mysqli_fetch_assoc($workspaces_result)): 
                        $status_text = '';
                        switch($workspace['Availability']) {
                            case 2: $status_text = 'Active'; break;
                            case 1: $status_text = 'Pending'; break;
                            default: $status_text = 'Unknown';
                        }
                    ?>
                        <tr>
                            <td><?php echo $workspace['workspace_id']; ?></td>
                            <td>
                                <a href="../workspace_details.php?ws_id=<?php echo $workspace['workspace_id']; ?>">
                                    <?php echo htmlspecialchars($workspace['name']); ?>
                                </a>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($workspace['owner_name']); ?></td>
                            <td><?php echo htmlspecialchars($workspace['zone_name']); ?></td>
                            <td><?php echo number_format($workspace['starting_price'], 2); ?> EGP/hr</td>
                            <td>
                                <?php 
                                $rating = $workspace['avg_rating'];
                                if ($rating > 0) {
                                    echo number_format($rating, 1) . ' <i class="fas fa-star" style="color: gold;"></i>';
                                } else {
                                    echo 'No ratings';
                                }
                                ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $workspace['Availability']; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($workspace['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="?action=delete&id=<?php echo $workspace['workspace_id']; ?>" 
                                   class="btn btn-danger btn-sm delete-btn"
                                   onclick="return confirmDelete(event)">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert confirmation for delete
    function confirmDelete(event) {
        event.preventDefault();
        const deleteUrl = event.currentTarget.getAttribute('href');
        
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
    }

    // Show delete status notification if exists
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
</script>

</body>
</html>