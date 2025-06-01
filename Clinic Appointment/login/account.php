<?php
    session_start();

    include_once("config.php");
    define("TITLE", "My Account");
    include_once('../header.php');

    // Redirect to login if not logged in
    if (!isset($_SESSION['userID'])) {
        echo "You must be logged in to view this page.";
        echo " <a href='login.php'>Login here</a>";
        exit();
    }

    $userID = $_SESSION['userID'];
    $username = $_SESSION['username'];
    $email = $_SESSION['email'] ?? 'Not available';

    // Fetch the latest appointment using userID instead of patient_name
    $appointment = null;
    $stmt = $con->prepare("
        SELECT * FROM tbl_appointments 
        WHERE userID = ? 
        ORDER BY appointment_date DESC, appointment_time DESC 
        LIMIT 1
    ");
    $stmt->bind_param("i", $userID);  // "i" for integer type
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
    }
    $stmt->close();

    //Edit Profile Fetch
    $userInfo = [];
    $stmtUser = $con->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = ?");
    $stmtUser->bind_param("i", $userID);
    $stmtUser->execute();
    $resultUser = $stmtUser->get_result();
    if ($resultUser->num_rows > 0) {
        $userInfo = $resultUser->fetch_assoc();
    }
    $stmtUser->close();

    $patientInfo = [];
    $stmtPatient = $con->prepare("SELECT birthdate, gender, address FROM tbl_patients WHERE user_id = ?");
    $stmtPatient->bind_param("i", $userID);
    $stmtPatient->execute();
    $resultPatient = $stmtPatient->get_result();
    if ($resultPatient->num_rows > 0) {
        $patientInfo = $resultPatient->fetch_assoc();
    }
    $stmtPatient->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="accountstyle.css">
