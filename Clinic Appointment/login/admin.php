<?php
    session_start();
    include_once("config.php");

    $sql = "SELECT appointment_id, userID, patient_name, service, dentist, appointment_date, appointment_time, status FROM tbl_appointments ORDER BY appointment_date ASC";
    $result = mysqli_query($con, $sql);
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Appointments</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="adminstyle.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>

    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../logo.png">
        </div>
        <nav class="sidebar-nav">
        <a href="#" class="active" onclick="showSection('dashboard')"><i class="fa fa-tachometer"></i> Dashboard</a>
            <a href="#appointment"onclick="showSection('appointment')"><i class="fas fa-calendar-check"></i> Appointments</a>
            <a href="#services" onclick="showSection('services')"><i class="fa-solid fa-teeth"></i> Services</a>
            <a href="#patients" onclick="showSection('patients')"><i class="fa-solid fa-hospital-user"></i> Patients</a>
            <a href="#dentists" onclick="showSection('dentists')"><i class="fa-solid fa-user-doctor"></i> Dentists & Staff</a>
            <a href="#payments" onclick="showSection('payment')"><i class="fa-solid fa-money-bill"></i> Payment Transactions</a> 
            <a href="#reports" onclick="showSection('reports')"><i class="fa-solid fa-square-poll-vertical"></i> Reports</a> 
            <a href="login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </div>

    <?php
        // Get total number of appointments
        $appointmentCountQuery = "SELECT COUNT(*) AS total_appointments FROM tbl_appointments";
        $appointmentCountResult = mysqli_query($con, $appointmentCountQuery);
        $appointmentCount = mysqli_fetch_assoc($appointmentCountResult)['total_appointments'];

        //Get total number of services
        $servicesCountQuery = "SELECT COUNT(*) AS total_services FROM tbl_services";
        $servicesCountResult = mysqli_query($con, $servicesCountQuery);
        $servicesCount = mysqli_fetch_assoc($servicesCountResult)['total_services'];

        // Get number of active dentists
        $activeDentistQuery = "SELECT COUNT(*) AS active_dentists FROM tbl_dentists WHERE status = 'active'";
        $activeDentistResult = mysqli_query($con, $activeDentistQuery);
        $activeDentists = mysqli_fetch_assoc($activeDentistResult)['active_dentists'];

        // Get today's appointments
        $todaysAppointmentsQuery = "SELECT appointment_id, patient_name, service, dentist, appointment_date, appointment_time, status FROM tbl_appointments WHERE appointment_date = CURDATE() AND status != 'Cancelled' ORDER BY appointment_time ASC";
        $todaysAppointmentsResult = mysqli_query($con, $todaysAppointmentsQuery);
        $todaysAppointmentsCount = mysqli_num_rows($todaysAppointmentsResult);

        // Get today's appointment summary by hour
        $summaryQuery = "SELECT TIME_FORMAT(appointment_time, '%H:00') AS hour, COUNT(*) AS total FROM tbl_appointments WHERE appointment_date = CURDATE() GROUP BY hour ORDER BY hour";
        $summaryResult = mysqli_query($con, $summaryQuery);

        $appointmentHours = [];
        $appointmentCounts = [];

        while ($row = mysqli_fetch_assoc($summaryResult)) {
            $appointmentHours[] = $row['hour'];
            $appointmentCounts[] = $row['total'];
        }

        //Upcoming Appointments
        $upcomingAppointmentsQuery = "SELECT * FROM tbl_appointments WHERE appointment_date > CURDATE() AND status != 'Cancelled' ORDER BY appointment_date ASC, appointment_time ASC LIMIT 5";
        $upcomingAppointmentsResult = mysqli_query($con, $upcomingAppointmentsQuery);
        $upcomingAppointmentsCount = mysqli_num_rows($upcomingAppointmentsResult);
        
    ?>

    <div class="main-content" id="dashboard">
        <h1>Dashboard Overview</h1>
        <p>Welcome Admin!</p>

        <!-- Stats Section -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-calendar-check fa-2x"></i>
                <div class="stat-info">
                    <h3><?php echo $appointmentCount; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>

            <div class="stat-card">
                <i class="fas fa-user-md fa-2x"></i>
                <div class="stat-info">
                    <h3><?php echo $activeDentists; ?></h3>
                    <p>Active Dentists</p>
                </div>
            </div>

            <div class="stat-card">
                <i class="fa-solid fa-teeth"></i>
                <div class="stat-info">
                    <h3><?php echo $servicesCount; ?></h3>
                    <p>Total Services</p>
                </div>
            </div>
        </div>

        <!-- Appointments Side-by-Side Layout -->
        <div class="appointments-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <!-- Today's Appointments Section -->
            <div class="today-appointments">
                <h2>Today's Appointments (<?php echo $todaysAppointmentsCount; ?>)</h2>

                <?php if ($todaysAppointmentsCount > 0) { ?>
                    <div class="appointments-table">
                        <div class="appointments-table-header">
                            <div class="appointments-table-column"><strong>Time</strong></div>
                            <div class="appointments-table-column"><strong>Patient Name</strong></div>
                            <div class="appointments-table-column"><strong>Service</strong></div>
                            <div class="appointments-table-column"><strong>Dentist</strong></div>
                            <div class="appointments-table-column"><strong>Status</strong></div>
                        </div>

                        <?php while ($row = mysqli_fetch_assoc($todaysAppointmentsResult)) { ?>
                            <div class="appointments-table-row">
                                <div class="appointments-table-column"><?php echo htmlspecialchars($row['appointment_time']); ?></div>
                                <div class="appointments-table-column">
                                    <?php echo htmlspecialchars($row['patient_name']); ?>
                                </div>
                                <div class="appointments-table-column">
                                    <?php echo htmlspecialchars($row['service']); ?>
                                </div>

                                <div class="appointments-table-column">
                                    <?php echo htmlspecialchars($row['dentist']); ?>
                                </div>

                                <div class="appointments-table-column"><?php echo htmlspecialchars($row['status']); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>No appointments scheduled for today.</p>
                <?php } ?>
            </div>

            <div class="upcoming-appointments">
                <h2>Upcoming Appointments (<?php echo $upcomingAppointmentsCount; ?>)</h2>

                <?php if ($upcomingAppointmentsCount > 0) { ?>
                    <div class="appointments-table">
                        <div class="appointments-table-header">
                            <div class="appointments-table-column"><strong>Date</strong></div>
                            <div class="appointments-table-column"><strong>Time</strong></div>
                            <div class="appointments-table-column"><strong>Patient</strong></div>
                        </div>

                        <?php while ($row = mysqli_fetch_assoc($upcomingAppointmentsResult)) { ?>
                            <div class="appointments-table-row">
                                <div class="appointments-table-column"><?php echo date('M j', strtotime($row['appointment_date'])); ?></div>
                                <div class="appointments-table-column"><?php echo htmlspecialchars($row['appointment_time']); ?></div>
                                <div class="appointments-table-column">
                                    <?php echo htmlspecialchars($row['patient_name']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>No upcoming appointments.</p>
                <?php } ?>
            </div>
        </div>

        <div class="graph-container" style="margin-top: 30px;">
            <h3>Appointment Time Summary</h3>
            <canvas id="appointmentSummaryChart" width="500" height="200"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const timeLabels = <?php echo json_encode($appointmentHours); ?>;
            const appointmentData = <?php echo json_encode($appointmentCounts); ?>;

            const ctx = document.getElementById('appointmentSummaryChart').getContext('2d');

            // Predefined set of 5 colors
            const barColors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
            ];

            // Repeat the color set if there are more than 5 bars
            const colorsForBars = appointmentData.map((_, index) => barColors[index % barColors.length]);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: timeLabels,
                    datasets: [{
                        label: 'Appointments per Hour',
                        data: appointmentData,
                        backgroundColor: colorsForBars,  // Assign fixed 5 colors for the bars
                        borderColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Today\'s Appointment Distribution by Time'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true, 
                            stepSize: 1,         
                            title: {
                                display: true,
                                text: 'Number of Patients'
                            },
                            ticks: {
                                callback: function(value) {
                                    return Number.isInteger(value) ? value : '';  // Show only whole numbers
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time (Hourly)'
                            }
                        }
                    }
                }
            });
        </script>

    </div>

    <!--Appointment Details -->
    <div class="main-content" id="appointment" style="display:none">
        <div class="container">
            <h2><i class="fas fa-calendar-alt"></i> ADMIN</h2>
            
            <div class="filter-container">
                <div class="filter-group">
                    <label for="filter-date"><i class="fas fa-calendar-day"></i> Date:</label>
                    <input type="date" id="filter-date" onchange="filterAppointments()">
                </div>
                
                <div class="filter-group">
                    <label for="filter-status"><i class="fas fa-filter"></i> Status:</label>
                    <select id="filter-status" onchange="filterAppointments()">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="reschedule">Reschedule</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="cancelled">No-Show</option>
                    </select> 
                </div>

                <button class="btn btn-primary" id="openAddAppointmentBtn">
                    <i class="fa-solid fa-calendar-plus"></i> Add Appointment
                </button>
                
                <button class="btn btn-accent" onclick="printAppointments()">
                    <i class="fas fa-print"></i> Print
                </button>
                
            </div>

            <div class="table-responsive">
                <table id="appointments-table">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient Name</th>
                            <th>Service</th>
                            <th>Dentist</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                                $statusClass = 'status-' . strtolower($row['status']);
                        ?>
                            <tr class="appointment-row" data-date="<?php echo $row['appointment_date']; ?>" data-status="<?php echo strtolower($row['status']); ?>">
                                <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['service']); ?></td>
                                <td><?php echo htmlspecialchars($row['dentist']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($row['appointment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                <td><span class="status <?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <form action="confirmAppointment.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                            <button type="submit" class="action-btn btn-primary-confirmed" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <a href="#" 
                                            class="action-btn btn-accent" 
                                            id="reschedBtn<?= $row['appointment_id'] ?>" 
                                            data-id="<?= $row['appointment_id'] ?>"
                                            onclick="openReschedModalWithID(this)"
                                            title="Reschedule">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>

                                        <form action="completeAppointment.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                            <button type="submit" class="action-btn btn-completed" title="Mark as Completed">
                                                <i class="fa-solid fa-calendar-check"></i>
                                            </button>
                                        </form>


                                        <form action="noshowAppointment.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                            <button class="action-btn btn-danger" title="No-Show">
                                                <i class="fa-regular fa-eye-slash"></i></button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                            <tr>
                                <td colspan="8" class="no-data">
                                    <i class="fas fa-calendar-times fa-2x"></i>
                                    <p>No appointments found</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <!-- Add Appointment Modal --> 
    <?php
    $patientsQuery = "
        SELECT p.patient_id, p.user_id, p.first_name, p.last_name
        FROM tbl_patients p
        INNER JOIN (
            SELECT userID, MAX(appointment_id) AS latest_appointment_id
            FROM tbl_appointments
            GROUP BY userID
        ) AS latest ON p.user_id = latest.userID
        INNER JOIN tbl_appointments a ON a.appointment_id = latest.latest_appointment_id
        WHERE a.status = 'Completed'
        ORDER BY p.patient_id ASC
    ";

    $patientsResult = mysqli_query($con, $patientsQuery);

    $patientsMap = [];
    while ($row = mysqli_fetch_assoc($patientsResult)) {
        $fullName = $row['first_name'] . ' ' . $row['last_name'];
        $patientsMap[$row['user_id']] = $fullName;
    }
    ?>

    <div id="addAppointmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>ADD NEW APPOINTMENT</h3>

            <form action="addAppointment.php" method="POST">
                <!-- Row 1: Patient ID and Patient Name -->
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="userID">Patient ID:</label>
                        <select name="userID" id="userID" onchange="updatePatientName()" required>
                            <option value="">Select Patient ID</option>
                            <?php
                            foreach ($patientsMap as $id => $name) {
                                echo "<option value=\"$id\">$id</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label for="patient_name">Patient Name:</label>
                        <input type="text" name="patient_name" id="patient_name" readonly required>
                    </div>
                </div>

                <!-- Row 2: Service and Purpose -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="services">Services:</label>
                        <select name="needServices" id="needServices" required>
                            <option value="">Select Services</option>
                            <option value="Follow-Up">Follow-up Checkup</option>
                            <option value="Brace-Adjustments">Adjustments</option>
                        </select>
                    </div>

                    <div style="flex: 1;">
                        <label for="appointment_date">Appointment Date:</label>
                        <input type="date" id="appointment_date" name="appointment_date" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <!-- Row 3: Date and Time -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="appointment_time">Appointment Time:</label>
                        <select name="appointment_time" id="appointment_time" required>
                            <option value="">Select Time</option>
                            <option value="firstBatch">Morning (8AM-9AM)</option>
                            <option value="secondBatch">Morning (9AM-10AM)</option>
                            <option value="thirdBatch">Morning (10AM-11AM)</option>
                            <option value="fourthBatch">Afternoon (11AM-12PM)</option>
                            <option value="fifthBatch">Afternoon (1PM-2PM)</option>
                            <option value="sixthBatch">Afternoon (2PM-3PM)</option>
                            <option value="sevenBatch">Afternoon (3PM-4PM)</option>
                            <option value="eightBatch">Afternoon (4PM-5PM)</option>
                            <option value="nineBatch">Afternoon (5PM-6PM)</option>
                            <option value="tenBatch">Evening (6PM-7PM)</option>
                            <option value="lastBatch">Evening (7PM-8PM)</option>
                        </select>
                    </div>

                    <div style="flex: 1;">
                        <label for="dentist">Dentist:</label>
                        <select name="dentistSelect" id="dentistSelect" required>
                            <option value="">Select Dentist</option>
                            <option value="Dr. Allen">Doc. Allen Lobregat</option>
                            <option value="Dr. Carol">Doc. Carol Ong</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 15px;">
                    <button type="submit" class="btn btn-success">Save Appointment</button>
                </div>
            </form>
        </div>
    </div>

   <!-- Reschedule Modal -->
    <div id="reschedModal" class="modal">
        <div class="modal-content">
            <h3>Reschedule Appointment</h3>
            <form action="rescheduleAppointment.php" method="POST">
                <input type="hidden" id="modalAppointmentID" name="appointment_id">
                
                <label for="new_date">Select New Date:</label>
                <input type="date" id="new_date_resched" name="new_date_resched" required min="<?= date('Y-m-d') ?>">

                <label for="new_time">Select New Time:</label>
                <select id="new_time_resched" name="new_time_resched" required>
                    <option value="">Select Time</option>
                        <option value="firstBatch">Morning (8AM-9AM)</option>
                        <option value="secondBatch">Morning (9AM-10AM)</option>
                        <option value="thirdBatch">Morning (10AM-11AM)</option>
                        <option value="fourthBatch">Afternoon (11AM-12PM)</option>
                        <option value="fifthBatch">Afternoon (1PM-2PM)</option>
                        <option value="sixthBatch">Afternoon (2PM-3PM)</option>
                        <option value="sevenBatch">Afternoon (3PM-4PM)</option>
                        <option value="eightBatch">Afternoon (4PM-5PM)</option>
                        <option value="nineBatch">Afternoon (5PM-6PM)</option>
                        <option value="tenBatch">Evening (6PM-7PM)</option>
                        <option value="lastBatch">Evening (7PM-8PM)</option>
                </select>

                <button type="submit" class="btn btn-success">CONFIRM SCHEDULE</button>
            </form>
        </div>
    </div>

    
    <!-- Services -->
    <div id="services" class="main-content" style="display:none;">
        <div class="container">
            <h2><i class="fas fa-procedures"></i> SERVICES</h2>
            <button class="btn btn-primary" id="openAddServiceBtn">ADD NEW SERVICE</button>

            <?php
                $servicesSql = "SELECT service_id, service_name, price, description FROM tbl_services";
                $result = mysqli_query($con, $servicesSql);
            ?>

            <div class="table-responsive">
                <table id="services-table">
                    <thead>
                        <tr>
                            <th>Service ID</th>
                            <th>Service Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['service_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn btn-primary-edit" title="Edit"><i class="fas fa-edit"></i></button>

                                        <form action="deleteService.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="service_id" value="<?php echo $row['service_id']; ?>">
                                            <button type="submit" class="action-btn btn-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                    <p>No services found</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>EDIT SERVICE</h3>
            <form id="editServiceForm" method="POST" action="updateService.php">
                <input type="hidden" name="service_id" id="editServiceId">

                <label for="editServiceName">Service Name:</label>
                <input type="text" name="service_name" id="editServiceName" required>

                <label for="editDescription">Description:</label>
                <input type="text" name="description" id="editDescription"required>

                <label for="editPrice">Price (₱):</label>
                <input type="number" name="price" id="editPrice" step="0.01" required>
                
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">Update Service</button>
                    <button type="button" onclick="closeEditModal()" class="modal-close-btn">Close</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>ADD SERVICE</h3>
            <form action="addServices.php" method="POST">
                <label for="serviceName">Service Name:</label>
                <select name="serviceName" required>
                    <option value="">Select Category</option>
                    <option value="General Dentistry">General Dentistry</option>
                    <option value="Orthodontics">Orthodontics</option>
                </select>

                <label for="description">Description:</label>
                <input type="text" name="description" required>

                <label for="price">Price (₱):</label>
                <input type="number" name="price" step="0.01" required>

                <button type="submit" class="btn btn-success">Add Service</button>
            </form>
        </div>
    </div>


    <!-- Patients -->
    <div id="patients" class="main-content" style="display:none;">
        <div class="container">
            <h2><i class="fa-solid fa-hospital-user"></i> PATIENTS</h2>

            <?php
                $patientSql = "SELECT patient_id, first_name, last_name, age, gender, email, phone, address FROM tbl_patients";
                $result = mysqli_query($con, $patientSql);
            ?>

            <div class="table-responsive">
                <table id="services-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Email Address</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['age']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn btn-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn btn-completed" title="Archive"><i class="fa-solid fa-box-archive"></i></button>
                                    </div>
                                    
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                            <tr>
                                <td colspan="8" class="no-data">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                    <p>No Patients found</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

    <!-- Edit Patient Modal -->
    <div id="editPatientModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>EDIT PATIENT</h3>
            <form id="editPatientForm" method="POST" action="updatePatient.php">

                <input type="hidden" name="patient_id" id="editPatientId">

                <!-- First row: First and Last Name -->
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="editFirstName">First Name:</label>
                        <input type="text" name="first_name" id="editFirstName" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editLastName">Last Name:</label>
                        <input type="text" name="last_name" id="editLastName" required>
                    </div>
                </div>

                <!-- Add Age below First and Last Name -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="editAge">Age:</label>
                        <input type="number" name="age" id="editAge" min="0" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editGender">Gender:</label>
                        <select name="gender" id="editGender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <!-- Second row: Gender and Email -->
                <div style="margin-top: 10px; display: flex; gap: 10px;">

                    <div style="flex: 1;">
                        <label for="editEmail">Email:</label>
                        <input type="text" name="email" id="editEmail" required>
                    </div>
                </div>

                <!-- Third row: Phone and Address -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="editPhone">Phone:</label>
                        <input type="text" name="phone" id="editPhone" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editAddress">Address:</label>
                        <input type="text" name="address" id="editAddress" required>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="submit" class="btn btn-success">Update Patient</button>
                    <button type="button" onclick="closeEditPatientModal()" class="modal-close-btn">Close</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Dentist -->
    <div id="dentists" class="main-content" style="display:none;">
        <div class="container">
            <h2><i class="fa-solid fa-user-doctor"></i> DENTISTS AND STAFF</h2>
            <button class="btn btn-primary" id="openAddDentistBtn">ADD NEW DENTIST/STAFF</button>

            <?php
                $dentistSql = "SELECT dentist_id, first_name, last_name, specialization, email, phone, status FROM tbl_dentists";
                $result = mysqli_query($con, $dentistSql);
            ?>

            <div class="table-responsive">
                <table id="services-table">
                    <thead>
                        <tr>
                            <th>Dentist ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['dentist_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn btn-primary-editStaff" title="Edit"><i class="fas fa-edit"></i></button>
                                        
                                        <form action="deleteStaff.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this staff?');">
                                            <input type="hidden" name="dentist_id" value="<?php echo $row['dentist_id']; ?>">
                                            <button type="submit" class="action-btn btn-deleteStaff" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                    <p>No Dentists found</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="addDentistModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>ADD DENTIST</h3>
            <form action="addStaff.php" method="POST">
                
                <!-- First row: First and Last Name -->
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="addFirstName">First Name:</label>
                        <input type="text" name="first_name" id="addFirstName" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="addLastName">Last Name:</label>
                        <input type="text" name="last_name" id="addLastName" required>
                    </div>
                </div>

                <!-- Second row: Specialization and Status -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="addSpecialization">Specialization:</label>
                        <input type="text" name="specialization" id="addSpecialization" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="addStatus">Status:</label>
                        <select name="status" id="addStatus" required>
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Third row: Email and Phone -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="addEmail">Email:</label>
                        <input type="text" name="email" id="addEmail" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="addPhone">Phone:</label>
                        <input type="text" name="phone" id="addPhone" required>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">Add Staff</button>
                    <button type="button" onclick="closeDentistModal()" class="modal-close-btn">ClOSE</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Dentist Modal -->
    <div id="editDentistModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>EDIT DENTIST</h3>
            <form id="editDentistForm" method="POST" action="updateStaff.php">
                
                <input type="hidden" name="dentist_id" id="editDentistId">

                <!-- First row: First and Last Name -->
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;">
                        <label for="editDentistFirstName">First Name:</label>
                        <input type="text" name="first_name" id="editDentistFirstName" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editDentistLastName">Last Name:</label>
                        <input type="text" name="last_name" id="editDentistLastName" required>
                    </div>
                </div>

                <!-- Second row: Specialization and Status -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="editDentistSpecialization">Specialization:</label>
                        <input type="text" name="specialization" id="editDentistSpecialization" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editDentistStatus">Status:</label>
                        <select name="status" id="editDentistStatus" required>
                            <option value="">Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Third row: Email and Phone -->
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <div style="flex: 1;">
                        <label for="editDentistEmail">Email:</label>
                        <input type="text" name="email" id="editDentistEmail" required>
                    </div>
                    <div style="flex: 1;">
                        <label for="editDentistPhone">Phone:</label>
                        <input type="text" name="phone" id="editDentistPhone" required>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">Update Details</button>
                    <button type="button" onclick="closeEditDentistModal()" class="modal-close-btn">CLOSE</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Payment Transactions -->
    <div id="payment" class="main-content" style="display:none;">
        <div class="container">
            <h2><i class="fa-solid fa-money-bill"></i> PAYMENT TRANSACTION</h2>

            <?php
                $paymentSql = "SELECT payment_id ,appointment_id, user_id, method, account_name, account_number, amount, reference_no, proof_image, status FROM tbl_payment";
                $result = mysqli_query($con, $paymentSql);
            ?>

            <div class="table-responsive">
                <table id="services-table">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Appointment ID</th>
                            <th>Method</th>
                            <th>Account Name</th>
                            <th>Account Number</th>
                            <th>Amount</th>
                            <th>References No.</th>
                            <th>Proof</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['method']); ?></td>
                                <td><?php echo htmlspecialchars($row['account_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['account_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['reference_no']); ?></td>

                                <td>
                                    <?php if (!empty($row['proof_image'])): ?>
                                        <?php 
                                        // Remove any leading slashes or uploads/ from the path
                                        $clean_path = ltrim($row['proof_image'], '/');
                                        $clean_path = str_replace('uploads/', '', $clean_path);
                                        $image_path = '/uploads/' . $clean_path;
                                        ?>
                                        <button type="button" onclick="viewImage('<?php echo htmlspecialchars($image_path); ?>')" 
                                            style="background:none; border:none; color:#007bff; text-decoration:underline; cursor:pointer;">
                                            View Image
                                        </button>
                                        
                                    <?php else: ?>
                                        <span>No Image</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <form action="confirmPayment.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                                <button type="submit" class="action-btn btn-primary-confirmedPayment" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                        </form>

                                        <form action="failedPayment.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>">
                                                <button type="submit" class="action-btn btn-danger" title="Mark as failed">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                            <tr>
                                <td colspan="10" class="no-data">
                                    <i class="fas fa-exclamation-circle fa-2x"></i>
                                    <p>No Payment found</p>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center;">
        <span onclick="closeModal()" style="position:absolute; top:20px; right:30px; font-size:30px; color:white; cursor:pointer;">&times;</span>
        <img id="modalImage" src="" alt="Proof Image" style="max-width:90%; max-height:80%; border:5px solid white; box-shadow:0 0 10px black;">
    </div>


    <!-- Reports -->
<div id="reports" class="main-content" style="display:none;">
    <div class="container" style="width: 95%; height:auto;">
        <h2><i class="fa-solid fa-square-poll-vertical"></i> REPORTS</h2>

        <!-- Report Type Dropdown -->
        <label for="reportType">Select Report:</label>
        <select id="reportType" onchange="toggleReportSections()">
            <option value="service">Monthly Service Distribution</option>
            <option value="appointments">Appointments Per Day</option>
            <option value="financial">Financial Report</option>
        </select>

        <!-- Monthly Service Distribution Section -->
        <div id="serviceReport">
            <?php
                $monthlyServiceData = [];

                for ($month = 1; $month <= 12; $month++) {
                    $sql = "SELECT service, COUNT(*) AS count
                            FROM tbl_appointments
                            WHERE MONTH(appointment_date) = $month AND YEAR(appointment_date) = 2025
                            GROUP BY service";

                    $result = mysqli_query($con, $sql);

                    $services = [];
                    $counts = [];

                    while ($row = mysqli_fetch_assoc($result)) {
                        $services[] = $row['service'];
                        $counts[] = (int)$row['count'];
                    }

                    $monthlyServiceData[$month] = [
                        'labels' => $services,
                        'counts' => $counts
                    ];
                }
            ?>

            <div class="graph-container" style="margin-top: 30px;">
                <h3>Monthly Service Distribution</h3>
                <label for="monthSelect">Select Month:</label>
                <select id="monthSelect" onchange="updateChart()">
                    <?php
                    for ($m = 1; $m <= 12; $m++) {
                        $monthName = date('F', mktime(0, 0, 0, $m, 10));
                        echo "<option value='$m'>$monthName</option>";
                    }
                    ?>
                </select>
                <canvas id="servicePieChart" width="500" height="200"></canvas>
                <div id="colorGuide" style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;"></div>
            </div>
        </div>

        <!-- Appointments Per Day Section -->
        <div id="appointmentsReport" style="display:none;">
            <h3>Appointments Per Day</h3>
            <?php
                $sql = "SELECT appointment_date, COUNT(*) as count FROM tbl_appointments GROUP BY appointment_date ORDER BY appointment_date";
                $result = mysqli_query($con, $sql);
                $dates = [];
                $counts = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $dates[] = $row['appointment_date'];
                    $counts[] = (int)$row['count'];
                }
            ?>
            <canvas id="appointmentsBarChart" width="500" height="200"></canvas>
        </div>

        <!-- Financial Report Section -->
        <div id="financialReport" style="display:none;">
            <h3>Financial Report</h3>
            <?php
                // Make sure to join tbl_payment with tbl_appointment on appointment_id
                $sql = "SELECT a.appointment_date, SUM(p.amount) as total_amount
                        FROM tbl_payment p
                        INNER JOIN tbl_appointments a ON p.appointment_id = a.appointment_id
                        WHERE p.status = 'paid'
                        GROUP BY a.appointment_date
                        ORDER BY a.appointment_date";

                $result = mysqli_query($con, $sql);
                $datesPaid = [];
                $amountsPaid = [];

                while ($row = mysqli_fetch_assoc($result)) {
                    $datesPaid[] = $row['appointment_date'];
                    $amountsPaid[] = (float)$row['total_amount'];
                }
            ?>
            <canvas id="financialBarChart" width="500" height="200"></canvas>
        </div>

        <!-- Chart JS Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const monthlyData = <?php echo json_encode($monthlyServiceData); ?>;
            const serviceCtx = document.getElementById('servicePieChart').getContext('2d');
            const appointmentsCtx = document.getElementById('appointmentsBarChart').getContext('2d');
            const financialCtx = document.getElementById('financialBarChart').getContext('2d');
            const colorGuide = document.getElementById('colorGuide');

            const colorPalette = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                                '#9966FF', '#FF9F40', '#8BC34A', '#E91E63',
                                '#00BCD4', '#9C27B0', '#CDDC39', '#607D8B'];

            let pieChart, appointmentsChart, financialChart;

            function updateChart() {
                const selectedMonth = document.getElementById('monthSelect').value;
                const data = monthlyData[selectedMonth];

                colorGuide.innerHTML = '';
                data.labels.forEach((label, index) => {
                    const colorItem = document.createElement('div');
                    colorItem.style.display = 'flex';
                    colorItem.style.alignItems = 'center';
                    colorItem.innerHTML = `
                        <div style="width: 15px; height: 15px; background: ${colorPalette[index % colorPalette.length]};
                            margin-right: 5px; border: 1px solid #ddd;"></div>
                        <span style="font-size: 0.9rem;">${label}</span>
                    `;
                    colorGuide.appendChild(colorItem);
                });

                if (pieChart) pieChart.destroy();
                pieChart = new Chart(serviceCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.counts,
                            backgroundColor: data.labels.map((_, i) => colorPalette[i % colorPalette.length])
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: `Patients per Service - ${getMonthName(selectedMonth)} 2025`
                            },
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Patients' } },
                            x: { title: { display: true, text: 'Services' } }
                        }
                    }
                });
            }

            function getMonthName(monthNumber) {
                const date = new Date();
                date.setMonth(monthNumber - 1);
                return date.toLocaleString('default', { month: 'long' });
            }

            function toggleReportSections() {
                const selected = document.getElementById('reportType').value;
                document.getElementById('serviceReport').style.display = selected === 'service' ? 'block' : 'none';
                document.getElementById('appointmentsReport').style.display = selected === 'appointments' ? 'block' : 'none';
                document.getElementById('financialReport').style.display = selected === 'financial' ? 'block' : 'none';

                if (selected === 'appointments' && !appointmentsChart) {
                    appointmentsChart = new Chart(appointmentsCtx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($dates); ?>,
                            datasets: [{
                                label: 'Appointments',
                                data: <?php echo json_encode($counts); ?>,
                                backgroundColor: '#36A2EB'
                            }]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Appointments Per Day'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Number of Appointments' }
                                },
                                x: {
                                    title: { display: true, text: 'Date' }
                                }
                            }
                        }
                    });
                }

                if (selected === 'financial' && !financialChart) {
                    financialChart = new Chart(financialCtx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($datesPaid); ?>,
                            datasets: [{
                                label: 'Total Amount (₱)',
                                data: <?php echo json_encode($amountsPaid); ?>,
                                backgroundColor: '#8BC34A'
                            }]
                        },
                        options: {
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Daily Financial Report'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: { display: true, text: 'Total Paid (₱)' }
                                },
                                x: {
                                    title: { display: true, text: 'Date' }
                                }
                            }
                        }
                    });
                }
            }

            // Load default chart
            updateChart();
        </script>
    </div>
