<?php
function getCartCount($conn, $username) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}
?>