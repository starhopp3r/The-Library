<?php
require_once 'config.php';

function updateLoansAndFines() {
    global $conn;
    
    // Get all active loans
    $query = "SELECT transaction_id, loan_date FROM transactions";
    $result = $conn->query($query);
    
    while($row = $result->fetch_assoc()) {
        // Calculate days remaining
        $loan_date = new DateTime($row['loan_date']);
        $current_date = new DateTime();
        $diff = $current_date->diff($loan_date)->days;
        $days_remaining = 30 - $diff;
        
        // Calculate fines if overdue
        $fines = ($days_remaining < 0) ? abs($days_remaining) : 0;
        
        // Update the transaction
        $update = "UPDATE transactions 
                  SET days_remaining = ?, fines = ? 
                  WHERE transaction_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("idi", $days_remaining, $fines, $row['transaction_id']);
        $stmt->execute();
    }
}

// Call this function at the top of any page that displays loan information
updateLoansAndFines();
?>