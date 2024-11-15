<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

$username = $_SESSION['username'];
$cart_count = getCartCount($conn, $username);
$error_message = '';

// Process loan if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_loan'])) {
    try {
        // Start transaction
        $conn->begin_transaction();

        // Get all books from user's cart
        $cart_query = "SELECT isbn FROM cart WHERE username = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        
        if ($cart_result->num_rows === 0) {
            throw new Exception("Cart is empty");
        }

        // Insert each book into transactions and update availability
        $insert_query = "INSERT INTO transactions (username, isbn, loan_date, days_remaining, fines) 
                        VALUES (?, ?, CURRENT_DATE, 30, 0.00)";
        $update_query = "UPDATE books SET availability = 'on loan' WHERE isbn = ?";
        $delete_query = "DELETE FROM cart WHERE username = ? AND isbn = ?";
        
        $insert_stmt = $conn->prepare($insert_query);
        $update_stmt = $conn->prepare($update_query);
        $delete_stmt = $conn->prepare($delete_query);

        while ($cart_item = $cart_result->fetch_assoc()) {
            // Insert into transactions
            $insert_stmt->bind_param("ss", $username, $cart_item['isbn']);
            $insert_stmt->execute();
            
            // Update book availability
            $update_stmt->bind_param("s", $cart_item['isbn']);
            $update_stmt->execute();
            
            // Remove from cart
            $delete_stmt->bind_param("ss", $username, $cart_item['isbn']);
            $delete_stmt->execute();
        }

        // Commit transaction
        $conn->commit();
        
        // Redirect to profile page
        header('Location: my_profile.php');
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Error processing loan: " . $e->getMessage();
    }
}

// Get cart items
$cart_items = [];
$query = "SELECT b.* FROM cart c 
          JOIN books b ON c.isbn = b.isbn 
          WHERE c.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Cart - The Library</title>
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a class="home-nav-link">The Library</a></div>
            <ul class="nav-links">
                <li><a href="browse_books.php">Browse Books</a></li>
                <li><a href="cart.php">Cart<?php echo $cart_count > 0 ? " ($cart_count)" : ''; ?></a></li>
                <li><a href="my_profile.php">My Profile</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php" class="logout-link">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="form-container" style="max-width: 1200px;">
            <h2>Your Cart</h2>
            
            <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
                <p class="empty-cart">Your cart is empty. <a href="browse_books.php">Browse books</a> to add some!</p>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach($cart_items as $book): ?>
                    <div class="cart-item">
                        <div class="book-image">
                            <img src="media/img/<?php echo $book['isbn']; ?>.jpg" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        </div>
                        <div class="book-info">
                            <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="book-genre"><?php echo htmlspecialchars($book['genre']); ?></p>
                        </div>
                        <form action="remove_from_cart.php" method="POST" class="remove-form">
                            <input type="hidden" name="isbn" value="<?php echo $book['isbn']; ?>">
                            <button type="submit" class="btn-remove">Remove</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="cart-actions">
                    <form method="POST">
                        <input type="hidden" name="process_loan" value="1">
                        <button type="submit" class="btn-checkout">Borrow Selected Books</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>
</body>
</html>