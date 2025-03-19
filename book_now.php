<?php


include("connection.php"); // Ensure this file defines $connect as a valid connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Get JSON data from the frontend
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Log the received data
    error_log('Received Data: ' . print_r($data, true));

    // Extract data
    $userId = $_SESSION['user_id'] ?? null;
    $roomId = mysqli_real_escape_string($connect, $data['room_id']); // Get from JSON, not GET
    $date = $data['date'] ?? '';
    $startTime = $data['start_time'] ?? '';
    $endTime = $data['end_time'] ?? '';
    $numPeople = $data['num_people'] ?? '';

    // Validate inputs
    if (empty($userId) || empty($roomId) || empty($date) || empty($startTime) || empty($endTime) || empty($numPeople)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Check if the time slot is already booked
    $query = "SELECT * FROM bookings WHERE room_id = ? AND date = ? AND (
              (start_time <= ? AND end_time > ?) OR
              (start_time < ? AND end_time >= ?) OR
              (start_time >= ? AND end_time <= ?)
              )";
    $stmt = $connect->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $connect->error]);
        exit;
    }

    $stmt->bind_param("isssssss", $roomId, $date, $startTime, $startTime, $endTime, $endTime, $startTime, $endTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This time slot is already booked.']);
        exit;
    }

    // Insert booking into the database
    $stmt = $connect->prepare("INSERT INTO bookings (user_id, room_id, date, start_time, end_time, num_people, status, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, DEFAULT, NOW())");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $connect->error]);
        exit;
    }

    $stmt->bind_param("iisssi", $userId, $roomId, $date, $startTime, $endTime, $numPeople);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Booking successfully created!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create booking: ' . $stmt->error]);
    }

    $stmt->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Calendar</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            position: relative; /* For close button positioning */
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

         button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .calendar-controls button:hover {
            background-color: #0056b3;
        }

        .calendar h2 {
            text-align: center;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
            color: #333;
        }

        .time-selection {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .checkin,
        .checkout {
            width: 48%;
        }

        h2 {
            color: #555;
            margin-bottom: 10px;
        }

        .time-selection input[type="time"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
        }

        .seats {
            text-align: center;
            color: #555;
        }

        .seat-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }

        .seat-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }

        .seat-controls button:hover {
            background-color: #0056b3;
        }

        #seat-count {
            width: 50px;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 0 5px;
        }

        /* Style for past days */
        td.past {
            color: #ccc; /* Gray out past days */
            cursor: not-allowed; /* Disable pointer events */
        }

        /* Style for today's date */
        td.today {
            background-color: #28a745; /* Green background for today */
            color: white; /* White text for better contrast */
            font-weight: bold; /* Make today's date bold */
        }

        /* Style for selected date */
        td.selected {
            background-color: #007bff; /* Blue background for selected date */
            color: white; /* White text for better contrast */
        }

        /* Close button (X) */
        #close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #333;
        }

        #close-button:hover {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Close Button (X) -->
        <button id="close-button">&times;</button>

        <h1>Select date and time</h1>
        <div class="calendar-controls">
            <button id="prev-month">Previous</button>
            <h2 id="current-month-year">March 2025</h2>
            <button id="next-month">Next</button>
        </div>
        <div class="calendar">
            <table>
                <thead>
                    <tr>
                        <th>SUN</th>
                        <th>MON</th>
                        <th>TUE</th>
                        <th>WED</th>
                        <th>THU</th>
                        <th>FRI</th>
                        <th>SAT</th>
                    </tr>
                </thead>
                <tbody id="calendar-body">
                    <!-- Calendar days will be populated here by JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="time-selection">
            <div class="checkin">
                <h2>Check-in</h2>
                <input type="time" id="checkin-time" value="09:00">
            </div>
            <div class="checkout">
                <h2>Check-out</h2>
                <input type="time" id="checkout-time" value="10:00">
            </div>
        </div>
        <div class="seats">
            <p>Number of seats:</p>
            <div class="seat-controls">
                <button id="minus-seat">-</button>
                <input type="text" id="seat-count" value="1" readonly>
                <button id="plus-seat">+</button>
            </div>
        </div>
        <button id="confirm-button" style="width: 100%; padding: 10px; margin-top: 20px;">Confirm</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarBody = document.getElementById('calendar-body');
            const currentMonthYear = document.getElementById('current-month-year');
            const prevMonthButton = document.getElementById('prev-month');
            const nextMonthButton = document.getElementById('next-month');
            const confirmButton = document.getElementById('confirm-button');
            const closeButton = document.getElementById('close-button'); // Close button

            let currentDate = new Date();
            let selectedDate = null;

            // Close button functionality
            closeButton.addEventListener('click', function () {
                window.history.back(); // Redirect to the previous page
            });

            function renderCalendar(date) {
                const year = date.getFullYear();
                const month = date.getMonth();
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay();

                currentMonthYear.textContent = `${firstDay.toLocaleString('default', { month: 'long' })} ${year}`;

                let calendarHtml = '';
                let day = 1;

                // Get today's date for comparison
                const today = new Date();
                const todayString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

                for (let i = 0; i < 6; i++) {
                    calendarHtml += '<tr>';
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < startingDay) {
                            calendarHtml += '<td></td>';
                        } else if (day > daysInMonth) {
                            calendarHtml += '<td></td>';
                        } else {
                            const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            const isSelected = selectedDate === dateString;
                            const isToday = dateString === todayString; // Check if the day is today
                            const isPast = new Date(dateString) < new Date(todayString); // Check if the day is in the past

                            // Add classes for today, past days, and selected days
                            let classes = '';
                            if (isToday) classes += 'today ';
                            if (isPast) classes += 'past ';
                            if (isSelected) classes += 'selected';

                            calendarHtml += `<td data-date="${dateString}" class="${classes.trim()}">${day}</td>`;
                            day++;
                        }
                    }
                    calendarHtml += '</tr>';
                    if (day > daysInMonth) {
                        break;
                    }
                }

                calendarBody.innerHTML = calendarHtml;

                // Add event listeners to calendar days
                const days = document.querySelectorAll('#calendar-body td');
                days.forEach(day => {
                    const date = day.getAttribute('data-date');
                    const isPast = new Date(date) < new Date(todayString);

                    if (!isPast) {
                        day.addEventListener('click', function () {
                            selectedDate = date;
                            renderCalendar(currentDate); // Re-render calendar to update selection
                        });
                    }
                });
            }

            prevMonthButton.addEventListener('click', function () {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar(currentDate);
            });

            nextMonthButton.addEventListener('click', function () {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar(currentDate);
            });

            renderCalendar(currentDate);

            const minusSeatButton = document.getElementById('minus-seat');
            const plusSeatButton = document.getElementById('plus-seat');
            const seatCountInput = document.getElementById('seat-count');

            minusSeatButton.addEventListener('click', function () {
                let currentSeats = parseInt(seatCountInput.value);
                if (currentSeats > 1) {
                    seatCountInput.value = currentSeats - 1;
                }
            });

            plusSeatButton.addEventListener('click', function () {
                let currentSeats = parseInt(seatCountInput.value);
                seatCountInput.value = currentSeats + 1;
            });

            const checkinTimeInput = document.getElementById('checkin-time');
            const checkoutTimeInput = document.getElementById('checkout-time');

            confirmButton.addEventListener('click', function () {
                if (!selectedDate) {
                    alert('Please select a date.');
                    return;
                }

                const checkinTime = checkinTimeInput.value;
                const checkoutTime = checkoutTimeInput.value;
                const seats = seatCountInput.value;

                // Get room_id from the query parameter
                const urlParams = new URLSearchParams(window.location.search);
                const roomId = urlParams.get('r_id');

                if (!roomId) {
                    alert('Room ID is missing.');
                    return;
                }

                // Prepare data to send to the backend
                const bookingData = {
                    date: selectedDate,
                    start_time: checkinTime,
                    end_time: checkoutTime,
                    num_people: seats,
                    room_id: roomId // Pass the room_id dynamically
                };

                console.log('Sending booking data:', bookingData); // Debugging

                // Send data to the backend (same file)
                fetch('book_now.php?r_id=' + roomId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(bookingData)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Booking successful! Redirecting to payment...');
                            window.location.href = 'payment.php'; // Redirect to payment.php
                        } else {
                            alert('Booking failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request. Details: ' + error.message);
                    });
            });
        });
    </script>
</body>

</html>