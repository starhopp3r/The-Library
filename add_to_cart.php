<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];
    $username = $_SESSION['username'];
    
    // Check if book is available
    $stmt = $conn->prepare("SELECT * FROM books WHERE isbn = ? AND availability = 'available'");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Check if book is already in cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE username = ? AND isbn = ?");
        $stmt->bind_param("ss", $username, $isbn);
        $stmt->execute();
        $cart_result = $stmt->get_result();
        
        if ($cart_result->num_rows == 0) {
            // Add to cart
            $stmt = $conn->prepare("INSERT INTO cart (username, isbn) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $isbn);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Book added to cart successfully!";
            } else {
                $_SESSION['error_message'] = "Error adding book to cart.";
            }
        } else {
            $_SESSION['error_message'] = "This book is already in your cart.";
        }
    } else {
        $_SESSION['error_message'] = "Book is not available.";
    }
}

header("Location: browse_books.php");
exit();
?>