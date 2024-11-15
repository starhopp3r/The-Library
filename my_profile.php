<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include database configuration
require_once 'config.php';
require_once 'functions.php';

// Initialize variables
$user = null;
$transactions = null;
$total_fines = 0;
$error_message = '';
$username = $_SESSION['username'];
$cart_count = getCartCount($conn, $username);

try {
    // Check if user is logged in
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    try {
        // Get all transactions that need updating
        $update_query = "SELECT transaction_id, loan_date FROM transactions";
        $stmt = $conn->prepare($update_query);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->execute();
        $transactions_to_update = $stmt->get_result();
        
        // Prepare the update statement outside the loop for better performance
        $update_stmt = $conn->prepare("UPDATE transactions SET days_remaining = ?, fines = ? WHERE transaction_id = ?");
        
        if (!$update_stmt) {
            throw new Exception("Prepare update statement failed: " . $conn->error);
        }
        
        while ($transaction = $transactions_to_update->fetch_assoc()) {
            // Calculate days remaining
            $loan_date = new DateTime($transaction['loan_date']);
            $current_date = new DateTime();
            $interval = $current_date->diff($loan_date);
            $days_since_loan = $interval->days;
            
            // Calculate days remaining (30 days loan period)
            $days_remaining = 30 - $days_since_loan;
            
            // Calculate fines (only if overdue)
            $fines = $days_remaining < 0 ? abs($days_remaining) : 0;
            
            // Update the database
            $update_stmt->bind_param("iii", 
                $days_remaining,
                $fines,
                $transaction['transaction_id']
            );
            
            $update_stmt->execute();
        }
        
        // Close the statements
        $update_stmt->close();
        $stmt->close();
        
    } catch (Exception $e) {
        $error_message = "An error occurred while updating fines: " . $e->getMessage();
    }

    // Get user information
    $username = $_SESSION['username'];
    $user_query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($user_query);  // Using $conn from your config file
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User not found");
    }

    // Get user's transaction history
    $trans_query = "SELECT t.*, b.title 
                   FROM transactions t 
                   LEFT JOIN books b ON t.isbn = b.isbn 
                   WHERE t.username = ? 
                   ORDER BY t.loan_date DESC";
    $stmt = $conn->prepare($trans_query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $transactions = $stmt->get_result();

    // Calculate total fines
    $fines_query = "SELECT COALESCE(SUM(fines), 0) as total_fines 
                    FROM transactions 
                    WHERE username = ? AND fines > 0";
    $stmt = $conn->prepare($fines_query);
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $fines_result = $stmt->get_result();
    $fines_row = $fines_result->fetch_assoc();
    $total_fines = $fines_row['total_fines'];

} catch (Exception $e) {
    $error_message = "An error occurred: " . $e->getMessage();
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
    <title>My Profile - The Library</title>
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .profile-section {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .profile-info {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .transactions-table th,
        .transactions-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .transactions-table th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .fines {
            color: #dc3545;
            font-weight: 500;
        }
        .days-remaining {
            font-weight: 500;
        }
        .days-remaining.overdue {
            color: #dc3545;
        }
        .days-remaining.warning {
            color: #ffc107;
        }
        .days-remaining.good {
            color: #28a745;
        }
        .error-message {
            background-color: #fee;
            color: #dc3545;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            border: 1px solid #dc3545;
        }
        .btn-pay-fines {
            background-color: #28a745;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-pay-fines:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">The Library</div>
            <ul class="nav-links">
                <li><a href="browse_books.php">Browse Books</a></li>
                <li><a href="cart.php">Cart<?php echo $cart_count > 0 ? " ($cart_count)" : ''; ?></a></li>
                <li><a href="my_profile.php" class="active">My Profile</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php" class="logout-link">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($user): ?>
                <!-- User Information Section -->
                <section class="profile-section">
                    <h2>Profile Information</h2>
                    <div class="profile-info">
                        <span>Name:</span>
                        <span><?php echo htmlspecialchars($user['name']); ?></span>
                        
                        <span>Username:</span>
                        <span><?php echo htmlspecialchars($user['username']); ?></span>
                        
                        <span>Email:</span>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                        
                        <span>Member Since:</span>
                        <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </section>

                <!-- Current Fines Section -->
                <?php if ($total_fines > 0): ?>
                <section class="profile-section">
                    <h3>Current Fines</h3>
                    <p class="fines">Total Outstanding Fines: $<?php echo number_format($total_fines, 2); ?></p>
                    <form action="pay_fines.php" method="POST">
                        <input type="hidden" name="amount" value="<?php echo $total_fines; ?>">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                        <button type="submit" class="btn-pay-fines">Pay Fines</button>
                    </form>
                </section>
                <?php endif; ?>

                <!-- Transaction History Section -->
                <section class="profile-section">
                    <h3>Transaction History</h3>
                    <?php if ($transactions && $transactions->num_rows > 0): ?>
                        <table class="transactions-table">
                            <thead>
                                <tr>
                                    <th>Book Title</th>
                                    <th>Loan Date</th>
                                    <th>Days Remaining</th>
                                    <th>Fines</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                    <?php
                                    $days = $transaction['days_remaining'];
                                    $status_class = '';
                                    if ($days < 0) {
                                        $status_class = 'overdue';
                                    } elseif ($days <= 3) {
                                        $status_class = 'warning';
                                    } else {
                                        $status_class = 'good';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['title'] ?? 'Unknown Book'); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($transaction['loan_date'])); ?></td>
                                        <td class="days-remaining <?php echo $status_class; ?>">
                                            <?php 
                                            if ($days < 0) {
                                                echo 'Overdue by ' . abs($days) . ' days';
                                            } else {
                                                echo $days . ' days remaining';
                                            }
                                            ?>
                                        </td>
                                        <td class="fines">
                                            <?php 
                                            if ($transaction['fines'] > 0) {
                                                echo '$' . number_format($transaction['fines'], 2);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No transaction history found.</p>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>
</body>
</html>