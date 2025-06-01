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

    <style>

        :root {
            --primary-color: #48A6A7;
            --secondary-color: #264653;
            --accent-color:rgb(242, 220, 165);
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

        a {
            text-decoration: none;
            color: inherit;
            text-transform: uppercase;
            font-weight: 600;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .nav-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: #21867a;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--white);
            color: var(--white);
        }

        .btn-outline:hover {
            background-color: var(--white);
            color: var(--primary-color);
        }

        .btn-services:hover {
            color: var(--primary-color);
        }

        .services .nav-container {
            text-align: center;
        }

        .btn-services {
            display: inline-block; 
            margin-top: 20px; 
        }

        header {
            background-color: var(--white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            border: 2px solid black;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .navbar .logo {
            display: flex;
            align-items: center; 
        }

        .navbar .logo img {
            margin-right: 10px; 
        }

        .logo i {
            margin-right: 20px;
        }

        .navbar .logo p {
            margin: 0; 
            font-size: 20px; 
        }

        .logo img {
            width: 100px;
            height: auto;
        }

        .nav-links {
            display: flex;
            list-style: none;
            
        }

        .nav-links li {
            margin-left: 40px;
            margin-top: 5px;
        }

        .nav-links a {
            font-weight: 600;
            transition: var(--transition);
            font-size: 19px;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-links .active {
            color: var(--primary-color);
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 120px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: var(--white);
                flex-direction: column;
                align-items: center;
                padding-top: 40px;
                transition: var(--transition);
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-links li {
                margin: 15px 0;
            }
            
            .hero {
                padding: 80px 0;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .btn {
                padding: 8px 16px;
            }
            
            .appointment-form {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .hero .btn-container {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .hero .btn {
                margin: 0;
                width: 100%;
                max-width: 250px;
                margin: 0 auto;
            }
            
            .form-col {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="nav-container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <img src="logo.png">
                    <p>Dental Buddies PH</p>
                </a>
                
                <ul class="nav-links">
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../index.php#services">Services</a></li>
                    <li><a href="../index.php#dentists">Dentists</a></li>
                    <li><a href="../index.php#contact">Contact</a></li>
                </ul>
                
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>
        <script>
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
        </script>
</body>
</html>