</div>



    <script>
         $(document).ready(function(){
        // Function that polls the backend for the selected date
            function checkAvailabilityAdminAdd() {
                var selectedDate = $("#appointment_date").val();
                if (selectedDate) {
                    $.ajax({
                        url: 'getAppointmentsAdmin.php',
                        type: 'GET',
                        data: { appointment_date: selectedDate },
                        dataType: 'json',
                        success: function(bookedSlots) {
                            // First, enable all options.
                            $("#appointment_time option").prop("disabled", false);
                            // Then disable the options that are booked.
                            $.each(bookedSlots, function(index, slot) {
                                $("#appointment_time option[value='" + slot + "']").prop("disabled", true);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching appointment data:", error);
                        }
                    });
                }
            }

            // Trigger checkAvailability when the date changes.
            $("#appointment_date").on("change", function(){
                checkAvailabilityAdminAdd();
            });

            setInterval(function(){
                checkAvailabilityAdminAdd();
            }, 100);
        });


            $(document).ready(function(){
            // Function that polls the backend for the selected date
            function checkAvailabilityAdminResched() {
                var selectedDate = $("#new_date_resched").val();
                if (selectedDate) {
                    $.ajax({
                        url: 'getAppointmentsAdminResched.php',
                        type: 'GET',
                        data: { new_date_resched: selectedDate },
                        dataType: 'json',
                        success: function(bookedSlots) {
                            // First, enable all options.
                            $("#new_time_resched option").prop("disabled", false);
                            // Then disable the options that are booked.
                            $.each(bookedSlots, function(index, slot) {
                                $("#new_time_resched option[value='" + slot + "']").prop("disabled", true);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching appointment data:", error);
                        }
                    });
                }
            }

            // Trigger checkAvailability when the date changes.
            $("#new_date_resched").on("change", function(){
                checkAvailabilityAdminResched();
            });

            setInterval(function(){
                checkAvailabilityAdminResched();
            }, 100);
        });
        
        function filterAppointments() {
            let selectedDate = document.getElementById("filter-date").value;
            let selectedStatus = document.getElementById("filter-status").value.toLowerCase();
            let rows = document.querySelectorAll(".appointment-row");
            
            rows.forEach(row => {
                let rowDate = row.getAttribute("data-date");
                let rowStatus = row.getAttribute("data-status");
                
                let dateMatch = selectedDate === "" || rowDate === selectedDate;
                let statusMatch = selectedStatus === "" || rowStatus === selectedStatus;
                
                if (dateMatch && statusMatch) {
                    row.style.display = "table-row";
                } else {
                    row.style.display = "none";
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("active");
        }

        function printAppointments() {
            window.print();
        }

        function exportToPDF() {
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768 && sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && event.target !== menuToggle) {
                sidebar.classList.remove('active');
            }
        });

        //Add Appointment
        const patientsMap = <?php echo json_encode($patientsMap); ?>;

        function updatePatientName() {
            const selectedID = document.getElementById("userID").value;
            document.getElementById("patient_name").value = patientsMap[selectedID] || '';
        }

        // Add Appointment Modal Logic
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openAddAppointmentBtn');
            const modal = document.getElementById('addAppointmentModal');

            openBtn.addEventListener('click', function () {
                modal.style.display = 'block';
            });

            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        function closeAddAppointmentModal() {
            document.getElementById('addAppointmentModal').style.display = 'none';
        }

        //Sidebar
        function showSection(sectionId, clickedElement) {
            // Hide all sections
            const sections = document.querySelectorAll('.main-content');
            sections.forEach(sec => sec.style.display = 'none');

            // Show the selected section
            const sectionToShow = document.getElementById(sectionId);
            if (sectionToShow) sectionToShow.style.display = 'block';

            // Remove 'active' from all sidebar links
            const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
            sidebarLinks.forEach(link => link.classList.remove('active'));

            // Add 'active' to the clicked link
            clickedElement.classList.add('active');
        }

        //Add Services
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openAddServiceBtn');
            const modal = document.getElementById('addServiceModal');

            openBtn.addEventListener('click', function () {
                modal.style.display = 'block';
            });

            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        function closeAddModal() {
            document.getElementById('addServiceModal').style.display = 'none';
        }

        //Edit Services
        document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-primary-edit');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const cells = row.querySelectorAll('td');

                // Fill form with recent data from table
                const serviceId = cells[0].innerText.trim();
                const serviceName = cells[1].innerText.trim();
                const description = cells[2].innerText.trim();
                const price = cells[3].innerText.replace(/[₱,]/g, '').trim();

                document.getElementById('editServiceId').value = serviceId;
                document.getElementById('editServiceName').value = serviceName;
                document.getElementById('editDescription').value = description;
                document.getElementById('editPrice').value = price;

                // Show modal
                document.getElementById('editServiceModal').style.display = 'block';
            });
        });
    });

    // Close modal function
    function closeEditModal() {
        document.getElementById('editServiceModal').style.display = 'none';
    }
    
    // Edit Patient Details
       document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-primary');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const cells = row.querySelectorAll('td');

                // Map cells to variables by index:
                const patientId = cells[0]?.innerText.trim() || '';

                const fullNameParts = cells[1]?.innerText.trim().split(" ") || [];
                const firstName = fullNameParts[0] || '';
                const lastName = fullNameParts.slice(1).join(' ') || ''; // in case last name has spaces

                const gender = cells[2]?.innerText.trim() || '';
                const age = cells[3]?.innerText.trim() || '';
                const email = cells[4]?.innerText.trim() || '';
                const phone = cells[5]?.innerText.trim() || '';
                const address = cells[6]?.innerText.trim() || '';

                // Set the values in the modal form inputs
                document.getElementById('editPatientId').value = patientId;
                document.getElementById('editFirstName').value = firstName;
                document.getElementById('editLastName').value = lastName;
                document.getElementById('editAge').value = age;
                document.getElementById('editGender').value = gender;
                document.getElementById('editEmail').value = email;
                document.getElementById('editPhone').value = phone;
                document.getElementById('editAddress').value = address;

                // Show the modal
                document.getElementById('editPatientModal').style.display = 'block';
            });
        });
    });

    // Close modal function
    function closeEditPatientModal() {
        document.getElementById('editPatientModal').style.display = 'none';
    }

    //Add Staff
        document.addEventListener('DOMContentLoaded', function () {
        const openAddDentistBtn = document.getElementById('openAddDentistBtn');
        const addDentistModal = document.getElementById('addDentistModal');

        openAddDentistBtn.addEventListener('click', function () {
            addDentistModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        });

        window.closeDentistModal = function () {
            addDentistModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    //Edit Staff/Dentists
    document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-primary-editStaff');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const cells = row.querySelectorAll('td');

                // Get values from table
                const dentistId = cells[0].innerText.trim();
                const firstName = cells[1].innerText.trim();
                const lastName = cells[2].innerText.trim();
                const specialization = cells[3].innerText.trim();
                const email = cells[4].innerText.trim();
                const phone = cells[5].innerText.trim();
                const status = cells[6].innerText.trim();

                // Set values in modal inputs
                document.getElementById('editDentistId').value = dentistId;
                document.getElementById('editDentistFirstName').value = firstName;
                document.getElementById('editDentistLastName').value = lastName;
                document.getElementById('editDentistSpecialization').value = specialization;
                document.getElementById('editDentistEmail').value = email;
                document.getElementById('editDentistPhone').value = phone;
                document.getElementById('editDentistStatus').value = status;

                // Show modal and prevent scroll
                document.getElementById('editDentistModal').style.display = 'block';
                document.body.classList.add('modal-open');
            });
        });
    });

    // Close modal function
    function closeEditDentistModal() {
        document.getElementById('editDentistModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }


    // For Reschedule
        function openReschedModalWithID(btn) {
        const appointmentID = btn.getAttribute('data-id');
        document.getElementById('modalAppointmentID').value = appointmentID;
        openReschedModal();
    }

    function openReschedModal() {
        document.getElementById("reschedModal").style.display = "block";
    }

    function closeReschedModal() {
        document.getElementById("reschedModal").style.display = "none";
    }

    // Close when clicking outside the modal
    window.onclick = function(event) {
        const modal = document.getElementById("reschedModal");
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };    

    //Viewing Image
    function viewImage(imageSrc) {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        modalImg.src = imageSrc;
        modal.style.display = "flex"; 
    }

    function closeModal() {
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("modalImage");
        modal.style.display = "none";
        modalImg.src = ""; 
    }
    </script>
</body>
</html>