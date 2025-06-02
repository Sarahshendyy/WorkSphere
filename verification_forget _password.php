<?php
include 'mail.php';
$error="";

if(!isset($_SESSION['otp'])) 
{
    header("Location: indexx.php");
}

$rand1=$_SESSION['otp'];
$email=$_SESSION['email'];
$old_time=$_SESSION['time'];

if(isset($_POST['submit']))
{
    if (!isset($_POST['otp1'], $_POST['otp2'], $_POST['otp3'], $_POST['otp4'], $_POST['otp5']))
        $error = "Please fill all OTP fields";
    else
    {
        $otp = $_POST['otp1'] . $_POST['otp2'] . $_POST['otp3'] . $_POST['otp4'] . $_POST['otp5'];
        $current_time = time();

        if ($rand1 == $otp)
        {
            if ($current_time - $old_time > 60)// WE HAVE 60 SECONDS TO VERIFY SINCE SUBMISSION TIME
                $error = "Expired OTP";
            else
                header("location:forget password.php");
        }
        else
            $error = "Incorrect OTP";
    }
}

if (isset($_POST['resend'])){
     $email=$_SESSION['email'];
     
     $select="SELECT *FROM `users` WHERE `email`='$email'";
     $runselect=mysqli_query($connect,$select);
     $fetch=mysqli_fetch_assoc($runselect);
     $user_name=$fetch['name'];

     if(mysqli_num_rows($runselect)>0){
     $rand1=rand(10000,99999);
     $massage = "
<body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
    <div style='background-color: #071739; padding: 20px; text-align: center; color: #CDD5DB;'>
        <h1>Password Reset Request</h1>
    </div>
    <div style='padding: 20px; background-color: #CDD5DB; color: #071739;'>
        <p>Dear <span style='color:rgb(207, 158, 97);'>$user_name</span>,</p>
        <p>We've received a request to reset your password. Please use the OTP that we've resent below to complete the process:</p>
        <p style='text-align: center; font-size: 24px; font-weight: bold; color: #E3C39D;'>$rand1</p>
        <p>If you did not request a password reset, please ignore this email or contact our support team for assistance.</p>
        <p>Best regards,<br>The WorkSphere Team</p>
    </div>
    <div style='background-color: #071739; padding: 10px; text-align: center; color: #CDD5DB;'>
        <p>For support and updates, please visit our website or contact us via email.</p>
        <p>Email: <a href='mailto:worksphere04@gmail.com' style='color:rgb(218, 166, 103); text-decoration: none;'>worksphere04@gmail.com</a></p>
    </div>
</body>
";

     $_SESSION['otp'] = $rand1;

     $old_time=time();  // AS IT IS
     $_SESSION['time']=$old_time;

     $mail->setFrom('worksphere04@gmail.com', 'WorkSphere');
     $mail->addAddress($email);
     $mail->isHTML(true);
     $mail->Subject = 'Password Reset OTP';
     $mail->Body=($massage);
     $mail->send();

     header("location:verification_forget _password.php");
     }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Page</title>
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
    <link rel="stylesheet" href="./css/verification.css">
</head>

<body>
    <!-- eldiv elkbeer -->
    <div class="container-main">
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
            <button  type="submit" name="resend" class="resend">resend</button>
        </div>
        <br>
        <button  type="submit" name="submit" class="verify">Verify</button>
    </div>

    </div>
    <script src="./js/verification.js"></script>
</body>

</html>