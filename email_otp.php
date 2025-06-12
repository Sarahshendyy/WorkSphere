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
    $message = '
        <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;">
        <div style="background-color: #071739; padding: 20px; text-align: center; color: #CDD5DB;">
            <h1>Password Reset Request</h1>
        </div>
        <div style="padding: 20px; background-color: #CDD5DB; color: #071739;">
            <p>Dear <span style="color:rgb(204, 169, 127);">' . htmlspecialchars($user_name) . '</span>,</p>
            <p>We received a request to reset your password. Please use the OTP below to complete the process:</p>
            <p style="text-align: center; font-size: 24px; font-weight: bold; color:rgb(206, 172, 130);">' . $rand . '</p>
            <p>If you did not request a password reset, please ignore this email or contact our support team for assistance.</p>
            <p>Best regards,<br>The WorkSphere Team</p>
        </div>
        <div style="background-color: #071739; padding: 10px; text-align: center; color: #CDD5DB;">
            <p>For support and updates, please visit our website or contact us via email.</p>
            <p>Email: <a href="mailto:worksphere50@gmail.com" style="color: #E3C39D; text-decoration: none;">worksphere050gmail.com</a></p>
        </div>
        </body>
    ';
    $_SESSION["otp"] = $rand;

    $mail->setFrom('worksphere50@gmail.com', 'WorkSphere');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset OTP';
    $mail->Body = $message;
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
