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

    <!-- Tooltipster CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/css/tooltipster.min.css">
    <!-- Tooltipster JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.3.0/js/jquery.tooltipster.min.js"></script>

    <link rel="stylesheet" href="./css/calendar.css">
</head>

<body>
    <div id='calendar'></div>
    <script>
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                editable: false,
                timezone: 'UTC',
                events: function (start, end, timezone, callback) {
                    $.ajax({
                        url: 'display_booking.php',
                        dataType: 'json',
                        success: function (bookingData) {
                            var events = [];
                            var currentTime = moment().utc();

                            if (bookingData.status) {
                                $.each(bookingData.data, function (i, booking) {
                                    var startTime = moment.utc(booking.start);
                                    var endTime = moment.utc(booking.end);

                                    if (endTime.isBefore(currentTime)) {
                                        booking.color = 'green';
                                    } else if (startTime.isAfter(currentTime)) {
                                        booking.color = 'orange';
                                    } else {
                                        booking.color = 'red';
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
                    window.location.href = 'my_bookings.php';
                },
                eventRender: function (event, element) {
    element.find('.fc-time').remove(); // Remove the default time
    element.find('.fc-title').html(event.title);
}
            });
        });
    </script>
</body>

</html>