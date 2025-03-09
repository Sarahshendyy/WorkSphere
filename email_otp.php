<?php
include 'mail.php';
$error="";

if (isset($_POST['submit']))
{
    $_SESSION['email'] = mysqli_real_escape_string($connect, $_POST['email']);
    $email = $_SESSION['email'];
    $old_time = time();
    $_SESSION['time'] = $old_time;

    $select = "SELECT * FROM `users` WHERE `email`='$email'";
    $runselect = mysqli_query($connect, $select);
    $fetch = mysqli_fetch_assoc($runselect);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $error = "Invalid email format";
    }
    else if (mysqli_num_rows($runselect) == 0)
        $error = "This email is not registered";
    else
    {
        $user_name = $fetch['name'];
        $rand = rand(10000, 99999);
        $massage = "
            <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa ; color: #00000a ;'>
                <div style='background-color: #0a7273 ; padding: 20px ; text-align: center ; color: #fffffa ;'>
                    <h1>Password Reset Request</h1>
                </div>
                <div style='padding: 20px ; background-color: #fffffa ; color: #00000a ;'>
                    <p style='color: #00000a ;'>Dear <span style='color: #fda521;'>$user_name</span>,</p>
                    <p style='color: #00000a ;'>We received a request to reset your password. Please use the OTP below to complete the process:</p>
                    <p style='color: #00000a ; text-align: center ; font-size: 24px ; font-weight: bold ; color: #fda521 ;'>$rand</p>
                    <p style='color: #00000a ;'>If you did not request a password reset, please ignore this email or contact our support team for assistance.</p>
                    <p style='color: #00000a ;'>Best regards,<br>The Deskify Team</p>
                </div>
                <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                    <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                    <p style='color: #fffffa;'>Email: <a href='mailto:deskify0@gmail.com' style='color: #fda521;'>deskify0@gmail.com</a></p>
                </div>
            </body>

        ";
        $_SESSION["otp"] = $rand;

        $mail->setFrom('deskify0@gmail.com', 'Deskify');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset OTP';
        $mail->Body = ($massage);
        $mail->send();

        header("location:verification_forget _password.php");
    }
}
?>



<html lang="en">

<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <title>Email Verify</title>
    <link rel="stylesheet" type="text/css" href="css/editpassword.css">
    <style>
         <style>
        .warning {
            display: none;
            color: red;
        }
        .warning.visible {
            display: block;
        }
    </style>
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="from-wraapper  Sign-in">
            <form method="POST">
            <h2>Verify Your E-mail</h2>
            <br>
            <div class="warning <?php if(!empty($error)) echo 'visible' ?>">
                        <?php if (!empty($error)) echo $error ?>
                    </div>
            <div class="input-group">
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''?>">
                <label for="">E-mail</label>
            </div>
            <br>
            <button type="submit" name="submit">Submit</button>
            <div class="signUp-link">
                <p> <a href="#" class="signUpBtn-link"></a> </p>
            </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>