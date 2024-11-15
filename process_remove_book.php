<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];

    // Check if book is currently on loan
    $stmt = $conn->prepare("SELECT availability FROM books WHERE isbn = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if($book['availability'] == 'on loan') {
        $_SESSION['error_message'] = "Cannot remove book while it is on loan.";
    } else {
        // Start transaction
        $conn->begin_transaction();

        try {
            // First, remove any references in the cart table
            $stmt = $conn->prepare("DELETE FROM cart WHERE isbn = ?");
            $stmt->bind_param("s", $isbn);
            $stmt->execute();

            // Then, remove from transactions table if it exists
            $stmt = $conn->prepare("DELETE FROM transactions WHERE isbn = ?");
            $stmt->bind_param("s", $isbn);
            $stmt->execute();

            // Finally, remove the book
            $stmt = $conn->prepare("DELETE FROM books WHERE isbn = ?");
            $stmt->bind_param("s", $isbn);
            $stmt->execute();

            // If everything went well, commit the transaction
            $conn->commit();
            $_SESSION['success_message'] = "Book removed successfully!";
            
        } catch (Exception $e) {
            // If there was an error, rollback the changes
            $conn->rollback();
            $_SESSION['error_message'] = "Error removing book. Please try again.";
        }
    }
}

header("Location: remove_books.php");
exit();
?>