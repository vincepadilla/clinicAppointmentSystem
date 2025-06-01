<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if(defined('TITLE')) {
            print TITLE;

        } else {
            print "All about the main title of the page";
        }
        ?>
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward"/>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>

        :root {
            --primary-color: #48A6A7;
            --secondary-color: #264653;
            --accent-color: #e9c46a;
            --light-color: #F2EFE7;
            --dark-color: #343a40;
            --text-color: #333;
            --text-light: #777;
            --white: #fff;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Metropolis';
            line-height: 1.6;
            color: var(--text-color);
            background-color: #F2EFE7;
        }

        .footer-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Footer */
        footer {
            background: var(--secondary-color);
            color: var(--white);
            padding: 50px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h3 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            color:#fff;
        }

        .footer-col p {
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: var(--white);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .social-links a:hover {
            color: var(--accent-color);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--accent-color);
        }

        .contact-info {
            list-style: none;
        }

        .contact-info li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }

        .contact-info i {
            margin-right: 10px;
            margin-top: 4px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }


        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
        }    

    </style>

</head>
<body>
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3><i class="fas fa-tooth"></i> Dental Buddies PH</h3>
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
                    <?php if (isset($_SESSION['valid'])): ?>
                        <li><a href="login/account.php">Account</a></li> <!-- User is logged in -->
                    <?php else: ?>
                        <li><a href="login/login.php">Login</a></li> <!-- User is not logged in -->
                    <?php endif; ?>
                        <li><a href="../index.php#services">Services</a></li>
                        <li><a href="../index.php#dentists">Dentists</a></li>
                        <li><a href="../about.php">About Us</a></li>
                        <li><a href="../index.php#contact">Contact</a></li>
                        <li><a href="../location.php">Location</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
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
</body>
</html>