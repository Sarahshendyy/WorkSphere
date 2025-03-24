<?php
include 'mail.php';

if(!isset($_SESSION['otp'])) 
{
    header("Location: indexx.php");
}

$rand=$_SESSION['otp'];
$user_name=$_SESSION['name'];
$email=$_SESSION['email'];
$hash_password=$_SESSION['password'];
$phone=$_SESSION['phone_number'];
$old_time=$_SESSION['old_time'];
$timestamp = date('Y-m-d H:i:s');

$error=null;
if(isset($_POST['submit']))
{
    if (!isset($_POST['otp1'], $_POST['otp2'], $_POST['otp3'], $_POST['otp4'], $_POST['otp5']))
        $error = "Please fill all OTP fields";
    else
    {
        $otp= $_POST['otp1']."".$_POST['otp2']."".$_POST['otp3']."".$_POST['otp4']."".$_POST['otp5'];
        $current_time=time();

        if($rand == $otp)
        {
            if($current_time - $old_time > 60) // WE HAVE ONE MINUTE TO VERIFY OTP - could be less FRONT - BACK DECIDE
            {
                $error="Expired OTP";
            }
            else
            {
                $massage1=" 
                <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
                    <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
                        <h1>Welcome to Deskify, <span style='color: #fda521;'>$user_name</span>!</h1>
                    </div>
                    <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
                        <p style='color: #00000a;'>Dear <span style='color: #fda521;'> $user_name</span>,</p>
                        <p style='color: #00000a;'>Thank you for joining Deskify! We are thrilled to have you on board.</p>
                        <p style='color: #00000a;'>Here are some things you can do to get started:</p>
                        <ul>
                            <li style='color: #00000a'>Explore our features and tools to manage your tasks efficiently.</li>
                            <li style='color: #00000a'>Customize your profile to make it your own.</li>
                            <li style='color: #00000a'>Connect with other users and share your experiences.</li>
                        </ul>
                        <p style='color: #00000a;'>If you have any questions or need assistance, feel free to reach out to our support team at any time.</p>
                        <p style='color: #fda521;'>Happy tasking!</p>
                        <p style='color: #00000a;'>Best regards,<br>The Deskify Team</p>
                    </div>
                    <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
                        <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
                        <p style='color: #fffffa;'>Email: <a href='mailto:deskify0p@gmail.com' style='color: #fda521;'>deskify0@gmail.com</a></p>
                    </div>
                </body>
                    ";
                unset($_SESSION['otp']); 

                $mail->setFrom('deskify0@gmail.com', 'Deskify');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Welcome Aboard';
                $mail->Body=($massage1);
                $mail->send();

                $insert="INSERT INTO `users` (`name`, `email`, `phone`, `password`, `image`, `created_at`, `role_id`) 
                VALUES ('$user_name', '$email', '$phone', '$hash_password', 'default.png', '$timestamp', 1)";
                $run_insert=mysqli_query($connect,$insert);
                $_SESSION['logCHK'] = true;
                header("location:signup&login.php");
            }
        }
        else
            $error= "Incorrect OTP";
    }
}

if (isset($_POST['resend']))
{
    unset($_SESSION['otp']);
    $rand=rand(10000,99999);
    $massage=" 
    <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fffffa; color: #00000a;'>
        <div style='background-color: #0a7273; padding: 20px; text-align: center; color: #fffffa;'>
            <h1>Welcome to Deskify, $user_name!</h1>
        </div>
        <div style='padding: 20px; background-color: #fffffa; color: #00000a;'>
            <p style='color: #00000a;'>Dear <span style='color: #fda521;'>$user_name </span>,</p>
            <p style='color: #00000a;'>Thank you for registering with Deskify! Please use the OTP that we've resent below to verify your email address and complete your registration:</p>
            <p style='text-align: center; font-size: 24px; font-weight: bold; color: #fda521;'>$rand</p>
            <p style='color: #00000a;'>If you did not request this registration, please ignore this email.</p>
            <p style='color: #00000a;'>Best regards,<br>The Deskify Team</p>
        </div>
        <div style='background-color: #0a7273; padding: 10px; text-align: center; color: #fffffa;'>
            <p style='color: #fffffa;'>For support and updates, please visit our website or contact us via email.</p>
            <p style='color: #fffffa;'>Email: <a href='mailto:deskify0@gmail.com' style='color: #fda521;'>deskify0@gmail.com</a></p>
        </div>
    </body>
    ";
    $_SESSION['otp'] = $rand;
 
    $old_time=time();  // TIME AS IT IS , we will have 60 seconds upon submission
    $_SESSION['old_time']=$old_time;

    $mail->setFrom('deskify0p@gmail.com', 'Deskify');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Activation code';
    $mail->Body=($massage);
    $mail->send();

    header("location:verification_signup.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Verification</title>
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/verification.css">
</head>

<body>
    <div class="container-main">
    <!-- eldiv elkbeer -->
    <div class="otp-card">
        <h1>Verification Code</h1>
        <p>code sent to your E-mail</p>
        <?php if($error){?>
          <div class="warning">
          <?php echo $error;?>
          </div>
        <?php } ?>
        <!-- cardinfo -->
        <div class="otp-card-inputs">
        <form method="POST">
    
            <input type="text" maxlength="1" autofocus name="otp1">
            <input type="text" disabled maxlength="1" name="otp2">
            <input type="text" disabled maxlength="1" name="otp3">
            <input type="text" disabled maxlength="1" name="otp4">
            <input type="text" disabled maxlength="1" name="otp5">
        </div>
        <div class="tany">
        <p>Didn't get the otp? </p>
<!--            <button class="resbtn"  name="resend">resend</button>-->
            <button type="submit" name="resend" class="resend">resend</button>
        </div>
        <br>
        <button style=""  type="submit" name="submit" class="verify ">Verify</button>
    </div>
    </form>
</div>
    <script src="./js/verification.js"></script>
</body>

</html>
