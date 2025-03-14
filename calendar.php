<?php
include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <!-- CSS for full calendar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- JS for moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <!-- JS for full calendar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <link rel="stylesheet" href="./css/calendar.css">
</head>

<body>
    <div id='calendar'></div>
    <script>
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                editable: false,
                events: function (start, end, timezone, callback) {
                    $.ajax({
                        url: 'display_booking.php',
                        dataType: 'json',
                        success: function (bookingData) {
                            var events = [];
                            var currentTime = moment();

                            if (bookingData.status) {
                                $.each(bookingData.data, function (i, booking) {
                                    var startTime = moment(booking.start);
                                    var endTime = moment(booking.end);

                                    if (endTime.isBefore(currentTime)) {
                                        booking.color = 'green'; // Completed
                                    } else if (startTime.isAfter(currentTime)) {
                                        booking.color = 'orange'; // Upcoming
                                    } else {
                                        booking.color = 'red'; // Ongoing
                                    }

                                    events.push(booking);
                                });
                            }
                            callback(events);
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching booking data:', error);
                            alert('Error fetching booking data.');
                        }
                    });
                },
                eventClick: function (event) {
                    window.location.href = 'my_bookings.php' ;
                }
            });
        });
    </script>
</body>

</html>