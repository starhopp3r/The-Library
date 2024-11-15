<?php
session_start();
require_once 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $hashed_password = md5($password);
    
    $stmt = $conn->prepare("SELECT id, name, username, is_admin FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if 1 row is returned, i.e. has a matching account
    if($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Redirect based on user role
        if($user['is_admin']) {
            header("Location: add_books.php");
        } else {
            header("Location: browse_books.php");
        }
        exit();
    } else { // No matching account
        $error = "Invalid username or password. Please try again.";
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
    <title>Login - The Library</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo">The Library</div>
            <ul class="nav-links">
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Login</h2>
            
            <?php if($error !== ""): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="loginForm" method="POST" onsubmit="return validateLoginForm()">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>

    <script>
        function validateLoginForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if(username.trim() === '') {
                alert('Please enter your username');
                return false;
            }

            if(password.trim() === '') {
                alert('Please enter your password');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>