<?php
session_start();
require_once 'config.php';

// Get all books for the table display
$query = "SELECT * FROM books ORDER BY title";
$result = $conn->query($query);
$books = $result->fetch_all(MYSQLI_ASSOC);

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
    <title>Remove Books - Admin</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a class="home-nav-link">The Library (Admin)</a></div>
            <ul class="nav-links">
                <li><a href="add_books.php">Add Books</a></li>
                <li><a href="remove_books.php" class="active">Remove Books</a></li>
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

            <!-- Remove Books Section -->
            <div class="admin-section">
                <h2>Remove Books</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ISBN</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Genre</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($books as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['genre']); ?></td>
                                <td><?php echo ucfirst($book['availability']); ?></td>
                                <td>
                                    <?php if($book['availability'] == 'on loan'): ?>
                                        <form action="process_return_book.php" method="POST" class="book-action-form">
                                            <input type="hidden" name="isbn" value="<?php echo $book['isbn']; ?>">
                                            <button type="submit" class="btn-return">Return</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="process_remove_book.php" method="POST" class="book-action-form" onsubmit="return confirm('Are you sure you want to remove this book?');">
                                            <input type="hidden" name="isbn" value="<?php echo $book['isbn']; ?>">
                                            <button type="submit" class="btn-delete">Remove</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>
</body>
</html>