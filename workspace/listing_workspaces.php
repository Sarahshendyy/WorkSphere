<?php
include "connection.php";

$successMessage = null;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Use header redirect instead of die()
    exit();
}

$user_id = $_SESSION['user_id'];
$zone_query = "SELECT * FROM zone";
$zone_result = mysqli_query($connect, $zone_query);
$room_type_query = "SELECT * FROM room_types";
$room_type_result = mysqli_query($connect, $room_type_query);
$room_types = [];
while ($type = mysqli_fetch_assoc($room_type_result)) {
    $room_types[] = $type;
}

if (isset($_POST['submit'])) {
    // Use prepared statement for inserting workspace to prevent SQL injection
    $stmt = $connect->prepare("INSERT INTO workspaces (user_id, name, location, description, `price/hr`, zone_id, latitude, longitude, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssdiid", $user_id, $_POST['name'], $_POST['location'], $_POST['description'], $_POST['price_hr'], $_POST['zone_id'], $_POST['latitude'], $_POST['longitude']);

    if (!$stmt->execute()) {
        die("Workspace Insert Error: " . $stmt->error);
    }
    $workspace_id = $stmt->insert_id;
    $stmt->close();

    // Prepare statement for inserting rooms
    $room_stmt = $connect->prepare("INSERT INTO rooms (workspace_id, room_name, seats, type_id, images, `p/hr`) VALUES (?, ?, ?, ?, ?, ?)");

    if (!empty($_POST['rooms']) && is_array($_POST['rooms'])) {
        foreach ($_POST['rooms'] as $index => $room) {
            $room_name = $room['name'];
            $seats = intval($room['seats']);
            $type_id = intval($room['type']);
            $price_hr_room = floatval($room['price_hr']);
            
            $image_paths = [];
            
            // Check if files were uploaded for this room index
            if (isset($_FILES['room_images']['name'][$index])) {
                $target_dir = "img/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true); // Ensure the directory exists
                }

                $file_count = count($_FILES['room_images']['name'][$index]);
                for ($i = 0; $i < $file_count; $i++) {
                    // Check for upload errors
                    if ($_FILES['room_images']['error'][$index][$i] === UPLOAD_ERR_OK) {
                        $image_name = $_FILES['room_images']['name'][$index][$i];
                        $tmp_name = $_FILES['room_images']['tmp_name'][$index][$i];
                        
                        $file_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                        $new_filename = uniqid('room_', true) . '.' . $file_ext;
                        $target_file = $target_dir . $new_filename;

                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Store the relative path for the database
                            $image_paths[] = $target_file; 
                        } else {
                            // Handle file move error
                            error_log("Failed to move uploaded file: " . $image_name);
                        }
                    }
                }
            }
            
            // Implode the array of paths into a single string
            $images_string = implode(",", $image_paths);

            // Bind params and execute for the room
            $room_stmt->bind_param("isiisd", $workspace_id, $room_name, $seats, $type_id, $images_string, $price_hr_room);
            if (!$room_stmt->execute()) {
                die("Room Insert Error for room '$room_name': " . $room_stmt->error);
            }
        }
    }
    $room_stmt->close();

    $successMessage = "Your workspace is waiting for admin approval. You will receive an email once it's approved or rejected.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List a Workspace</title>
    <link rel="stylesheet" href="./css/listing_workspaces.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php if ($successMessage): ?>
  <div class="success-container">
    <div class="success-msg">
    <a href="../indexx.php" class="close-btn"><i class="fa-solid fa-xmark"></i></a>
    <h2 class="success-title">Done!</h2>
      <i class="fa-solid fa-circle-check"></i>
      <p><?= htmlspecialchars($successMessage) ?></p>
    </div>
  </div>
<?php endif; ?>

