<?php 
    session_start();
    include_once('./login/config.php');
    $isLoggedIn = isset($_SESSION['userID']) ? true : false;

    $fname = $lname = $email = $phone = $birthdate = $gender = $age = $street = $city = $brgy = $zip = '';

    if (isset($_SESSION['userID'])) {
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

        $queryPatients = "SELECT birthdate, gender, age, address FROM tbl_patients WHERE user_id = ?";
        $stmtPatients = $con->prepare($queryPatients);
        if ($stmtPatients) {
            $stmtPatients->bind_param("i", $user_id);
            $stmtPatients->execute();
            $stmtPatients->bind_result($birthdate, $gender, $age, $address);
            $stmtPatients->fetch();
            $stmtPatients->close();

            $addressParts = explode(',', $address);
            $street = isset($addressParts[0]) ? trim($addressParts[0]) : '';
            $brgy   = isset($addressParts[1]) ? trim($addressParts[1]) : '';
            $city   = isset($addressParts[2]) ? trim($addressParts[2]) : '';
            $zip    = isset($addressParts[3]) ? trim($addressParts[3]) : '';
        }
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
        $fname = htmlspecialchars($_POST['fname'] ?? 'N/A');
        $lname = htmlspecialchars($_POST['lname'] ?? 'N/A');
        $email = htmlspecialchars($_POST['email'] ?? 'N/A');
        $phone = htmlspecialchars($_POST['phone'] ?? 'N/A');

        $age = htmlspecialchars($_POST['age'] ?? 'N/A');
        $birthdate = htmlspecialchars($_POST['birthdate'] ?? 'N/A');
        $gender = htmlspecialchars($_POST['gender'] ?? 'N/A');
        $street = htmlspecialchars($_POST['street'] ?? 'N/A');
        $city = htmlspecialchars($_POST['city'] ?? 'N/A');
        $brgy = htmlspecialchars($_POST['barangay'] ?? 'N/A');
        $zip = htmlspecialchars($_POST['zip_code'] ?? 'N/A');
            
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Buddies - Appointment System</title>
    <link rel="stylesheet" href="styles.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <img src="logo.png">
                    <p>Dental Buddies PH</p>
                </a>
                
                <ul class="nav-links">
                    <li><a href="#services">Services</a></li>
                    <li><a href="#dentists">Dentists</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <?php if (isset($_SESSION['valid'])): ?>
                        <li><a href="login/account.php">Account</a></li> 
                    <?php else: ?>
                        <li><a href="login/login.php">Login</a></li> 
                    <?php endif; ?>
                </ul>

                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <h1>Your Smile Deserves the Best Care</h1>
            <p>Professional dental care in a comfortable and friendly environment</p>
            <div class="btn-container">
                <a href="#appointment" class="btn btn-primary">Book an Appointment</a>
                <a href="learnmore.php" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
                <p>Comprehensive dental care for the whole family</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <h3>General Dentistry</h3>
                    <p>Regular checkups, cleanings, Tooth Extraction, Fillings, and Preventive Care.</p>
                </div>
                
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-teeth-open"></i>
                    </div>
                    <h3>Orthodontics</h3>
                    <p>Braces and aligners for a perfectly straight smile.</p>
                </div>
            </div>

            <a href="view_services.php" class="btn btn-services">Learn More</a>
        </div>
    </section>

    <!-- Appointment Section -->
    <section id="appointment" class="appointment">
    <div class="container">
        <div class="appointment-form">
        <h2>Book Your Appointment</h2>
            <form action="payment.php" method="POST" id="appointmentForm">
                <div class="form-columns">
            
                    <!-- Left: Personal Info -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" required value="<?php echo htmlspecialchars($fname); ?>" disabled>
                        <input type="hidden" name="fname" value="<?php echo htmlspecialchars($fname); ?>">
                        </div>

                        <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" required value="<?php echo htmlspecialchars($lname); ?>" disabled>
                        <input type="hidden" name="lname" value="<?php echo htmlspecialchars($lname); ?>">
                        </div>

                      <!-- Birthdate -->
                        <div class="form-group">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" disabled>
                            <input type="hidden" name="age" value="<?php echo htmlspecialchars($age); ?>">
                            <input type="hidden" name="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>">
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required disabled>
                                <option value="">Select a Gender</option>
                                <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?> >Male</option>
                                <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?> >Female</option>
                            </select>
                            <input type="hidden" name="gender" value="<?php echo htmlspecialchars($gender); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>" disabled>
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone); ?>" disabled>
                            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>

                       <div class="form-group" style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                            <div style="width: 48%; margin: 10px 1%;">
                                <label for="street" style="width: 100%; margin-top: 10px;">Street</label>
                                <input type="text" id="street" name="street" value="<?php echo htmlspecialchars($street); ?>" placeholder="123 Main St" required style="width: 100%; margin-top: 5px;" disabled>
                                <input type="hidden" name="street" value="<?php echo htmlspecialchars($street); ?>">
                            </div>

                            <div style="width: 48%; margin: 10px 1%;">
                                <label for="barangay" style="width: 100%; margin-top: 10px;">Barangay</label>
                                <input type="text" id="barangay" name="barangay" value="<?php echo htmlspecialchars($brgy); ?>" placeholder="Brgy Rizal" required style="width: 100%; margin-top: 5px;" disabled>
                                <input type="hidden" name="barangay" value="<?php echo htmlspecialchars($brgy); ?>">
                            </div>

                            <div style="width: 48%; margin: 10px 1%; margin-top: 10px;">
                                <label for="city" style="width: 100%; margin-top: 10px;">City</label>
                                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" placeholder="Taguig City" required style="width: 100%; margin-top: 5px;" disabled>
                                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                            </div>

                            <div style="width: 48%; margin: 10px 1%; margin-top: 10px;">
                                <label for="zip_code" style="width: 100%; margin-top: 10px;">Zip Code</label>
                                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($zip); ?>" placeholder="1205" required style="width: 100%; margin-top: 5px;" disabled>
                                <input type="hidden" name="zip_code" value="<?php echo htmlspecialchars($zip); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Appointment Details -->
                    <div class="form-section">
                        <h3>Appointment Details</h3>
                        <div class="form-group">
                        <label for="service">Service Needed</label>
                        <select id="service" name="service" required onchange="updateSubServices()">
                            <option value="">Select a service</option>
                            <option value="General">General Dentistry</option>
                            <option value="Orthodontics">Orthodontics</option>
                        </select>
                        </div>

                          <!-- Second dropdown (populated dynamically) -->
                        <div class="form-group" id="sub-service-container" style="display: none;">
                            <label for="sub_service">Sub-Service</label>
                            <select id="sub_service" name="sub_service" required>
                                <option value="">Select a sub-service</option>
                            </select>
                        </div>

                       
                        <div class="form-group">
                            <label for="dentist">Select Dentist</label>
                            <select id="dentist" name="dentist" required>
                                <option value="">Select Dentist</option>
                                <option value="docAllen">Doc. Allen Lobregat</option>
                                <option value="docCarol">Doc. Carol</option>
                            </select>
                        </div>

                        <div class="form-group">
                        <label for="date">Preferred Date</label>
                        <input type="date" id="date" name="date" required>
                        </div>

                        <div class="form-group">
                        <label for="time">Preferred Time</label>
                        <select id="time" name="time" required>
                            <option value="">Select a time</option>
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

                    </div>
                </div>

                    <div class="submit-btn">
                        <button type="submit" class="btn btn-primary" id="bookBtn">BOOK APPOINTMENT</button>
                    </div>

                    <?php if ($fname == ""): ?>
                        <script>
                            document.getElementById("bookBtn").addEventListener("click", function(event) {
                                event.preventDefault(); // prevent form submission
                                if (confirm("You need to log in before booking. Do you want to log in now?")) {
                                    window.location.href = './login/login.php';
                                }
                            });
                        </script>
                    <?php endif; ?>
            </form>
        </div>
    </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="dentists">
        <div class="container">
            <div class="section-title">
                <h2>Dentist</h2>
                <p>Our Proffesional Dentist</p>
            </div>
            
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    
                    <div class="testimonial-author">
                        <img src="dentisticon.png" alt="">
                        <div class="author-info">
                            <h4>Dr. Allen J. Lobregat</h4>
                            <small>Dentist</small>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    
                    <div class="testimonial-author">
                        <img src="dentisticon.png" alt="">
                        <div class="author-info">
                            <h4>Dr. Carol Ong</h4>
                            <small>Dentist</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3><i class="fas fa-tooth"></i> Dental Buddies</h3>
                    <p>Providing exceptional dental care with a personal touch since 2011.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#services">Services</a></li>
                        <li><a href="#dentists">Dentists</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="location.php">Location</a></li>
                    </ul>
                </div>
                
                <div class="footer-col" id="contact">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Doroteo Jose St, Santa Cruz, Manila, Quezon City</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>09228611987</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>dentalbuddies@gmail.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Sun: 8AM-8PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Dental Buddies PH. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>                    
    <script>
           $(document).ready(function() {
            function checkAvailability() {
                var selectedDate = $("#date").val();
                if (selectedDate) {
                    $.ajax({
                        url: 'getAppointments.php',
                        type: 'GET',
                        data: { date: selectedDate },
                        dataType: 'json',
                        success: function(bookedSlots) {
                            // Enable all options first
                            $("#time option").prop("disabled", false);

                            // Disable booked slots
                            bookedSlots.forEach(function(slot) {
                                $("#time option[value='" + slot + "']").prop("disabled", true);
                            });

                            // If currently selected option is disabled, reset the selection
                            if ($("#time option:selected").prop("disabled")) {
                                $("#time").val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching appointment data:", error);
                        }
                    });
                } else {
                    // If no date selected, enable all options
                    $("#time option").prop("disabled", false);
                    $("#time").val('');
                }
            }

            // Trigger on date change
            $("#date").on("change", checkAvailability);

            // Optional: periodically recheck availability if date is selected
            setInterval(function() {
                if ($("#date").val()) {
                    checkAvailability();
                }
            }, 5000);
        });
        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
        
        // Set minimum date for appointment (tomorrow)
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1);
            
            const dd = String(tomorrow.getDate()).padStart(2, '0');
            const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            const yyyy = tomorrow.getFullYear();
            
            const minDate = yyyy + '-' + mm + '-' + dd;
            document.getElementById('date').min = minDate;
            
            // Close mobile menu when clicking a link
            const navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    document.querySelector('.nav-links').classList.remove('active');
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".nav-links a").forEach(anchor => {
                anchor.addEventListener("click", function (event) {
                    if (this.getAttribute("href").startsWith("#")) {
                        event.preventDefault();
                        const targetId = this.getAttribute("href").substring(1);
                        document.getElementById(targetId).scrollIntoView({
                            behavior: "smooth"
                        });
                    }
                });
            });
        });

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


        function updateSubServices() {
            const service = document.getElementById("service").value;
            const subServiceContainer = document.getElementById("sub-service-container");
            const subServiceSelect = document.getElementById("sub_service");

            // Clear existing options
            subServiceSelect.innerHTML = '<option value="">Select a sub-service</option>';

            if (service === "General") {
                subServiceSelect.innerHTML += '<option value="Checkups">Checkups</option>';
                subServiceSelect.innerHTML += '<option value="Cleaning">Cleaning</option>';
                subServiceSelect.innerHTML += '<option value="Extraction">Tooth Extraction</option>';
                subServiceContainer.style.display = 'block';
            } else if (service === "Orthodontics") {
                subServiceSelect.innerHTML += '<option value="Braces">Braces</option>';
                subServiceContainer.style.display = 'block';
            } else {
                subServiceContainer.style.display = 'none';
            }
        }

            document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const isLoggedIn = <?php echo $isLoggedIn; ?>;

            if (!isLoggedIn) {
                e.preventDefault(); // Stop form submission
                if (confirm("You need to log in before booking. Do you want to log in now?")) {
                    window.location.href = "./login/login.php";
                }
            }
        });

    </script>
</body>
</html>