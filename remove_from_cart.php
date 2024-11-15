<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];
    $username = $_SESSION['username'];
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE username = ? AND isbn = ?");
    $stmt->bind_param("ss", $username, $isbn);
    $stmt->execute();
}

header("Location: cart.php");
exit();
?>