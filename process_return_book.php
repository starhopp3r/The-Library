<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update book status to available
        $update_book = $conn->prepare("UPDATE books SET availability = 'available' WHERE isbn = ?");
        $update_book->bind_param("s", $isbn);
        $update_book->execute();

        // Remove the transaction record
        $delete_transaction = $conn->prepare("DELETE FROM transactions WHERE isbn = ?");
        $delete_transaction->bind_param("s", $isbn);
        $delete_transaction->execute();

        // Commit transaction
        $conn->commit();
        
        $_SESSION['success_message'] = "Book has been returned successfully.";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Error processing return. Please try again.";
    }
}

header("Location: remove_books.php");
exit();
?>