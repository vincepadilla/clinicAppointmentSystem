<?php 
    session_start();
    include_once('config.php');
    $isLoggedIn = isset($_SESSION['userID']) ? true : false;
    $email = '';
    $fname = '';
    $lname = '';
    $phone = '';

    if ($isLoggedIn) {
        $user_id = $_SESSION['userID'];

        $query = "SELECT email, first_name, last_name, phone FROM users WHERE user_id = ?";
        $stmt = $con->prepare($query);

        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($email, $fname, $lname, $phone);
            $stmt->fetch();
            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Your Account</title>
    <link rel="stylesheet" href="profilestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="profile-wrapper">
        <div class="profile-container">
            <header class="profile-header">
                <h1>Profile Information</h1>
                <p>Update your personal information and address details</p>
            </header>

            <section class="profile-form-section">
                <form action="../index.php" method="POST" id="profileForm" class="profile-form">
                    <div class="form-section personal-info">
                        <h2><i class="fas fa-id-card"></i> Personal Information</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="fname">First Name</label>
                                <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($fname); ?>" required disabled>
                                <input type="hidden" name="fname" value="<?= $fname ?>">
                            </div>

                            <div class="form-group">
                                <label for="lname">Last Name</label>
                                <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($lname); ?>" required disabled>
                                <input type="hidden" name="lname" value="<?= $lname ?>">
                            </div>

                            <div class="form-group">
                                <label for="birthdate">Birthdate</label>
                                <input type="date" id="birthdate" name="birthdate" onchange="calculateAge()">
                                <input type="hidden" id="age" name="age" value="<?= $age ?>">
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required disabled>
                                <input type="hidden" name="email" value="<?= $email ?>">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required disabled>
                                <input type="hidden" name="phone" value="<?= $phone ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-section address-info">
                        <h2><i class="fas fa-home"></i> Address Information</h2>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="street">Street</label>
                                <input type="text" id="street" name="street" required>
                            </div>

                            <div class="form-group">
                                <label for="barangay">Barangay</label>
                                <input type="text" id="barangay" name="barangay" required>
                            </div>

                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" required>
                            </div>

                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script> 
        //Calculate Age
        function calculateAge() {
            const birthdate = document.getElementById("birthdate").value;
            if (!birthdate) return;

            const today = new Date();
            const birthDate = new Date(birthdate);
            let age = today.getFullYear() - birthDate.getFullYear();

            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            document.getElementById("age").value = age;
        }

        window.onload = function() {
            const today = new Date().toISOString().split("T")[0];
            document.getElementById("birthdate").setAttribute("max", today);
        };
    </script>
</body>
</html>
