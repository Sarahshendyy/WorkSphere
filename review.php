<?php
include "connection.php";
$user_id = $_SESSION['user_id'];

// Handle form submission
if (isset($_POST['submit'])) {
    $booking_id = $_POST['booking_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $time=date("Y-m-d H:i:s");

    // Validate rating (must be between 1 and 5)
    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Rating must be between 1 and 5.');</script>";
    } else {
        // Insert the review into the database
        $insert_query = "INSERT INTO `reviews` 
                         VALUES (NULL, $booking_id, $rating, '$comment', '$time')";
        if (mysqli_query($connect, $insert_query)) {
           // Display a pop-up message and then redirect
           echo "<script>
           alert('Thank you for your review!');
           window.location.href = 'indexx.php'; // Replace 'home.php' with your home page URL
         </script>";
        } else {
            echo "<script>alert('Error submitting review. Please try again.');</script>";
        }
    }
}

// Fetch the booking ID from the URL
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
} else {
    echo "<script>alert('Invalid booking ID.'); window.location.href = 'my_bookings.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="./css/review.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="review-container">
        <h1>Review Your Booking</h1>
        <form method="POST" action="">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            <div class="rating-section">
                <label for="rating">Rating (1-5):</label>
                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required />
                    <label for="star5" title="5 stars">&#9733;</label>
                    <input type="radio" id="star4" name="rating" value="4" />
                    <label for="star4" title="4 stars">&#9733;</label>
                    <input type="radio" id="star3" name="rating" value="3" />
                    <label for="star3" title="3 stars">&#9733;</label>
                    <input type="radio" id="star2" name="rating" value="2" />
                    <label for="star2" title="2 stars">&#9733;</label>
                    <input type="radio" id="star1" name="rating" value="1" />
                    <label for="star1" title="1 star">&#9733;</label>
                </div>
                <!-- <p id="rating-value"></p> -->
            </div>

            <div class="comment-section">
                <label for="comment">Comment (optional):</label>
                <textarea name="comment" id="comment" rows="4" placeholder="Write your comment here..."></textarea>
            </div>

            <button type="submit" name="submit" class="submit-button">Submit Review</button>
        </form>
    </div>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.star-rating input');
            const ratingValue = document.getElementById('rating-value'); // Optional: Display selected value

            stars.forEach(star => {
                star.addEventListener('change', function () {
                    if (ratingValue) {
                        ratingValue.textContent = `Selected Rating: ${this.value}`;
                    }
                });
            });
        });
    </script> -->
</body>

</html>