</head>
<body>

    <div class="container">
        <!-- Account action buttons at the top -->
        <div class="account-actions">
            <a href="#edit" class="btn btn-warning" onclick="openEditModal()">Edit Account</a>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </div>

        <div class="card">
            <h2 class="card-title">Account Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Username</strong>
                    <?php echo htmlspecialchars($username); ?>
                </div>
                <div class="info-item">
                    <strong>User ID</strong>
                    <?php echo htmlspecialchars($userID); ?>
                </div>
                <div class="info-item">
                    <strong>Email</strong>
                    <?php echo htmlspecialchars($email); ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Your Recent Appointment</h2>
            <?php if ($appointment) { ?>
                <div class="appointment-details">
                <p><strong>Appointment ID:</strong> <?php echo htmlspecialchars($appointment['appointment_id']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment['appointment_time']); ?></p>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service']); ?></p>
                    <p><strong>Dentist:</strong> <?php echo htmlspecialchars($appointment['dentist']); ?></p>
                    <?php
                        $statusClass = '';
                        switch ($appointment['status']) {
                            case 'Pending':
                                $statusClass = 'status-pending';
                                break;
                            case 'Confirmed':
                                $statusClass = 'status-confirmed';
                                break;
                            case 'Cancelled':
                                $statusClass = 'status-cancelled';
                                break;
                            default:
                                $statusClass = 'status-default';
                        }
                    ?>
                    <p class="status-badge <?php echo $statusClass; ?>">
                        <strong>Status:</strong> <?php echo htmlspecialchars($appointment['status']); ?>
                    </p>

                    <?php
                        if ($appointment['status'] == "Reschedule") {
                            echo "<p><strong>Your preferred date and time have been rescheduled due to some conflict or the dentist's availability. 
                            If you are not available on the new date and time, please click reschedule to select your preferred schedule.</strong></p>";

                        } else if ($appointment['status'] == "Pending" && 
                                ($appointment['service'] == 'Follow-Up' || $appointment['service'] == 'Brace-Adjustments')) {
                            echo "<p><strong>Please confirm if you're available. If not, click reschedule and select your preferred time.</strong></p>";

                        } else if ($appointment['status'] == "Pending") {
                            echo "<p><strong>Your appointment has been scheduled. Please wait for the confirmation to ensure the date and time are finalized.</strong></p>";

                        } else if ($appointment['status'] == "Confirmed") {
                            echo "<p><strong>Your appointment has been confirmed.</strong></p>";

                        } else if ($appointment['status'] == "Completed") {
                            echo "<p><strong>Your appointment has been completed.</strong></p>";

                        } else if($appointment['status'] == "No-Show") {
                            echo "<p><strong>Your appointment has been marked as a No-Show.</strong> Please make sure to update or cancel future appointments if you are unable to attend. 
                            Repeated no-shows may affect your eligibility for refunds. <br>Note: A second no-show will result in a 10% deduction from your appointment fee refund.</p>";
                            
                        }
                    ?>
                </div>
                
                <?php if ($appointment['status'] != 'Cancelled' && $appointment['status'] != 'Completed') {

                $isFollowUpOrAdjustments = in_array($appointment['service'], ['Follow-Up', 'Brace-Adjustments']);
                $status = $appointment['status'];

                // Determine button states
                if ($status == 'No-Show' && $isFollowUpOrAdjustments) {
                    $disableCancel = false;
                    $disableResched = false;
                    $disablePrint = true;
                    $disableConfirm = true;

                } else if($status == "Confirmed" && $isFollowUpOrAdjustments) {
                    $disableCancel = true;
                    $disableResched = false;
                    $disablePrint = true;
                    $disableConfirm = true;

                } else if ($isFollowUpOrAdjustments) {
                    $disableCancel = true;
                    $disableResched = false;
                    $disablePrint = true;
                    $disableConfirm = false;

                } else {
                    if ($status == 'Pending') {
                        $disableCancel = false;
                        $disableResched = false;
                        $disablePrint = true;
                        $disableConfirm = true;
                    } elseif ($status == 'Confirmed') {
                        $disableCancel = false;
                        $disableResched = true;
                        $disablePrint = false;
                        $disableConfirm = true;
                    } elseif ($status == 'Reschedule') {
                        $disableCancel = false;
                        $disableResched = false;
                        $disablePrint = true;
                        $disableConfirm = false;
                    } else {
                        $disableCancel = true;
                        $disableResched = true;
                        $disablePrint = true;
                        $disableConfirm = true;
                    }
                }
            ?>
                <div class="action-buttons">

                    <!-- Cancel Appointment -->
                    <a href="cancelAppointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                    class="btn btn-danger <?php echo $disableCancel ? 'disabled' : ''; ?>"
                    <?php echo $disableCancel ? 'onclick="return false;"' : "onclick=\"return confirm('Are you sure you want to cancel your appointment? Your payment will be refunded if already paid.');\""; ?>>
                        Cancel Appointment
                    </a>

                    <!-- Reschedule Appointment -->
                    <a href="reschedule.php?id=<?php echo $appointment['appointment_id']; ?>" 
                    class="btn btn-primary <?php echo $disableResched ? 'disabled' : ''; ?>" 
                    id="reschedBtn"
                    data-id="<?php echo $appointment['appointment_id']; ?>"
                    <?php echo $disableResched ? 'onclick="return false;"' : ''; ?>>
                        Reschedule Appointment
                    </a>

                    <!-- Print Receipt -->
                    <a href="printReceipt.php?id=<?php echo $appointment['appointment_id']; ?>" 
                    class="btn btn-print <?php echo $disablePrint ? 'disabled' : ''; ?>" 
                    <?php echo $disablePrint ? 'onclick="return false;"' : ''; ?>>
                        Print Receipt
                    </a>

                    <!-- Confirm -->
                    <a href="userConfirmed.php?id=<?php echo $appointment['appointment_id']; ?>" 
                    class="btn btn-confirm <?php echo $disableConfirm ? 'disabled' : ''; ?>"
                    <?php echo $disableConfirm ? 'onclick="return false;"' : ''; ?>>
                        Confirm
                    </a>

                </div>

            <?php } else { ?>
                <div class="action-buttons" style="opacity: 0.6; pointer-events: none;">
                    <a class="btn btn-danger disabled">Cancel Appointment</a>
                    <a class="btn btn-primary disabled">Reschedule Appointment</a>
                    <a class="btn btn-print disabled">Print Receipt</a>
                    <a class="btn btn-confirm disabled">Confirm</a>
                </div>
                <p><em>No further actions are available.</em></p>
            <?php } ?>

            
            <?php } else { ?>
                <div class="no-appointment">
                    <p>You have no recent appointments.</p>
                    <a href="../index.php" class="btn btn-primary">Book an Appointment</a>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h3>EDIT ACCOUNT</h3>
            <form action="updateAccount.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($userInfo['last_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>First Name:</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($userInfo['first_name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($userInfo['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($userInfo['phone'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Birthdate:</label>
                        <input type="date" name="birthdate" value="<?php echo htmlspecialchars($patientInfo['birthdate'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender" required>
                            <option value="Male" <?php if (($patientInfo['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if (($patientInfo['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group full-width">
                    <label>Address:</label>
                    <textarea name="address" required><?php echo htmlspecialchars($patientInfo['address'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">UPDATE ACCOUNT</button>
            </form>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="reschedModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeReschedModal()">&times;</span>
            <h3>Reschedule Appointment</h3>
            <form action="reschedule.php" method="POST">
                <input type="hidden" id="modalAppointmentID" name="appointment_id">

                <label for="new_date">Select New Date:</label>
                <input type="date" id="new_date" name="new_date" required>

                <label for="new_time">Select New Time:</label>
                <select id="new_time" name="new_time" required>
                    <option value="">Select Time</option>
                    <option value="8:00AM-9:00AM">8:00AM-9:00AM (Morning)</option>
                     <option value="9:00AM-10:00AM">9:00AM-10:00AM (Morning)</option>
                    <option value="10:00AM-11:00AM">10:00AM-11:00AM (Morning)</option>
                    <option value="11:00AM-12:00PM">11:00AM-12:00PM (Morning)</option>
                    <option value="1:00PM-2:00PM">1:00PM-2:00PM (Afternoon)</option>
                    <option value="2:00PM-3:00PM">2:00PM-3:00PM (Afternoon)</option>
                    <option value="3:00PM-4:00PM">3:00PM-4:00PM (Afternoon)</option>
                    <option value="4:00PM-5:00PM">4:00PM-5:00PM (Afternoon)</option>
                    <option value="5:00PM-6:00PM">5:00PM-6:00PM (Afternoon)</option>
                    <option value="6:00PM-7:00PM">6:00PM-7:00PM (Evening)</option>
                    <option value="7:00PM-8:00PM">7:00PM-8:00PM (Evening)</option>
                </select>

                <button type="submit" class="btn btn-success">CONFIRM SCHEDULE</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function checkRescheduleAvailability() {
        const selectedDate = $("#new_date").val();
        const appointmentID = $("#modalAppointmentID").val();

        if (!selectedDate || !appointmentID) return;

        $.ajax({
            url: 'getAppointmentsUserResched.php',
            type: 'GET',
            data: { new_date: selectedDate, appointment_id: appointmentID },
            dataType: 'json',
            success: function(bookedSlots) {
                // Enable all options first
                $("#new_time option").prop("disabled", false);

                // Disable booked slots
                $.each(bookedSlots, function(index, slot) {
                    $("#new_time option[value='" + slot + "']").prop("disabled", true);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching availability data:", error);
            }
        });
    }

    $("#new_date").on("change", checkRescheduleAvailability);
        document.getElementById('reschedBtn').addEventListener('click', function(event) {
            event.preventDefault(); // Stop navigation
            const appointmentID = this.getAttribute('data-id');
            document.getElementById('modalAppointmentID').value = appointmentID;

            // Set min date to tomorrow
            const dateInput = document.getElementById('new_date');
            const today = new Date();
            today.setDate(today.getDate() + 1); // add 1 day
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            dateInput.min = `${yyyy}-${mm}-${dd}`;

            openReschedModal();
        });

        function openReschedModal() {
            document.getElementById("reschedModal").style.display = "block";
        }

        function closeReschedModal() {
            document.getElementById("reschedModal").style.display = "none";
        }

        // Optional: Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById("reschedModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        //Edit Profile
        function openEditModal() {
            const modal = document.getElementById("editModal");
            modal.style.display = "flex";
            setTimeout(() => {
                modal.classList.add("show");
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById("editModal");
            modal.classList.remove("show");
            setTimeout(() => {
                modal.style.display = "none";
            }, 300); // Match this with your CSS transition duration
        }

        window.onclick = function(event) {
            const modal = document.getElementById("editModal");
            if (event.target === modal) {
                closeEditModal();
            }
        };

    </script>

    <?php include_once('../footer.php');?>

</body>
</html>