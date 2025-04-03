<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");

if ($connect->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new Exception('User not logged in');
        }

        $roomId = mysqli_real_escape_string($connect, $data['room_id'] ?? '');
        $month = $data['month'] ?? '';
        $year = $data['year'] ?? '';

        if (empty($roomId) || empty($month) || empty($year)) {
            throw new Exception('All fields are required');
        }

        // First check if any days in this month are already booked
        $firstDay = "$year-$month-01";
        $lastDay = date("Y-m-t", strtotime($firstDay));

        $checkQuery = "SELECT COUNT(*) as booked_days FROM bookings 
                      WHERE room_id = ? 
                      AND date BETWEEN ? AND ?";
        $stmt = $connect->prepare($checkQuery);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $connect->error);
        }

        $stmt->bind_param("iss", $roomId, $firstDay, $lastDay);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['booked_days'] > 0) {
            throw new Exception('This month has already booked days and cannot be fully reserved');
        }

        // Insert monthly booking for ALL days in the month (including weekends)
        $stmt = $connect->prepare("INSERT INTO bookings 
                                  (user_id, room_id, date, start_time, end_time, is_monthly, status, created_at) 
                                  VALUES (?, ?, ?, '09:00', '18:00', 1, DEFAULT, NOW())");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $connect->error);
        }

        // Create bookings for ALL days in the month (including weekends)
        $current = strtotime($firstDay);
        $last = strtotime($lastDay);
        $successCount = 0;

        while ($current <= $last) {
            $date = date('Y-m-d', $current);
            
            // Book ALL days (removed weekend check)
            $stmt->bind_param("iss", $userId, $roomId, $date);
            if ($stmt->execute()) {
                $successCount++;
            } else {
                error_log("Failed to book date $date: " . $stmt->error);
            }
            $current = strtotime('+1 day', $current);
        }

        if ($successCount > 0) {
            $booking_id = $stmt->insert_id;
            echo json_encode([
                'status' => 'success',
                'message' => "Monthly booking created for $successCount days!",
                'booking_id' => $booking_id
            ]);
        } else {
            throw new Exception('Failed to create any bookings');
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Get room details for display
$room_id = mysqli_real_escape_string($connect, $_GET['r_id']);
$room_query = "SELECT * FROM rooms WHERE room_id = $room_id";
$room_result = mysqli_query($connect, $room_query);
$room = mysqli_fetch_assoc($room_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Booking - <?php echo $room['room_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1000px;
            position: relative;
        }
        #close-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        h1 {
            color: #28a745;
            margin-bottom: 20px;
            text-align: center;
        }
        .room-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .month-selector {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .month-selector button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .month-selector button:hover {
            background-color: #0056b3;
        }
        #current-month-year {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        .calendar {
            margin-bottom: 30px;
        }
        .calendar table {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar th, .calendar td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .calendar th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .calendar td {
            height: 40px;
        }
        .calendar td.booked {
            background-color: #ff6b6b;
            color: white;
            cursor: not-allowed;
        }
        .calendar td.past {
            background-color: #e9ecef;
            color: #adb5bd;
        }
        .calendar td.available {
            background-color: #d4edda;
            cursor: pointer;
        }
        .calendar td.today {
            border: 2px solid #007bff;
        }
        #book-monthly {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            transition: all 0.3s;
        }
        #book-monthly:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        #book-monthly:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .savings-badge {
            background-color: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <button id="close-button" onclick="window.history.back()">&times;</button>
        
        <h1>Monthly Booking <span class="savings-badge">Save <?php echo $room['p/hr'] * 160 - $room['p/m']; ?> EGP</span></h1>
        
        <div class="room-info">
            <h3><?php echo $room['room_name']; ?></h3>
            <p>Monthly Price: <strong><?php echo $room['p/m']; ?> EGP</strong></p>
            <p>Includes all days of the month, 9AM-6PM</p>
        </div>
        
        <div class="month-selector">
            <button id="prev-month">&lt; Previous</button>
            <h2 id="current-month-year"></h2>
            <button id="next-month">Next &gt;</button>
        </div>
        
        <div class="calendar">
            <table>
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
        </div>
        
        <button id="book-monthly" disabled>Book This Month</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarBody = document.getElementById('calendar-body');
            const currentMonthYear = document.getElementById('current-month-year');
            const prevMonthBtn = document.getElementById('prev-month');
            const nextMonthBtn = document.getElementById('next-month');
            const bookBtn = document.getElementById('book-monthly');
            
            let currentDate = new Date();
            let selectedMonth = currentDate.getMonth();
            let selectedYear = currentDate.getFullYear();
            let bookedDays = [];
            let roomId = new URLSearchParams(window.location.search).get('r_id');
            
            // Initialize calendar
            renderCalendar();
            fetchBookedDays();
            
            // Month navigation
            prevMonthBtn.addEventListener('click', function() {
                selectedMonth--;
                if (selectedMonth < 0) {
                    selectedMonth = 11;
                    selectedYear--;
                }
                renderCalendar();
                fetchBookedDays();
            });
            
            nextMonthBtn.addEventListener('click', function() {
                selectedMonth++;
                if (selectedMonth > 11) {
                    selectedMonth = 0;
                    selectedYear++;
                }
                renderCalendar();
                fetchBookedDays();
            });
            
            // Book button
            bookBtn.addEventListener('click', function() {
                if (bookBtn.disabled) return;
                
                if (confirm(`Are you sure you want to book this room for the entire month of ${currentMonthYear.textContent}?`)) {
                    const bookingData = {
                        room_id: roomId,
                        month: selectedMonth + 1,
                        year: selectedYear
                    };
                    
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(bookingData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.href = `booking_details.php?booking_id=${data.booking_id}`;
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error occurred'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Booking failed. Please check console for details.');
                    });
                }
            });
            
            // Fetch booked days for the current month
            function fetchBookedDays() {
                const firstDay = new Date(selectedYear, selectedMonth, 1);
                const lastDay = new Date(selectedYear, selectedMonth + 1, 0);
                
                fetch(`get_booked_days.php?room_id=${roomId}&start=${formatDate(firstDay)}&end=${formatDate(lastDay)}`)
                    .then(response => response.json())
                    .then(data => {
                        bookedDays = data.booked_days || [];
                        updateCalendarAvailability();
                    })
                    .catch(error => {
                        console.error('Error fetching booked days:', error);
                    });
            }
            
            // Render calendar
            function renderCalendar() {
                const firstDay = new Date(selectedYear, selectedMonth, 1);
                const lastDay = new Date(selectedYear, selectedMonth + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = firstDay.getDay();
                
                currentMonthYear.textContent = `${firstDay.toLocaleString('default', { month: 'long' })} ${selectedYear}`;
                
                let calendarHtml = '';
                let day = 1;
                
                for (let i = 0; i < 6; i++) {
                    calendarHtml += '<tr>';
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < startingDay) {
                            calendarHtml += '<td></td>';
                        } else if (day > daysInMonth) {
                            calendarHtml += '<td></td>';
                        } else {
                            const date = new Date(selectedYear, selectedMonth, day);
                            const dateStr = formatDate(date);
                            const isToday = isSameDay(date, new Date());
                            
                            let classes = '';
                            if (isToday) classes += ' today';
                            
                            calendarHtml += `<td data-date="${dateStr}" class="${classes}">${day}</td>`;
                            day++;
                        }
                    }
                    calendarHtml += '</tr>';
                    if (day > daysInMonth) break;
                }
                
                calendarBody.innerHTML = calendarHtml;
                updateCalendarAvailability();
            }
            
            // Update calendar cell classes based on booked days
            function updateCalendarAvailability() {
                const today = new Date();
                const cells = document.querySelectorAll('#calendar-body td[data-date]');
                
                let hasBookedDays = false;
                
                cells.forEach(cell => {
                    const dateStr = cell.getAttribute('data-date');
                    const date = new Date(dateStr);
                    const isPast = date < today && !isSameDay(date, today);
                    
                    // Reset classes
                    cell.className = '';
                    
                    if (isPast) {
                        cell.classList.add('past');
                        return;
                    }
                    
                    if (bookedDays.includes(dateStr)) {
                        cell.classList.add('booked');
                        hasBookedDays = true;
                    } else {
                        cell.classList.add('available');
                    }
                });
                
                bookBtn.disabled = hasBookedDays;
                if (hasBookedDays) {
                    bookBtn.title = "This month has booked days and cannot be fully reserved";
                } else {
                    bookBtn.title = "";
                }
            }
            
            // Helper functions
            function formatDate(date) {
                return date.toISOString().split('T')[0];
            }
            
            function isSameDay(date1, date2) {
                return date1.getFullYear() === date2.getFullYear() &&
                       date1.getMonth() === date2.getMonth() &&
                       date1.getDate() === date2.getDate();
            }
        });
    </script>
</body>
</html>