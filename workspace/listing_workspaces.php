<?php
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    die("Session not set! <script>window.location.href='login.php';</script>");
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
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $location = mysqli_real_escape_string($connect, $_POST['location']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $price_hr = floatval($_POST['price_hr']);
    $zone_id = intval($_POST['zone_id']);
    $insert_workspace = "INSERT INTO workspaces (user_id, name, location, description, `price/hr`, zone_id, created_at) 
                         VALUES ('$user_id', '$name', '$location', '$description', '$price_hr', '$zone_id', NOW())";
    $insertQry = mysqli_query($connect, $insert_workspace);

    if (!$insertQry) {
        die("Workspace Insert Error: " . mysqli_error($connect));
    }

    
    $workspace_id = mysqli_insert_id($connect);

    
    if (!empty($_POST['rooms']) && is_array($_POST['rooms'])) {
        foreach ($_POST['rooms'] as $index => $room) {
            $room_name = mysqli_real_escape_string($connect, $room['name']);
            $seats = intval($room['seats']);
            $type_id = intval($room['type']); // Changed to type_id
            $room_status = mysqli_real_escape_string($connect, $room['status']);
            $price_hr = floatval($room['price_hr']);

            
            $image_paths = [];
            if (!empty($_FILES['room_images']['name'][$index])) {
                $target_dir = "img/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                
                foreach ($_FILES['room_images']['name'][$index] as $key => $image_name) {
                    $file_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                    $new_filename = uniqid() . '.' . $file_ext;
                    $target_file = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['room_images']['tmp_name'][$index][$key], $target_file)) {
                        $image_paths[] = $target_file;
                    } else {
                        die("Failed to upload image: " . $_FILES['room_images']['name'][$index][$key]);
                    }
                }
            }

            $images = implode(",", $image_paths);

            
            $insert_room = "INSERT INTO rooms (workspace_id, room_name, seats, type_id, room_status, images, `p/hr`) 
                            VALUES ('$workspace_id', '$room_name', '$seats', '$type_id', '$room_status', '$images', '$price_hr')";
            $insertRoomQry = mysqli_query($connect, $insert_room);

            if (!$insertRoomQry) {
                die("Room Insert Error: " . mysqli_error($connect));
            }
        }
    }

    echo "<script>alert('Workspace listed successfully!'); window.location.href='indexx.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List a Workspace</title>
    <link rel="stylesheet" href="./css/listing_workspaces.css">
</head>
<body>

<div class="container">
    <h2>List Your Workspace</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        
       
        <label for="name">Workspace Name:</label>
        <input type="text" name="name" required>

        <label for="location">Location:</label>
        <input type="text" name="location" required>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>

        <label for="price_hr">Price per Hour:</label>
        <input type="number" step="0.01" name="price_hr" required>

        <label for="zone_id">Select Zone:</label>
        <select name="zone_id" required>
            <option value="">-- Select a Zone --</option>
            <?php while ($zone = mysqli_fetch_assoc($zone_result)): ?>
                <option value="<?php echo $zone['zone_id']; ?>">
                    <?php echo $zone['zone_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    

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
                            <?php echo $type['type_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <label>Status:</label>
                <input type="text" name="rooms[0][status]" required>

                <label>Price per Hour:</label>
                <input type="number" step="0.01" name="rooms[0][price_hr]" required>

                <label>Room Images:</label>
                <label class="custom-file-label">
                Choose Files
                <input type="file" name="room_images[0][]" multiple>
                    </label>
                </div>
        </div>
        
        <button type="button" onclick="addRoom()">Add Another Room</button>
        <button type="submit" name="submit">Submit</button>
    </form>
</div>

<script>
let roomCount = 1;

function addRoom() {
    let roomDiv = document.createElement("div");
    roomDiv.classList.add("room");

    roomDiv.innerHTML = `
        <label>Room Name:</label>
        <input type="text" name="rooms[${roomCount}][name]" required>

        <label>Seats:</label>
        <input type="number" name="rooms[${roomCount}][seats]" required>
        <br><br>

        <label>Type:</label>
        <select name="rooms[${roomCount}][type]" required>
            <option value="">-- Select Room Type --</option>
            <?php foreach ($room_types as $type): ?>
                <option value="<?php echo $type['type_id']; ?>">
                    <?php echo $type['type_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Status:</label>
        <input type="text" name="rooms[${roomCount}][status]" required>

        <label>Price per Hour:</label>
        <input type="number" step="0.01" name="rooms[${roomCount}][price_hr]" required>

        <label>Room Images:</label>
        <label class="custom-file-label">
            Choose Files
            <input type="file" name="room_images[${roomCount}][]" multiple>
        </label>
    `;

    document.getElementById("rooms").appendChild(roomDiv);
    roomCount++;
}

</script>

</body>
</html>