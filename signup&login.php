<?php
include 'mail.php';


$Smsg = '';
if (isset($_POST['sign-btn']))
{
  if(isset($_GET['LC']))
    $_GET['LC'] = '';
  $user_name =mysqli_real_escape_string($connect, $_POST['name']);
  $email =mysqli_real_escape_string($connect, $_POST['sign-email']);
  $password = mysqli_real_escape_string($connect, $_POST['sign-password']);
  $comfirm_password = mysqli_real_escape_string($connect, $_POST['confirm_password']);
  $phone_number = mysqli_real_escape_string($connect, $_POST['phone_number']);
  $hash_password = password_hash($password, PASSWORD_DEFAULT);
  $select = "SELECT * FROM users WHERE email='$email'";
  $run_select = mysqli_query($connect, $select);

  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $numbers = preg_match('@[0-9]@', $password);
  $character = preg_match('@[^\w]@', $password);

  $row = mysqli_num_rows($run_select);

  if (empty($user_name) || empty($email) || empty($password) || empty($comfirm_password) || empty($phone_number))
    $Smsg = "Please fill required data";
  else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $Smsg = "Invalid email format";
  elseif ($row > 0)
    $Smsg = "Email is already used";
  elseif ($uppercase < 1 || $lowercase < 1 || $numbers < 1 || $character < 1)
      $Smsg = "Password needs: numbers, uppercase, lowercase letters & a special character";
  elseif ($password != $comfirm_password)
    $Smsg = "Password doesn't match confirm password";
  elseif (strlen($phone_number) != 11)
    $Smsg = "Invalid phone number";
  else
  {
    $_SESSION['name'] = $user_name;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $hash_password;
    $_SESSION['phone_number'] = $phone_number;
    $rand = rand(10000, 99999);
    $_SESSION['otp'] = $rand; // OTP NOT RAND SESSION FOR REALS!!!!
    $massage = "
  <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #CDD5DB; color: #071739;'>
    <div style='background-color: #071739; padding: 20px; text-align: center; color: #CDD5DB;'>
      <h1>Welcome to WorkSphere, $user_name!</h1>
    </div>
    <div style='padding: 20px; background-color: #CDD5DB; color: #071739;'>
      <p>Dear <span style='color: #E3C39D;'>$user_name</span>,</p>
      <p>Thank you for registering with WorkSphere! Please use the OTP below to verify your email address and complete your registration:</p>
      <p style='text-align: center; font-size: 24px; font-weight: bold; color: #E3C39D;'>$rand</p>
      <p>If you did not request this registration, please ignore this email.</p>
      <p>Best regards,<br>The WorkSphere Team</p>
    </div>
    <div style='background-color: #071739; padding: 10px; text-align: center; color: #CDD5DB;'>
      <p>For support and updates, please visit our website or contact us via email.</p>
      <p>Email: <a href='mailto:worksphere50@gmail.com' style='color: #E3C39D; text-decoration: none;'>worksphere50@gmail.com</a></p>
    </div>
  </body>
";

    $old_time = time(); // TIME AS IT IS ON THE THE FORM SUBMISSION
    $_SESSION['old_time'] = $old_time;

    $mail->setFrom('worksphere50@gmail.com', 'WorkSphere');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Account Activation code';
    $mail->Body = ($massage);
    $mail->send();

    header("location:verification_signup.php");
  }
}

$Lmsg = "";
$loginError = false;
$remember = "";

if (isset($_POST['login'])){
  $email = mysqli_real_escape_string($connect, $_POST['log-email']);
  $password = mysqli_real_escape_string($connect, $_POST['log-password']);
  $remember = isset($_POST['remember']) ? $_POST['remember'] : ''; // Correctly handle the checkbox

  if (empty($email)){
      $Lmsg = "Email can't be left empty"; // FRONT SPECIAL styling - just in case someone disabled REQUIRED
      $loginError = true;
  }
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $Lmsg = "Invalid email format";
      $loginError = true;
  }
  else if (empty($password)) {
      $Lmsg = "Password can't be left empty";
      $loginError = true;
  }
  else{
    $FindEmailstmt = "SELECT * FROM users WHERE email = '$email'";
    $ExecFindEmail = mysqli_query($connect, $FindEmailstmt);

    if ($ExecFindEmail)
    {
      if (mysqli_num_rows($ExecFindEmail) > 0)
      {
        $data = mysqli_fetch_assoc($ExecFindEmail);
        $hashedPass = $data['password'];
        if (password_verify($password, $hashedPass))
        {
          $_SESSION['user_id'] = $data['user_id'];
          $_SESSION['role_id'] = $data['role_id'];
          $_SESSION['name'] = $data['name'];

          if ($remember) {
              setcookie("remember_email", $email, time() + 3600 * 24 * 365);
              setcookie("remember_password", $password, time() + 3600 * 24 * 365);
              setcookie("remember", $remember, time() + 3600 * 24 * 365);
          } else {
              // If 'Remember Me' is not checked, delete cookies
              setcookie("remember_email", "", time() - 3600);
              setcookie("remember_password", "", time() - 3600);
              setcookie("remember", "", time() - 3600);
          }

          if ($data['role_id'] == 1 || $data['role_id'] == 2) {
            header("Location: indexx.php");
          } elseif ($data['role_id'] == 3) {
            header("Location: workspace/workspaces_dashboard.php");
          } elseif ($data['role_id'] == 4) {
            header("Location: admin/admin_dashboard.php");
          }
          exit(); 
        }
        else{
          $Lmsg = "Incorrect Password"; // FRONT SPECIAL styling
          $loginError = true;
        }
      }
      else{
        $Lmsg = "Email isn't registered"; // FRONT SPECIAL styling
        $loginError = true;
      }

    }
  }
}

