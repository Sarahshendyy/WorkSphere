<?php
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$room_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
$owner_id = $_SESSION['user_id'];
$room_name = "";
$seats = "";
$room_type = "";
$price_per_hour = "";
$images = "";
$image_array = [];
$selected_amenities = [];


if ($room_id > 0) {
    $select = "SELECT * FROM rooms WHERE room_id = '$room_id' 
               AND workspace_id IN (SELECT workspace_id FROM workspaces WHERE user_id = '$owner_id')";
    $run_select = mysqli_query($connect, $select);
    $fetch = mysqli_fetch_assoc($run_select);

    if ($fetch) {
        $room_name = $fetch['room_name'];
        $seats = $fetch['seats'];
        $room_type = $fetch['room_type'];
        $price_per_hour = $fetch['p/hr']; 
        $images = $fetch['images'];
        $image_array = !empty($images) ? explode(',', $images) : [];
    }
    $selected_amenities_query = "SELECT amenity FROM amenities WHERE room_id = '$room_id'";
    $selected_amenities_result = mysqli_query($connect, $selected_amenities_query);
    while ($row = mysqli_fetch_assoc($selected_amenities_result)) {
        $selected_amenities[] = $row['amenity'];
    }
}


$all_amenities_query = "SELECT DISTINCT amenity FROM amenities";
$all_amenities_result = mysqli_query($connect, $all_amenities_query);


if (isset($_POST['update'])) {
    $room_name = mysqli_real_escape_string($connect, $_POST['room_name']);
    $seats = intval($_POST['seats']);
    $room_type = mysqli_real_escape_string($connect, $_POST['room_type']);
    $price_per_hour = floatval($_POST['price_per_hour']);
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    if (!empty($_FILES['room_images']['name'][0])) {
        foreach ($_FILES['room_images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['room_images']['name'][$key]);
            $target_file = "img/" . $image_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_array[] = $image_name;
            }
        }
    }
    $updated_images = implode(',', $image_array);

    $update = "UPDATE rooms SET 
               room_name='$room_name', 
               seats='$seats', 
               room_type='$room_type', 
               `p/hr`='$price_per_hour',
               images='$updated_images' 
               WHERE room_id='$room_id'";
    $run_update = mysqli_query($connect, $update);

    if ($run_update) {
        header("Location: workspaces_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error updating room.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Room</title>
<link rel="stylesheet" type="text/css" href="./css/Edit-room.css">
</head>

<body>
<div class="wrapper">
    <div class="layer"></div>
    <div class="form-wrapper">
        <form method="POST" enctype="multipart/form-data">
            <h2>Edit Room</h2>

            <div class="input-group">
                <label for="room_name"><h3>Room Name</h3></label>
                <input type="text" id="room_name" name="room_name" value="<?php echo htmlspecialchars($room_name); ?>" required>
            </div>

            <div class="input-group">
                <label for="seats"><h3>Seats</h3></label>
                <input type="number" id="seats" name="seats" value="<?php echo $seats; ?>" required>
            </div>

            <div class="input-group">
                <label><h3>Room Type</h3></label>
                <input type="text" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>" required>
            </div>

            <div class="input-group">
                <label for="price_per_hour"><h3>Price Per Hour (EGP)</h3></label>
                <input type="number" id="price_per_hour" step="0.01" name="price_per_hour" value="<?php echo $price_per_hour; ?>" required>
            </div>

            <div class="input-group">
                <label><h3>Existing Images</h3></label>
                <div class="image-preview">
                    <?php foreach ($image_array as $image): ?>
                        <div class="image-container">
                            <img src="img/<?php echo $image; ?>" width="100">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="input-group">
                <label for="room_images"><h3>Add New Images</h3></label>
                <input type="file" id="room_images" name="room_images[]" multiple>
            </div>

            <div class="input-group">
                <label><h3>Existing Amenities</h3></label>
                <?php while ($amenity = mysqli_fetch_assoc($all_amenities_result)): ?>
                    <div class="checkbox-group">
                        <input type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity['amenity']); ?>" 
                            <?php if (in_array($amenity['amenity'], $selected_amenities)) echo "checked"; ?>>
                        <label><?php echo htmlspecialchars($amenity['amenity']); ?></label>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" name="update">Update Room</button>
            <div class="back-link">
                <a href="workspaces_dashboard.php">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="./js/Edit-room.js"></script>
</body>
</html>