<div class="container">
    <h2>List Your Workspace</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        
       
        <label for="name">Workspace Name:</label>
        <input type="text" name="name" required>

        <label for="location">Location:</label>
        <input type="text" name="location" required>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>

        <label for="price_hr">Workspace Base Price per Hour (optional):</label>
        <input type="number" step="0.01" name="price_hr" required>

        <label for="zone_id">Select Zone:</label>
        <select name="zone_id" required>
            <option value="">-- Select a Zone --</option>
            <?php mysqli_data_seek($zone_result, 0); // Reset pointer for re-use ?>
            <?php while ($zone = mysqli_fetch_assoc($zone_result)): ?>
                <option value="<?php echo $zone['zone_id']; ?>">
                    <?php echo htmlspecialchars($zone['zone_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
                
        <label for="latitude">Latitude:</label>
        <input type="number" step="any" name="latitude" required>

        <label for="longitude">Longitude:</label>
        <input type="number" step="any" name="longitude" required>

       <small style="display:block; margin-top:5px; color:#4B6382;">
        🧭 Don't know your coordinates? 
        <a href="https://maps.google.com" target="_blank" style="color:#A68868; font-weight:600;">
        Click here to open Google Maps
        </a> — Navigate to your workspace, right-click the location, choose “What’s here?”, and copy the Latitude & Longitude shown.
        </small>



        <h3>Rooms:</h3>
        <div id="rooms">
            <div class="room">
                <label>Room Name:</label>
                <input type="text" name="rooms[0][name]" required>

                <label>Seats:</label>
                <input type="number" name="rooms[0][seats]" required>
                <br><br>

                <label>Type:</label>
                <select name="rooms[0][type]" required>
                    <option value="">-- Select Room Type --</option>
                    <?php foreach ($room_types as $type): ?>
                        <option value="<?php echo $type['type_id']; ?>">
                            <?php echo htmlspecialchars($type['type_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <label>Room Price per Hour:</label>
                <input type="number" step="0.01" name="rooms[0][price_hr]" required>
                
                <label for="file-0" class="file-label">Room Images:</label>
                <div class="file-upload-wrapper">
                  <input type="file" id="file-0" name="room_images[0][]" multiple class="file-input">
                </div>
            </div>
        </div>
        
        <button type="button" onclick="addRoom()">Add Another Room</button>
        <button type="submit" name="submit">Submit Workspace</button>
    </form>
</div>

<script>
let roomCount = 1;

function addRoom() {
    const roomId = `room-${roomCount}`;
    let roomDiv = document.createElement("div");
    roomDiv.classList.add("room");
    roomDiv.setAttribute("id", roomId);

    roomDiv.innerHTML = `
        <a href="#" class="close-btn" onclick="removeRoom('${roomId}'); return false;"><i class="fa-solid fa-xmark"></i></a>
        <h4>Room #${roomCount + 1}</h4>
        <label>Room Name:</label>
        <input type="text" name="rooms[${roomCount}][name]" required>

        <label>Seats:</label>
        <input type="number" name="rooms[${roomCount}][seats]" required>

        <label>Type:</label>
        <select name="rooms[${roomCount}][type]" required>
            <option value="">-- Select Room Type --</option>
            <?php foreach ($room_types as $type): ?>
                <option value="<?php echo $type['type_id']; ?>"><?php echo htmlspecialchars($type['type_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label>Room Price per Hour:</label>
        <input type="number" step="0.01" name="rooms[${roomCount}][price_hr]" required>

        <label for="file-${roomCount}" class="file-label">Room Images:</label>
        <div class="file-upload-wrapper">
            <input type="file" id="file-${roomCount}" class="file-input" name="room_images[${roomCount}][]" multiple>
        </div>
    `;

    document.getElementById("rooms").appendChild(roomDiv);
    roomCount++;
}
function removeRoom(id) {
    const roomElement = document.getElementById(id);
    if (roomElement) {
        roomElement.remove();
    }
    // Note: This does not re-index the rooms. For this form, that is okay.
    // The PHP will receive a non-sequential list of indexes, which is fine.
}

</script>
</body>
</html>