// Load cookies on page load. This should be done *before* the HTML is rendered.
$remember_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '';
$remember_password = isset($_COOKIE['remember_password']) ? $_COOKIE['remember_password'] : '';
$remember_checked = isset($_COOKIE['remember']) ? 'checked' : '';


?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Signup</title>
    <link rel="icon" type="image/x-icon" href="./img/keklogo.png">
    <link rel="stylesheet" type="" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main">
        <input type="checkbox" id="chk" aria-hidden="true" <?php if (($loginError) || isset($_SESSION['logCHK']) || (isset($_GET['LC'])) && $_GET['LC'] == 1 )echo 'checked'; ?>>

            <div class="signup">
                <form method="post">
                    <label for="chk" aria-hidden="true">Sign up</label>
                    <div class="warning <?php if(!empty($Smsg)) echo 'visible' ?>">
                        <?php if (!empty($Smsg)) echo $Smsg ?>
                    </div>
                    <input type="text" name="name" placeholder="User Name" required value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''?>">
                    <input type="email" name="sign-email" placeholder="Email" required value="<?php echo isset($_POST['sign-email']) ? $_POST['sign-email'] : ''?>">
                    <input type="password" name="sign-password" placeholder="Password" required value="<?php echo isset($_POST['sign-password']) ? $_POST['sign-password'] : ''?>">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''?>">
                    <input type="text" name="phone_number" placeholder="Phone Number" required value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>">
                    <button type="submit" name="sign-btn" id="s-btn">Sign up</button>
                </form>
            </div>

            <div class="login">
                <form class="form" method="post">
                    <label for="chk" aria-hidden="true">Login</label>
                    <div class="warning <?php if(!empty($Lmsg)) echo 'visible' ?>">
                        <?php if (!empty($Lmsg)) echo $Lmsg ?>
                    </div>
                    <input type="email" name="log-email" placeholder="Email" required value="<?php echo isset($_POST['log-email']) ? $_POST['log-email'] : $remember_email ?>">
                    <input type="password" name="log-password" placeholder="Password" required value="<?php echo isset($_POST['log-password']) ? $_POST['log-password'] : $remember_password ?>">
          <div class="checkbox-container">
                <input class="check" type="checkbox" name="remember" value="1" <?php echo $remember_checked; ?>>
                <span class="checkbox-text">Remember me</span>
          </div>
          <button class="loginbtn" type="submit" name="login">Login</button>
                    <a href="email_otp.php">Forget Password?</a>
                </form>
            </div>
    </div>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formFields = ['name', 'sign-email', 'phone_number', 'sign-password', 'confirm_password'];

            formFields.forEach(field => {
                const inputElement = document.querySelector(`[name="${field}"]`);
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                inputElement.insertAdjacentElement('afterend', errorElement);

                inputElement.addEventListener('input', function () {
                    validateField(field, inputElement, errorElement);
                });

                inputElement.addEventListener('blur', function () {
                    validateField(field, inputElement, errorElement);
                });
            });

            function validateField(field, inputElement, errorElement) {
                const value = inputElement.value.trim();
                errorElement.textContent = '';

                if (field === 'name') {
                    if (value === '') {
                        errorElement.textContent = 'User name is required';
                    }
                }

                if (field === 'sign-email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        errorElement.textContent = 'Invalid email format';
                    }
                }

                if (field === 'phone_number') {
                    if (!/^\d{11}$/.test(value)) {
                        errorElement.textContent = 'Phone number must be 11 digits';
                    }
                }

                if (field === 'sign-password') {
                    const uppercase = /[A-Z]/.test(value);
                    const lowercase = /[a-z]/.test(value);
                    const numbers = /[0-9]/.test(value);
                    const specialChar = /[^\w]/.test(value);

                    if (value.length < 8) {
                        errorElement.textContent = 'Password must be at least 8 characters';
                    } else if (!uppercase || !lowercase || !numbers || !specialChar) {
                        errorElement.textContent = 'Password must include uppercase, lowercase, number, and special character';
                    }
                }

                if (field === 'confirm_password') {
                    const password = document.querySelector('[name="sign-password"]').value;
                    if (value !== password) {
                        errorElement.textContent = 'Passwords do not match';
                    }
                }
            }
        });
    </script>
</body>

</html>


