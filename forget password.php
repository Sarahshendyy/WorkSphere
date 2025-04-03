<?php
include 'mail.php';
$error=null;
$email=$_SESSION['email'];

if(isset($_POST['submit']))
{
    $select = "SELECT * FROM `users` WHERE `email`='$email'";
    $runSelect = mysqli_query($connect, $select);
    $fetch = mysqli_fetch_assoc($runSelect);
    $user_name = $fetch['name'];
    $newpassword =mysqli_real_escape_string($connect, $_POST['npassword']);
    $repassword =mysqli_real_escape_string($connect, $_POST['repassword']);
    $uppercase = preg_match('@[A-Z]@', $newpassword);
    $lowercase = preg_match('@[a-z]@', $newpassword);
    $number = preg_match('@[0-9]@', $newpassword);
    $character = preg_match('@[^/w]@', $newpassword);
    if (empty($newpassword) || empty($repassword))
        $error = "Must enter data";
    else if ($uppercase < 1 || $lowercase < 1 || $number < 1 || $character < 1)
        $error = "Password must contain uppercase, lowercase, numbers, characters";
    else if ($newpassword != $repassword)
        $error = "New password doesn't match confirm password";
    else
    {
        if ($newpassword == $repassword)
        {
            $newHashPass = password_hash($newpassword, PASSWORD_DEFAULT);
            $update = "UPDATE `users` SET `password`='$newHashPass' WHERE `email`='$email'";
            $runubdate = mysqli_query($connect, $update);
            if ($runubdate) {
                $massage = "
                <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
                    <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                        <h1>Password Reset Successful</h1>
                    </div>
                    <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                        <p style='color: #00000a;'>Dear <span style='color: #fda521;'> $user_name</span>,</p>
                        <p style='color: #00000a;'>Your password has been successfully reset. You can now log in to your account using your new password.</p>
                        <p style='color: #00000a;'>If you did not request this change, please contact our support team immediately.</p>
                        <p style='color: #00000a;'>Best regards,<br>The WorkSphere Team</p>
                    </div>
                    <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                        <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                        <p style='color: #fffffa;'>Email: <a href='mailto:worksphere04@gmail.com' style='color: #fda521;'>worksphere04@gmail.com</a></p>
                    </div>
                </body>
                ";

                $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Successfully';
                $mail->Body = ($massage);
                $mail->send();

                unset($_SESSION['otp']); // to avoid trouble
                $_SESSION['logCHK'] = true;
                header("Location:signup&login.php");
            }
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
    <title>New Password Page</title>
    <link rel="stylesheet" type="text/css" href="css/editpassword.css">
    <style>
        .warning {
            display: none;
            color: red;
        }
        .warning.visible {
            display: block;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="from-wraapper  Sign-in">
            <form method="POST">
            <h2>Forgot Password</h2>
            <div class="warning <?php if ($error) { echo 'visible'; } ?>">
                <?php if ($error) { echo $error; } ?>
            </div>
            <div class="input-group">
                <input type="password"  name="npassword">
                <label for="">New password</label>
            </div>

            <div class="input-group">
                <input type="password"  name="repassword">
                <label for="">Confirm new password</label>
            </div>

            <a href="login.php"> <button type="submit"  name="submit">Submit</button> </a>
            <div class="signUp-link">
                <p> <a href="#" class="signUpBtn-link"></a> </p>
            </div>
            </form>
            </div>
        </div>

    <script src="script.js"></script>
</body>

</html>