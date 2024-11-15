<?php
session_start();
require_once 'config.php';

// Define the admin username
$admin_username = 'admin'; // replace 'admin' with the actual admin username

// Get success or error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Check if the user is logged in and if the username matches the admin username
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    session_destroy();
    header("Location: login.php");
    exit();
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
    <title>Add Books - Admin</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a class="home-nav-link">The Library (Admin)</a></div>
            <ul class="nav-links">
                <li><a href="add_books.php" class="active">Add Books</a></li>
                <li><a href="remove_books.php">Remove Books</a></li>
                <li><a href="logout.php" class="logout-link">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container" style="max-width: 1200px;">
            <!-- Success/Error Messages -->
            <?php if($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Add New Book Form -->
            <div class="admin-section">
                <h2>Add New Book</h2>
                <form action="process_add_book.php" method="POST" class="admin-form" id="addBookForm" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="isbn">ISBN (13 digits)</label>
                            <input type="text" id="isbn" name="isbn" required pattern="\d{13}">
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <input type="text" id="genre" name="genre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="book_image">Book Cover Image (JPG only)</label>
                        <input type="file" id="book_image" name="book_image" accept=".jpg,.jpeg" required>
                        <p class="file-hint">Please upload a JPG image</p>
                    </div>
                    <button type="submit" class="btn-admin">Add Book</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>

    <script>
        // Client-side validation
        document.getElementById('addBookForm').addEventListener('submit', function(e) {
            const isbn = document.getElementById('isbn').value;
            
            // ISBN validation
            if(!/^\d{13}$/.test(isbn)) {
                e.preventDefault();
                alert('ISBN must be exactly 13 digits');
                return;
            }
        });

        // Image validation
        document.getElementById('book_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                if(file.size > 5000000) {
                    alert('File is too large. Maximum size is 5MB.');
                    this.value = '';
                    return;
                }
                
                if(!file.type.match('image/jpeg')) {
                    alert('Only JPG files are allowed.');
                    this.value = '';
                    return;
                }
            }
        });
    </script>
</body>
</html>