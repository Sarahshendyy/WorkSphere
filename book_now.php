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
    $booking_id=$data['booking_id'] ?? '';
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
        $booking_id = $stmt->insert_id; // Get the last inserted booking ID
        echo json_encode(['status' => 'success', 'message' => 'Booking successfully created!', 'booking_id' => $booking_id]);
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Book Meeting Room</title>

  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

  <style>
    :root {
      --dark-navy: #071739;
      --medium-blue: #4B6382;
      --light-blue: #A4B5C4;
      --light-grayish: #CDD5DB;
      --brownish: #A68868;
      --light-brown: #E3C39D;

      --font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      --shadow-light: 0 4px 12px rgba(7, 23, 57, 0.15);
      --shadow-hover: 0 6px 16px rgba(7, 23, 57, 0.25);
    }

    /* Reset and base */
    * {
      box-sizing: border-box;
    }

    body {
      font-family: var(--font-family);
      background-color: var(--light-grayish);
      margin: 0;
      padding: 30px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: var(--dark-navy);
    }

    .container {
      background-color: #fff;
      padding: 40px 40px 50px;
      border-radius: 16px;
      box-shadow: var(--shadow-light);
      width: 90%;
      max-width: 760px;
      position: relative;
      transition: box-shadow 0.3s ease;
    }

    .container:hover {
      box-shadow: var(--shadow-hover);
    }

    h1 {
      text-align: center;
      color: var(--dark-navy);
      margin-bottom: 30px;
      font-weight: 600;
      font-size: 2.2rem;
      letter-spacing: 0.04em;
    }

    .calendar-controls {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
      background-color: var(--light-blue);
      padding: 14px 22px;
      border-radius: 12px;
      user-select: none;
    }

    button {
      background-color: var(--medium-blue);
      color: white;
      border: none;
      padding: 12px 28px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1rem;
      box-shadow: 0 3px 8px rgba(75, 99, 130, 0.4);
      transition: background-color 0.25s ease, box-shadow 0.25s ease;
      letter-spacing: 0.02em;
    }

    button:hover {
      background-color: var(--dark-navy);
      box-shadow: 0 6px 14px rgba(7, 23, 57, 0.6);
    }

    button:disabled {
      background-color: #ccc;
      cursor: not-allowed;
      box-shadow: none;
    }

    .calendar h2 {
      text-align: center;
      color: var(--dark-navy);
      margin: 0;
      font-size: 1.4rem;
      font-weight: 600;
      user-select: none;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 8px 8px;
      margin: 20px 0 30px;
      user-select: none;
    }

    th {
      background-color: var(--light-grayish);
      color: var(--dark-navy);
      padding: 12px 0;
      font-weight: 700;
      font-size: 1rem;
      border-radius: 8px;
    }

    td {
      background-color: var(--light-blue);
      padding: 14px 0;
      text-align: center;
      border-radius: 10px;
      font-weight: 500;
      font-size: 1rem;
      color: var(--dark-navy);
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
      box-shadow: inset 0 0 0 0 transparent;
    }

    td:hover:not(.past):not(.selected) {
      background-color: var(--medium-blue);
      color: white;
      box-shadow: inset 0 0 8px rgba(75, 99, 130, 0.25);
    }

    td.past {
      color: #999;
      cursor: not-allowed;
      background-color: var(--light-grayish);
      box-shadow: none;
    }

    td.today {
      background-color: var(--brownish);
      color: white;
      font-weight: 700;
      box-shadow: 0 0 12px rgba(166, 136, 104, 0.7);
    }

    td.selected {
      background-color: var(--medium-blue);
      color: white;
      font-weight: 700;
      box-shadow: 0 0 14px rgba(75, 99, 130, 0.9);
    }

    .time-selection {
      display: flex;
      justify-content: space-between;
      margin: 35px 0 40px;
      gap: 24px;
    }

    .time-group {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .time-group h2 {
      color: var(--dark-navy);
      margin-bottom: 14px;
      font-size: 1.2rem;
      font-weight: 600;
    }

    select {
      width: 100%;
      padding: 14px 18px;
      border: 1.5px solid var(--light-grayish);
      border-radius: 12px;
      font-size: 1rem;
      background-color: white;
      cursor: pointer;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      font-weight: 500;
      color: var(--dark-navy);
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image:
        linear-gradient(45deg, transparent 50%, var(--medium-blue) 50%),
        linear-gradient(135deg, var(--medium-blue) 50%, transparent 50%);
      background-position:
        calc(100% - 20px) calc(1em + 2px),
        calc(100% - 15px) calc(1em + 2px);
      background-size: 5px 5px, 5px 5px;
      background-repeat: no-repeat;
    }

    select:focus {
      outline: none;
      border-color: var(--medium-blue);
      box-shadow: 0 0 8px rgba(75, 99, 130, 0.5);
      background-color: #fff;
    }

    .seats {
      text-align: center;
      margin-bottom: 40px;
    }

    .seats p {
      margin-bottom: 14px;
      font-size: 1.25rem;
      color: var(--dark-navy);
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .seat-controls {
      display: inline-flex;
      justify-content: center;
      align-items: center;
      border: 1.5px solid var(--light-grayish);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.03);
    }

    .seat-btn {
      width: 44px;
      height: 44px;
      background-color: var(--medium-blue);
      color: white;
      border: none;
      font-size: 22px;
      font-weight: 700;
      cursor: pointer;
      transition: background-color 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      user-select: none;
    }

    .seat-btn:hover {
      background-color: var(--dark-navy);
    }

    #seat-count {
      width: 70px;
      text-align: center;
      padding: 12px 10px;
      border: none;
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--dark-navy);
      user-select: none;
      background-color: transparent;
    }

    #close-button {
      position: absolute;
      top: 18px;
      right: 18px;
      background: none;
      border: none;
      font-size: 28px;
      cursor: pointer;
      color: #999;
      transition: color 0.25s ease;
      font-weight: 700;
      user-select: none;
    }

    #close-button:hover {
      color: var(--brownish);
    }

    #confirm-button {
      width: 100%;
      padding: 16px;
      margin-top: 10px;
      font-size: 1.1rem;
      background-color: var(--brownish);
      border: none;
      border-radius: 14px;
      color: white;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 5px 14px rgba(166, 136, 104, 0.5);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
    }

    #confirm-button:hover {
      background-color: var(--light-brown);
      box-shadow: 0 6px 18px rgba(227, 195, 157, 0.7);
    }

    /* SweetAlert2 custom styling */
    .swal2-popup {
      font-family: var(--font-family);
      background: var(--light-grayish) !important;
      border-radius: 16px !important;
      color: var(--dark-navy) !important;
      box-shadow: 0 8px 25px rgba(7, 23, 57, 0.3) !important;
    }

    .swal2-title {
      font-weight: 700 !important;
      font-size: 1.5rem !important;
      color: var(--dark-navy) !important;
    }

    .swal2-confirm {
      background-color: var(--medium-blue) !important;
      color: white !important;
      font-weight: 600 !important;
      padding: 10px 25px !important;
      border-radius: 12px !important;
      border: none !important;
      cursor: pointer !important;
      transition: background-color 0.25s ease !important;
      box-shadow: none !important;
      margin: 0 4px !important;
    }

    .swal2-confirm:hover {
      background-color: var(--dark-navy) !important;
    }

    .swal2-cancel {
      background-color: var(--brownish) !important;
      color: white !important;
      font-weight: 600 !important;
      padding: 10px 25px !important;
      border-radius: 12px !important;
      border: none !important;
      cursor: pointer !important;
      margin: 0 4px !important;
      transition: background-color 0.25s ease !important;
      box-shadow: none !important;
    }

    .swal2-cancel:hover {
      background-color: var(--light-brown) !important;
    }

    /* Responsive */
    @media (max-width: 600px) {
      .container {
        padding: 30px 25px 40px;
        width: 95%;
      }

      .time-selection {
        flex-direction: column;
        gap: 20px;
      }

      td,
      th {
        padding: 10px 0;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <button id="close-button" aria-label="Close booking window">&times;</button>
    <h1>Book Meeting Room</h1>

    <div class="calendar-controls">
      <button id="prev-month">Previous</button>
      <h2 id="current-month-year">March 2025</h2>
      <button id="next-month">Next</button>
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
        <tbody id="calendar-body">
          <!-- Calendar days will be populated here by JavaScript -->
        </tbody>
      </table>
    </div>

    <div class="time-selection">
      <div class="time-group">
        <h2>Start Time</h2>
        <select id="checkin-time"></select>
      </div>
      <div class="time-group">
        <h2>End Time</h2>
        <select id="checkout-time"></select>
      </div>
    </div>

    <div class="seats">
      <p>Number of Attendees</p>
      <div class="seat-controls">
        <button id="minus-seat" class="seat-btn" aria-label="Decrease attendees count">-</button>
        <input type="text" id="seat-count" value="1" readonly aria-live="polite" aria-label="Number of attendees" />
        <button id="plus-seat" class="seat-btn" aria-label="Increase attendees count">+</button>
      </div>
    </div>

    <button id="confirm-button">Confirm Booking</button>
  </div>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const calendarBody = document.getElementById('calendar-body');
      const currentMonthYear = document.getElementById('current-month-year');
      const prevMonthButton = document.getElementById('prev-month');
      const nextMonthButton = document.getElementById('next-month');
      const confirmButton = document.getElementById('confirm-button');
      const closeButton = document.getElementById('close-button');
      const checkinSelect = document.getElementById('checkin-time');
      const checkoutSelect = document.getElementById('checkout-time');

      let currentDate = new Date();
      let selectedDate = null;

      // SweetAlert base config
      const swalConfig = {
        buttonsStyling: false,
      };

      // Generate time options in 15-minute intervals (quarter hours)
      function generateTimeOptions() {
        const timeOptions = [];
        for (let hour = 0; hour < 24; hour++) {
          ['00', '15', '30', '45'].forEach(minute => {
            timeOptions.push(`${String(hour).padStart(2, '0')}:${minute}`);
          });
        }
        return timeOptions;
      }

      // Populate time dropdowns
      const timeOptions = generateTimeOptions();
      timeOptions.forEach(time => {
        const option1 = document.createElement('option');
        option1.value = time;
        option1.textContent = time;
        checkinSelect.appendChild(option1);

        const option2 = document.createElement('option');
        option2.value = time;
        option2.textContent = time;
        checkoutSelect.appendChild(option2);
      });

      // Set default times: 09:00 to 10:00
      checkinSelect.value = '09:00';
      checkoutSelect.value = '10:00';

      // Close button action
      closeButton.addEventListener('click', () => window.history.back());

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
              const isToday = dateString === todayString;
              const isPast = new Date(dateString) < new Date(todayString);

              let classes = '';
              if (isToday) classes += 'today ';
              if (isPast) classes += 'past ';
              if (isSelected) classes += 'selected';

              calendarHtml += `<td data-date="${dateString}" class="${classes.trim()}">${day}</td>`;
              day++;
            }
          }
          calendarHtml += '</tr>';
          if (day > daysInMonth) break;
        }

        calendarBody.innerHTML = calendarHtml;

        // Add event listeners to days
        document.querySelectorAll('#calendar-body td[data-date]').forEach(dayCell => {
          if (!dayCell.classList.contains('past')) {
            dayCell.addEventListener('click', () => {
              selectedDate = dayCell.getAttribute('data-date');
              renderCalendar(currentDate);
            });
          }
        });
      }

      prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
      });

      nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
      });

      renderCalendar(currentDate);

      // Seats control
      const minusSeatButton = document.getElementById('minus-seat');
      const plusSeatButton = document.getElementById('plus-seat');
      const seatCountInput = document.getElementById('seat-count');

      minusSeatButton.addEventListener('click', () => {
        let currentSeats = parseInt(seatCountInput.value, 10);
        if (currentSeats > 1) seatCountInput.value = currentSeats - 1;
      });

      plusSeatButton.addEventListener('click', () => {
        let currentSeats = parseInt(seatCountInput.value, 10);
        seatCountInput.value = currentSeats + 1;
      });

      // Confirm booking
      confirmButton.addEventListener('click', () => {
        if (!selectedDate) {
          Swal.fire({
            title: 'Please select a date.',
            icon: 'warning',
            ...swalConfig
          });
          return;
        }

        const startTime = checkinSelect.value;
        const endTime = checkoutSelect.value;

        if (startTime >= endTime) {
          Swal.fire({
            title: 'End time must be after start time.',
            icon: 'warning',
            ...swalConfig
          });
          return;
        }

        const seats = seatCountInput.value;

        const urlParams = new URLSearchParams(window.location.search);
        const roomId = urlParams.get('r_id');

        if (!roomId) {
          Swal.fire({
            title: 'Room ID is missing.',
            icon: 'error',
            ...swalConfig
          });
          return;
        }

        const bookingData = {
          date: selectedDate,
          start_time: startTime,
          end_time: endTime,
          num_people: seats,
          room_id: roomId,
        };

        console.log('Sending booking data:', bookingData);

        fetch('book_now.php?r_id=' + roomId, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(bookingData),
        })
          .then(response => {
            if (!response.ok) throw new Error('Network response was not ok: ' + response.statusText);
            return response.json();
          })
          .then(data => {
            if (data.status === 'success') {
              Swal.fire({
                title: 'Booking successful!',
                text: 'Redirecting to payment...',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                ...swalConfig
              }).then(() => {
                window.location.href = `booking_details.php?booking_id=${data.booking_id}`;
              });
            } else {
              Swal.fire({
                title: 'Booking failed',
                text: data.message,
                icon: 'error',
                ...swalConfig
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              title: 'Error',
              text: 'An error occurred while processing your request. Details: ' + error.message,
              icon: 'error',
              ...swalConfig
            });
          });
      });
    });
  </script>
</body>

</html>
