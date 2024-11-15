<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isbn = trim($_POST['isbn']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);

    // Validate ISBN format
    if (!preg_match('/^\d{13}$/', $isbn)) {
        $_SESSION['error_message'] = "Invalid ISBN format. Must be 13 digits.";
        header("Location: add_books.php");
        exit();
    }

    // Check for duplicate ISBN
    $stmt = $conn->prepare("SELECT isbn FROM books WHERE isbn = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_message'] = "A book with this ISBN already exists.";
        header("Location: add_books.php");
        exit();
    }

    // Image Upload Handling
    if(isset($_FILES['book_image']) && $_FILES['book_image']['error'] == 0) {
        $file = $_FILES['book_image'];
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Validate file type
        if($file_type != "jpg" && $file_type != "jpeg") {
            $_SESSION['error_message'] = "Only JPG files are allowed.";
            header("Location: add_books.php");
            exit();
        }

        // Validate file size (max 5MB)
        if($file['size'] > 5000000) {
            $_SESSION['error_message'] = "File is too large. Maximum size is 5MB.";
            header("Location: add_books.php");
            exit();
        }

        // Create image directory if it doesn't exist
        $upload_dir = "media/img/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Set the filename to ISBN
        $new_filename = $isbn . ".jpg";
        $upload_path = $upload_dir . $new_filename;

        // Move the uploaded file
        if(!move_uploaded_file($file['tmp_name'], $upload_path)) {
            $_SESSION['error_message'] = "Error uploading image. Please try again.";
            header("Location: add_books.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Book image is required.";
        header("Location: add_books.php");
        exit();
    }

    // Add new book
    $stmt = $conn->prepare("INSERT INTO books (isbn, title, author, genre, description, availability) VALUES (?, ?, ?, ?, ?, 'available')");
    $stmt->bind_param("sssss", $isbn, $title, $author, $genre, $description);
    
    if($stmt->execute()) {
        $_SESSION['success_message'] = "Book added successfully!";
    } else {
        // If book insertion fails, remove the uploaded image
        if(file_exists($upload_path)) {
            unlink($upload_path);
        }
        $_SESSION['error_message'] = "Error adding book. Please try again.";
    }
}

header("Location: add_books.php");
exit();
?>