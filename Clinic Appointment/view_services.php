<?php
include_once('./login/config.php'); 
define("TITLE", "View Services");
include_once('header.php');

// Fetch services data
$sql = "SELECT service_id, service_name, description, price FROM tbl_services";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

     <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Koulen&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward"/>

    <title>Available Dental Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #48A6A7;
            --secondary-color: #264653;
            --accent-color:rgb(255, 226, 151);
            --light-color: #F2EFE7;
            --dark-color: #343a40;
            --text-color: #333;
            --white: #fff;
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Metropolis';
            background-color: var(--light-color);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }

        h2 {
            margin-top: 20px;
            color: var(--secondary-color);
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .services-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .service-card {
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            width: 280px;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid #e0e0e0;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .service-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .service-header h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .service-price {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 1rem 0;
            text-align: center;
        }

        .service-features {
            padding: 1.5rem;
        }

        .feature-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .feature-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .feature-title {
            font-weight: bold;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .feature-value {
            color: var(--text-color);
        }

        .feature-value strong {
            color: var(--secondary-color);
        }

        .note {
            text-align: center;
            margin: 2rem auto;
            padding: 1rem;
            max-width: 800px;
            font-style: italic;
            color: #111111;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .services-container {
                flex-direction: column;
                align-items: center;
            }
            
            .service-card {
                width: 90%;
                max-width: 350px;
            }
        }
    </style>
</head>

<body>

    <h2>AVAILABLE SERVICES</h2>

    <div class="container">
        <div class="services-container">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="service-card">';
                    echo '<div class="service-header">';
                    echo '<h3>' . htmlspecialchars($row["service_name"]) . '</h3>';
                    echo '</div>';
                    
                    echo '<div class="service-price">₱' . number_format($row["price"], 2) . '</div>';
                    
                    echo '<div class="service-features">';
                    
                    // Assuming description contains features separated by newlines
                    $features = explode("\n", $row["description"]);
                    foreach ($features as $feature) {
                        if (trim($feature) !== '') {
                            // Split each feature line into title and value if it contains a colon
                            if (strpos($feature, ':') !== false) {
                                list($title, $value) = explode(':', $feature, 2);
                                echo '<div class="feature-item">';
                                echo '<div class="feature-title">' . htmlspecialchars(trim($title)) . '</div>';
                                echo '<div class="feature-value">' . htmlspecialchars(trim($value)) . '</div>';
                                echo '</div>';
                            } else {
                                echo '<div class="feature-item">';
                                echo '<div class="feature-value">' . htmlspecialchars(trim($feature)) . '</div>';
                                echo '</div>';
                            }
                        }
                    }
                    
                    echo '</div>'; // Close service-features
                    echo '</div>'; // Close service-card
                }
            } else {
                echo '<p style="text-align: center; width: 100%;">No services available at the moment.</p>';
            }
            ?>
        </div>

        <div class="note">
            <p>This is an estimated amount for each service. The total amount may vary depending on your specific dental needs and treatment plan.</p>
        </div>
    </div>

<?php include_once('footer.php');?>

</body>
</html>