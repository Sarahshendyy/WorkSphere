<?php             
include 'connection.php'; 

$user_id = $_SESSION['user_id'];

// Fetch bookings
$display_query = "SELECT `bookings`.*, `rooms`.`room_name`, `workspaces`.`name`
 FROM `bookings` 
JOIN `rooms` ON `bookings`.`room_id` = `rooms`.`room_id`
JOIN `users` ON `bookings`.`user_id` = `users`.`user_id`
JOIN `workspaces` ON `rooms`.`workspace_id` = `workspaces`.`workspace_id`
WHERE `bookings`.`user_id` = $user_id";

$run = mysqli_query($connect, $display_query);
$count = mysqli_num_rows($run);

if ($count > 0) {
    $data_arr = array();
    foreach ($run as $data_row) {
        $data_arr[] = array(
            'id' => $data_row['booking_id'],
            'title' =>  "Workspace:". $data_row['name']."<br>Room: " . $data_row['room_name'] . "<br>Start: " .
$data_row['start_time'] . "<br>End: " . $data_row['end_time'],

            "start" => $data_row['date'] . "T" . $data_row['start_time'], '<br>',
            "end" => $data_row['date'] . "T" . $data_row['end_time'],
           
            'color' => '#ff5733', // Custom color for bookings
            'textColor' => '#ffffff'
        );
    }
    
    $data = array(
        'status' => true,
        'msg' => 'Successfully retrieved bookings!',
        'data' => $data_arr
    );
} else {
    $data = array(
        'status' => false,
        'msg' => 'No bookings found!',
        'data' => []
    );
}

echo json_encode($data);
?>
