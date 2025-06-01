<?php 
include_once("config.php");
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PhpMailer/src/Exception.php';
require '../PhpMailer/src/PHPMailer.php';
require '../PhpMailer/src/SMTP.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['username']));
    $fname = mysqli_real_escape_string($con, trim($_POST['fname']));
    $lname = mysqli_real_escape_string($con, trim($_POST['lname']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));

    if (!empty($name) && !empty($fname) && !empty($lname) && !empty($email) && !empty($phone) && !empty($password)) {

        if (strlen($password) < 6) {
            echo "<script>alert('Password must be at least 6 characters long.'); window.location.href='register.php';</script>";
            exit;
        }
        
        $verify_query = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

        if (mysqli_num_rows($verify_query) != 0) {
            echo "<script>alert('The email is already in use.'); window.location.href='register.php';</script>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999);

            // Store user info and OTP in session
            $_SESSION['temp_user'] = [
                'name' => $name,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashed_password
            ];
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 600; // 10 minutes

            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'vincehenrick.padilla0712@gmail.com'; 
                $mail->Password = 'xazs imyr lepb yjuq';    
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('your-email@gmail.com', 'Dental Buddies');
                $mail->addAddress($email, $name);
                $mail->Subject = 'OTP Verification';
                $mail->Body = "Hello $name,\n\nYour OTP is: $otp\nIt will expire in 10 minutes.";

                $mail->send();

                header("Location: otpVerification.php");
                exit;
            } catch (Exception $e) {
                echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="loginpagestyle.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="logo">
                <img src="../logo.png" alt="Dental Buddies Logo">
            </div>
        </div>

        <div class="divider"></div>

        <div class="right">
            <div class="box form-box">
                <header>Sign Up</header>
                <form action="" method="post">
                    <div class="field input">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" required>
                    </div>

                    <div class="field input">
                        <label for="firstName">First Name</label>
                        <input type="text" name="fname" id="fname" required>
                    </div>

                    <div class="field input">
                        <label for="lastName">Last Name</label>
                        <input type="text" name="lname" id="lname" required>
                    </div>

                    <div class="field input">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="field input">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" required>
                    </div>
                    <div class="field input">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="Register">
                    </div>
                    <div class="links">
                        Already a member? <a href="login.php">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
