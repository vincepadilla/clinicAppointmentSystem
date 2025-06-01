<?php
session_start();
include_once("config.php");

if (!isset($_SESSION['temp_user']) || !isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry'])) {
    echo "<script>alert('No registration session found. Please register again.'); window.location.href='register.php';</script>";
    exit;
}

if (isset($_POST['submit'])) {
    $entered_otp = trim($_POST['otp']);

    if (time() > $_SESSION['otp_expiry']) {
        unset($_SESSION['temp_user'], $_SESSION['otp'], $_SESSION['otp_expiry']);
        echo "<script>alert('OTP expired. Please register again.'); window.location.href='register.php';</script>";
        exit;
    }

    if ($entered_otp == $_SESSION['otp']) {
        $name = $_SESSION['temp_user']['name'];
        $fname = $_SESSION['temp_user']['fname'];
        $lname = $_SESSION['temp_user']['lname'];
        $email = $_SESSION['temp_user']['email'];
        $phone = $_SESSION['temp_user']['phone'];
        $password_hash = $_SESSION['temp_user']['password'];

        $query = "INSERT INTO users (name, first_name, last_name, email, phone, password_hash, role, email_verify) 
                  VALUES ('$name', '$fname', '$lname', '$email', '$phone', '$password_hash', 'patient', 'verified')";

        if (mysqli_query($con, $query)) {
            unset($_SESSION['temp_user'], $_SESSION['otp'], $_SESSION['otp_expiry']);
            echo "<script>alert('Registration successful. Please login.'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Database error during registration.');</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
                <header>OTP Verification</header>
                <form method="post">
                    <div class="field input">
                        <label for="otp">Enter OTP</label>
                        <input type="text" name="otp" id="otp" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="Verify">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
