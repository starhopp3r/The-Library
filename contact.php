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
    <title>Contact - The Library</title>
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
        <div class="form-container">
            <h2>Contact Us</h2>
            <p class="contact-intro">Have a question? We'd love to hear from you.</p>
            
            <form id="contactForm" action="process_contact.php" method="POST" onsubmit="return validateContactForm()">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>

    <script>
        function validateContactForm() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;

            // Email validation
            const emailRegex = /^[\w.-]+@(?:\w+\.){1,3}\w{2,3}$/;
            if(!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return false;
            }

            // Message validation
            if(message.length < 10) {
                alert('Message must be at least 10 characters long');
                return false;
            }

            alert('Message sent');
            return false;
        }
    </script>
</body>
</html>