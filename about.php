<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Get cart count if user is logged in
$cart_count = isset($_SESSION['username']) ? getCartCount($conn, $_SESSION['username']) : 0;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/main.css">
    <title>About - The Library</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo">The Library</div>
            <ul class="nav-links">
                <?php if(isset($_SESSION['username'])): ?>
                    <?php if($is_admin): ?>
                        <li><a href="add_books.php">Add Books</a></li>
                        <li><a href="remove_books.php">Remove Books</a></li>
                    <?php else: ?>
                        <li><a href="browse_books.php">Browse Books</a></li>
                        <li><a href="cart.php">Cart<?php echo $cart_count > 0 ? " ($cart_count)" : ''; ?></a></li>
                        <li><a href="my_profile.php">My Profile</a></li>
                    <?php endif; ?>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="logout.php" class="logout-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Page content goes here -->
        <h1> About Us </h1>
        <img src="media/img/about.jpg" width="500" height="250" id="aboutimage" alt="About Us">
        <p>Welcome to The Library, your gateway to a world of knowledge and discovery! Our library is dedicated to providing access to a vast collection of 
            books and events that cater to a diverse community of readers, researchers, and lifelong learners. 
            Whether you're here to explore literature, conduct research, or participate in our educational programs, 
            we strive to make your experience enriching and enjoyable.</p>
        <h2>Mission</h2>
        <p>At The Library, our mission is to foster a culture of learning and creativity by providing accessible information, resources, 
            and services. We aim to support the intellectual and personal growth of our patrons through a welcoming and inspiring environment that promotes 
            literacy, critical thinking and innovation.</p>
        <h2>Vision</h2>
        <p>We envision The Library as a hub for knowledge and community engagement, where people of all ages and backgrounds come together to explore ideas, 
            discover new interests and connect with one another. We aspire to be a leading library in the digital age, embracing new technologies while 
            preserving the timeless value of reading and education for future generations.</p>
    </main>
    <!-- Footer of the website -->
    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>
</body>
</html>