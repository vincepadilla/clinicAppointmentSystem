<?php
session_start();
include_once("config.php");

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($con, trim($_POST['username']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));

    if (!empty($name) && !empty($password)) {

        // Admin shortcut
        if ($name == "admin" && $password == "admin") {
            header("Location: admin.php");
            exit();
        }

        // Check if user exists
        $query = "SELECT * FROM users WHERE name='$name'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password_hash'])) {
                // Set session
                $_SESSION['userID'] = $row['user_id'];
                $_SESSION['username'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['valid'] = true;

                $userID = $row['user_id'];

                // Check if patient info is complete
                $profileQuery = "SELECT first_name, last_name, birthdate, gender, email, phone, address 
                                 FROM tbl_patients WHERE user_id = $userID";
                $profileResult = mysqli_query($con, $profileQuery);

                if ($profileResult && mysqli_num_rows($profileResult) > 0) {
                    $profileData = mysqli_fetch_assoc($profileResult);

                    // Check if all profile fields are filled
                    if (
                        !empty($profileData['first_name']) &&
                        !empty($profileData['last_name']) &&
                        !empty($profileData['birthdate']) &&
                        !empty($profileData['gender']) &&
                        !empty($profileData['email']) &&
                        !empty($profileData['phone']) &&
                        !empty($profileData['address'])
                    ) {
                        header("Location: ../index.php"); 
                        exit();
                    }
                }

                header("Location: profile.php");
                exit();
            } else {
                $error = "Wrong password. Please try again.";
            }
        } else {
            $error = "No account found with that username.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!-- HTML part stays unchanged -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="loginpagestyle.css">
</head>
<body>
<div class="container">
    <div class="left">
        <div class="logo">
            <img src="../logo.png">
        </div>
    </div>
    <div class="right">
        <div class="form-box">
            <header>Login</header>
            <?php if (isset($error)) { echo "<div class='message'><p>$error</p></div><br>"; } ?>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login">
                </div>
                <div class="links">
                    Don't have an account? <a href="register.php">Sign Up Now</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
