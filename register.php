<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = md5($password);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, is_admin) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("ssss", $name, $username, $email, $hashed_password);
            
            if($stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
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
    <title>Register - The Library</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo">The Library</div>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Register Account</h2>
            
            <?php if(isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form id="registrationForm" method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn-register">Register</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>

    <script>
        function validateForm() {
            const name = document.getElementById('name').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Name validation
            if(name.length < 2) {
                alert('Name must be at least 2 characters long');
                return false;
            }

            // Username validation
            if(username.length < 4) {
                alert('Username must be at least 4 characters long');
                return false;
            }

            // Email validation
            // const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const emailRegex = /^[\w.-]+@(?:\w+\.){1,3}\w{2,3}$/;
            if(!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return false;
            }

            // Password validation
            if(password.length < 8) {
                alert('Password must be at least 8 characters long');
                return false;
            }

            // Password strength validation
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

            if(!(hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChar)) {
                alert('Password must contain at least one uppercase letter, one lowercase letter, one number and one special character');
                return false;
            }

            // Confirm password
            if(password !== confirmPassword) {
                alert('Passwords do not match');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>