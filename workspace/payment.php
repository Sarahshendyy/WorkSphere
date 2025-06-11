<?php
include "mail.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$workspaceId = mysqli_real_escape_string($connect, $_GET['workspace_id']);
// $workspaceId = 13;
$userId = $_SESSION['user_id'];

// Verify the user is the owner of this workspace
$query = "SELECT workspaces.*, users.*, workspaces.name as workspace_name 
          FROM workspaces 
          JOIN users ON workspaces.user_id = users.user_id
          WHERE workspaces.workspace_id = ? 
          AND workspaces.user_id = ?"; 
$stmt = $connect->prepare($query);
if (!$stmt) {
    die("Database error: " . $connect->error);
}

$stmt->bind_param("ii", $workspaceId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Workspace not found or you don't have permission to access this page.");
}

$workspaceData = $result->fetch_assoc();

// Process payment
if (isset($_POST['pay'])) {
    $transactionId = rand(10000, 99999);
    $amount = 1000; // Fixed monthly subscription fee
    $renewalDate = date('Y-m-d', strtotime('+1 month')); // Calculate renewal date

    // Insert payment into the payments table
    $insert = "INSERT INTO payments (booking_id, workspace_id, amount, payment_method, transaction_id, renewal_date, created_at) 
               VALUES (NULL, ?, ?, 'visa', ?, ?, NOW())";
    $stmt = $connect->prepare($insert);
    
    // Corrected bind_param - 4 variables for 4 placeholders
    $stmt->bind_param("idis", $workspaceId, $amount, $transactionId, $renewalDate);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update workspace availability
        $update = "UPDATE workspaces SET Availability = 2 WHERE workspace_id = ?";
        $updateStmt = $connect->prepare($update);
        $updateStmt->bind_param("i", $workspaceId);
        $updateStmt->execute();
        
        header("Location: workspaces_dashboard.php");
        exit;
    } else {
        $error = "Payment failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Owner Subscription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #e0d6c3 0%, #b8c6db 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .main-payment-container {
            width: 420px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            padding: 40px 30px 30px 30px;
            position: relative;
            margin-top: 80px;
        }
        .card-container {
            position: absolute;
            left: 50%;
            top: -90px;
            transform: translateX(-50%);
            z-index: 2;
        }
        .front {
            width: 320px;
            height: 190px;
            background: linear-gradient(135deg, #0a1a36 60%, #1e3c72 100%);
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
            color: #fff;
            padding: 30px 25px 20px 25px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .front .image {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .front .image img {
            height: 32px;
        }
        .card-number-box {
            font-size: 1.3rem;
            letter-spacing: 2px;
            margin: 18px 0 10px 0;
        }
        .flexbox {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .box span {
            font-size: 0.8rem;
            color: #b8c6db;
        }
        .card-holder-name, .expiration {
            font-size: 1rem;
            font-weight: 500;
            margin-top: 2px;
        }
        .total-amount {
            color: #4CAF50;
            font-weight: bold;
            font-size: 1.1rem;
            text-align: right;
            margin-top: 10px;
        }
        .payment-form {
            margin-top: 120px;
        }
        .inputBox {
            margin-bottom: 18px;
        }
        .inputBox span {
            display: block;
            font-size: 0.95rem;
            color: #222;
            margin-bottom: 7px;
            font-weight: 500;
        }
        .inputBox input,
        .inputBox select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 7px;
            font-size: 1rem;
            background: #f8f9fa;
            outline: none;
            transition: border 0.2s;
        }
        .inputBox input:focus,
        .inputBox select:focus {
            border: 1.5px solid #0a7273;
        }
        .flexbox-form {
            display: flex;
            gap: 10px;
        }
        .flexbox-form .inputBox {
            flex: 1 1 0;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 3px;
        }
        .cssbuttons-io-button {
            background: #0a7273;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .cssbuttons-io-button:hover {
            background: #085d5e;
        }
        @media (max-width: 500px) {
            .main-payment-container {
                width: 98vw;
                padding: 20px 5vw 20px 5vw;
            }
            .card-container .front {
                width: 90vw;
                min-width: 220px;
                max-width: 98vw;
            }
        }
    </style>
</head>
<body>
    <div class="main-payment-container">
        <div class="card-container">
            <div class="front">
               <div class="image">
                    <img src="img/chip-card2 (1).png" alt="">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="visa-icon">
                </div>
                <div class="card-number-box">################</div>
                <div class="flexbox">
                    <div class="box">
                        <span>CARD HOLDER</span>
                        <div class="card-holder-name">FULL NAME</div>
                    </div>
                    <div class="box">
                        <span>EXPIRES</span>
                        <div class="expiration">
                            <span class="exp-month">MM</span>
                            <span class="exp-year">YY</span>
                        </div>
                    </div>
                </div>
                <div class="total-amount">TOTAL DUE: 1000 EGP</div>
            </div>
        </div>
        <form method="POST" class="payment-form" onsubmit="return validateForm()">
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <div class="inputBox">
                <span>Workspace Name</span>
                <input type="text" value="<?php echo htmlspecialchars($workspaceData['workspace_name']); ?>" readonly>
            </div>
            <div class="inputBox">
                <span>Subscription Type</span>
                <input type="text" value="Monthly Platform Access" readonly>
            </div>
            <div class="inputBox">
                <span>Renewal Date</span>
                <input type="text" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>" readonly>
            </div>
            <div class="inputBox">
                <span>Card Number</span>
                <input type="number" maxlength="16" name="card_number" id="card-number-input" class="card-number-input" oninput="validateNum()" required>
                <span name="numError" id="numError" class="error" style="display:none;"></span>
            </div>
            <div class="inputBox">
                <span>Card Holder</span>
                <input type="text" class="card-holder-input" name="C-HOLDER" id="card-holder-input" oninput="validateName()" required>
                <span name="nameError" id="nameError" class="error" style="display:none;"></span>
            </div>
            <div class="flexbox-form">
                <div class="inputBox">
                    <span>Expiration MM</span>
                    <select name="MM" id="month-input" oninput="validateMonth()" class="month-input" required>
                        <option value="month" id="MONTH">Month</option>
                        <?php for ($i = 1; $i <= 12; $i++) {
                            echo "<option value='$i'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
                        } ?>
                    </select>
                    <span name="monthError" id="monthError" class="error" style="display:none;"></span>
                </div>
                <div class="inputBox">
                    <span>Expiration YY</span>
                    <select name="YY" id="year-input" oninput="validateYear()" class="year-input" required>
                        <option value="year" id="YEAR">Year</option>
                        <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++) {
                            echo "<option value='$i'>$i</option>";
                        } ?>
                    </select>
                    <span name="yearError" id="yearError" class="error" style="display:none;"></span>
                </div>
                <div class="inputBox">
                    <span>CVV</span>
                    <input type="text" maxlength="3" class="cvv-input" name="cvv" id="cvv-input" oninput="validateCvv()" required>
                    <span name="cvvError" id="cvvError" class="error" style="display:none;"></span>
                </div>
            </div>
            <button class="cssbuttons-io-button" type="submit" name="pay">
                <span>Pay</span>
                <i class="fa fa-arrow-right"></i>
            </button>
        </form>
    </div>
   
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Card Number Display and Validation
            document.getElementById('card-number-input').oninput = () => {
                document.querySelector('.card-number-box').innerText = document.getElementById('card-number-input').value;
                validateNum();
            };

            // Card Holder Name Display and Validation
            document.getElementById('card-holder-input').oninput = () => {
                document.querySelector('.card-holder-name').innerText = document.getElementById('card-holder-input').value;
                validateName();
            };

            // Expiration Month Display and Validation
            document.getElementById('month-input').oninput = () => {
                document.querySelector('.exp-month').innerText = document.getElementById('month-input').value;
                validateMonth();
            };

            // Expiration Year Display and Validation
            document.getElementById('year-input').oninput = () => {
                document.querySelector('.exp-year').innerText = document.getElementById('year-input').value;
                validateYear();
            };

            // CVV Display and Validation
            document.getElementById('cvv-input').oninput = () => {
                document.querySelector('.cvv-box').innerText = document.getElementById('cvv-input').value;
                validateCvv();
            };

            // Rotate card on CVV focus
            document.getElementById('cvv-input').onmouseenter = () => {
                document.querySelector('.front').style.transform = 'perspective(1000px) rotateY(-180deg)';
                document.querySelector('.back').style.transform = 'perspective(1000px) rotateY(0deg)';
            };

            // Rotate card back on CVV blur
            document.getElementById('cvv-input').onmouseleave = () => {
                document.querySelector('.front').style.transform = 'perspective(1000px) rotateY(0deg)';
                document.querySelector('.back').style.transform = 'perspective(1000px) rotateY(180deg)';
            };

            // Prevent form submission if validation fails
            document.querySelector('form').onsubmit = function (e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            };

            // Validation Functions
            function validateNum() {
                const cardNumber = document.getElementById('card-number-input').value;
                const numError = document.getElementById('numError');
                if (cardNumber.length !== 16) {
                    numError.innerText = 'Card number must be 16 digits.';
                    numError.style.display = 'block';
                    return false;
                } else {
                    numError.style.display = 'none';
                    return true;
                }
            }

            function validateName() {
                const cardHolder = document.getElementById('card-holder-input').value;
                const nameError = document.getElementById('nameError');
                if (!cardHolder.match(/^[A-Za-z ]+$/)) {
                    nameError.innerText = 'Invalid cardholder name.';
                    nameError.style.display = 'block';
                    return false;
                } else {
                    nameError.style.display = 'none';
                    return true;
                }
            }

            function validateMonth() {
                const month = document.getElementById('month-input').value;
                const monthError = document.getElementById('monthError');
                if (month === 'month') {
                    monthError.innerText = 'Please select a valid month.';
                    monthError.style.display = 'block';
                    return false;
                } else {
                    monthError.style.display = 'none';
                    return true;
                }
            }

            function validateYear() {
                const year = document.getElementById('year-input').value;
                const yearError = document.getElementById('yearError');
                if (year === 'year') {
                    yearError.innerText = 'Please select a valid year.';
                    yearError.style.display = 'block';
                    return false;
                } else {
                    yearError.style.display = 'none';
                    return true;
                }
            }

            function validateCvv() {
                const cvv = document.getElementById('cvv-input').value;
                const cvvError = document.getElementById('cvvError');
                if (cvv.length !== 3 || !cvv.match(/^\d{3}$/)) {
                    cvvError.innerText = 'CVV must be 3 digits.';
                    cvvError.style.display = 'block';
                    return false;
                } else {
                    cvvError.style.display = 'none';
                    return true;
                }
            }

            function validateForm() {
                return (
                    validateNum() &&
                    validateName() &&
                    validateMonth() &&
                    validateYear() &&
                    validateCvv()
                );
            }
        });
    </script>
</body>
</html>