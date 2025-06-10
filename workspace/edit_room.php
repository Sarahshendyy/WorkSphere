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
$type_id = "";
$price_per_hour = "";
$images = "";
$image_array = [];
$selected_amenities = [];
$room_types_query = "SELECT * FROM room_types";
$room_types_result = mysqli_query($connect, $room_types_query);

if ($room_id > 0) {
    $select = "SELECT * FROM rooms WHERE room_id = '$room_id' 
               AND workspace_id IN (SELECT workspace_id FROM workspaces WHERE user_id = '$owner_id')";
    $run_select = mysqli_query($connect, $select);
    $fetch = mysqli_fetch_assoc($run_select);

    if ($fetch) {
        $room_name = $fetch['room_name'];
        $seats = $fetch['seats'];
        $type_id = $fetch['type_id'];
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

if (isset($_POST['delete_image'])) {
    $image_to_delete = $_POST['image_to_delete'];
    $image_array = array_diff($image_array, [$image_to_delete]);

    if (file_exists($image_to_delete)) {
        unlink($image_to_delete);
    }

    $updated_images = implode(',', $image_array);
    $update_images = "UPDATE rooms SET images='$updated_images' WHERE room_id='$room_id'";
    mysqli_query($connect, $update_images);

    header("Location: edit_room.php?id=$room_id");
    exit();
}

if (isset($_POST['update'])) {
    $room_name = mysqli_real_escape_string($connect, $_POST['room_name']);
    $seats = intval($_POST['seats']);
    $type_id = intval($_POST['type_id']);
    $price_per_hour = floatval($_POST['price_per_hour']);
    $amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    if (!empty($_FILES['room_images']['name'][0])) {
        foreach ($_FILES['room_images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['room_images']['name'][$key]);
            $target_file = "img/" . $image_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_array[] = $target_file;
            }
        }
    }

    $updated_images = implode(',', $image_array);

    $update = "UPDATE rooms SET 
               room_name='$room_name', 
               seats='$seats', 
               type_id='$type_id', 
               `p/hr`='$price_per_hour',
               images='$updated_images' 
               WHERE room_id='$room_id'";
    $run_update = mysqli_query($connect, $update);

    if ($run_update) {
        mysqli_query($connect, "DELETE FROM amenities WHERE room_id = '$room_id'");

        foreach ($amenities as $amenity_name) {
            $safe_amenity = mysqli_real_escape_string($connect, $amenity_name);
            mysqli_query($connect, "INSERT INTO amenities (amenity, room_id) VALUES ('$safe_amenity', '$room_id')");
        }

        if (!empty($_POST['new_amenity'])) {
            $new_amenity = mysqli_real_escape_string($connect, $_POST['new_amenity']);
            mysqli_query($connect, "INSERT INTO amenities (amenity, room_id) VALUES ('$new_amenity', '$room_id')");
        }

        header("Location: rooms_table.php");
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <label for="type_id"><h3>Room Type</h3></label>
                <select id="type_id" name="type_id" required>
                    <?php while ($type = mysqli_fetch_assoc($room_types_result)): ?>
                        <option value="<?php echo $type['type_id']; ?>" <?php echo ($type['type_id'] == $type_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['type_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
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
                            <img src="<?php echo $image; ?>" width="100">
                            <span class="delete-image" onclick="showDeletePopup('<?php echo $image; ?>')">
                                <i class="fas fa-times"></i>
                            </span>
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
            <div class="amenities-container">
                <?php mysqli_data_seek($all_amenities_result, 0); ?>
                <?php while ($amenity = mysqli_fetch_assoc($all_amenities_result)): ?>
                <div class="amenity-item">
                    <input type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity['amenity']); ?>"
                    <?php echo in_array($amenity['amenity'], $selected_amenities) ? 'checked' : ''; ?>>
                    <label><?php echo htmlspecialchars($amenity['amenity']); ?></label>
                </div>
                <?php endwhile; ?>
                </div>
            </div>

            <div class="input-group">
                <label><h3>Add New Amenity</h3></label>
                <input type="text" name="new_amenity" placeholder="e.g., Whiteboard">
            </div>



            <button type="submit" name="update">Update Room</button>
            <div class="back-link">
                <a href="rooms_table.php">Cancel</a>
            </div>
        </form>
    </div>
</div>


<div id="deleteShow" class="deletepopup">
    <div class="deletecard">
        <form method="POST">
            <h2>Delete Image?</h2>
            <a class="closebtn" title="cancel" onclick="hideDeletePopup()">
                <i class="fa-solid fa-x" id="closeee"></i>
            </a>
            <input type="hidden" name="image_to_delete" id="image_to_delete">
            <button type="submit" name="delete_image" class="popupconfirm">Confirm</button>
        </form>
    </div>
</div>

<script>
    function showDeletePopup(imagePath) {
        document.getElementById('deleteShow').style.display = 'flex';
        document.getElementById('image_to_delete').value = imagePath;
    }
    
    function hideDeletePopup() {
        document.getElementById('deleteShow').style.display = 'none';
    }
    
    document.getElementById('closeee').addEventListener('click', hideDeletePopup);
</script>
</body>
</html>
