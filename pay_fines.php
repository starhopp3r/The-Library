<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Validate payment amount and username
if (!isset($_POST['amount']) || !isset($_POST['username'])) {
    header('Location: my_profile.php');
    exit();
}

$amount = floatval($_POST['amount']);
$username = $_POST['username'];

// Verify this username matches the logged-in user
if ($_SESSION['username'] !== $username) {
    header('Location: my_profile.php');
    exit();
}

// Process payment if form is submitted
$payment_error = '';
$payment_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'])) {
    // Validate credit card details
    $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvv = $_POST['cvv'];
    
    // Validate card number (using Luhn algorithm)
    function validateCard($number) {
        $sum = 0;
        $numDigits = strlen($number);
        $parity = $numDigits % 2;
        
        for ($i = $numDigits - 1; $i >= 0; $i--) {
            $digit = intval($number[$i]);
            
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return ($sum % 10) == 0;
    }
    
    // Validate expiry date
    $current_year = date('Y');
    $current_month = date('m');
    
    if (!preg_match('/^[0-9]{16}$/', $card_number)) {
        $payment_error = "Invalid card number format";
    } elseif (!validateCard($card_number)) {
        $payment_error = "Invalid card number";
    } elseif (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        $payment_error = "Invalid CVV";
    } elseif ($expiry_year < $current_year || 
              ($expiry_year == $current_year && $expiry_month < $current_month)) {
        $payment_error = "Card has expired";
    } else {
        // This is where you would normally process the payment with a payment gateway
        // For demo purposes, we'll just mark it as successful
        
        // Update the database to clear the fines
        try {
            // Start transaction
            $conn->begin_transaction();
            
            // First, update the books status to 'available' for all paid transactions
            $update_books_query = "UPDATE books b 
                                 JOIN transactions t ON b.isbn = t.isbn 
                                 SET b.availability = 'available' 
                                 WHERE t.username = ? AND t.fines > 0";
            $stmt = $conn->prepare($update_books_query);
            $stmt->bind_param("s", $username);
            $stmt->execute();

            // Delete the paid transactions
            $delete_query = "DELETE FROM transactions WHERE username = ? AND fines > 0";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            
            // Log the payment (you might want to create a payments table in your database)
            // For demo purposes, we'll just commit the transaction
            $conn->commit();
            
            $payment_success = true;
            
        } catch (Exception $e) {
            $conn->rollback();
            $payment_error = "Error processing payment: " . $e->getMessage();
        }
    }
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
    <title>Pay Fines - The Library</title>
    <style>
        .payment-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .payment-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .form-group label {
            font-weight: 500;
        }
        .form-group input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .expiry-group {
            display: flex;
            gap: 1rem;
        }
        .expiry-group select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .error-message {
            color: #dc3545;
            background-color: #fee;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #dc3545;
            margin-bottom: 1rem;
        }
        .success-message {
            color: #28a745;
            background-color: #dff0d8;
            padding: 1rem;
            border-radius: 4px;
            border: 1px solid #28a745;
            margin-bottom: 1rem;
        }
        .btn-pay {
            background-color: #28a745;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-pay:hover {
            background-color: #218838;
        }
        .card-info {
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">The Library</div>
            <ul class="nav-links">
                <li><a href="browse_books.php">Browse Books</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="my_profile.php">My Profile</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="logout.php" class="logout-link">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="payment-container">
            <h2>Pay Library Fines</h2>
            
            <?php if ($payment_success): ?>
                <div class="success-message">
                    Payment processed successfully! Your fines have been cleared.
                    <p><a href="my_profile.php">Return to My Profile</a></p>
                </div>
            <?php else: ?>
                <?php if ($payment_error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($payment_error); ?></div>
                <?php endif; ?>

                <div class="card-info">
                    <p><strong>Amount to Pay:</strong> $<?php echo number_format($amount, 2); ?></p>
                </div>

                <form method="POST" class="payment-form">
                    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" 
                               placeholder="1234 5678 9012 3456" maxlength="19" required
                               pattern="\d{4}\s?\d{4}\s?\d{4}\s?\d{4}">
                    </div>

                    <div class="form-group">
                        <label>Expiry Date</label>
                        <div class="expiry-group">
                            <select name="expiry_month" required>
                                <option value="">Month</option>
                                <?php for($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo sprintf('%02d', $m); ?>">
                                        <?php echo sprintf('%02d', $m); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <select name="expiry_year" required>
                                <option value="">Year</option>
                                <?php 
                                $current_year = date('Y');
                                for($y = $current_year; $y <= $current_year + 10; $y++): 
                                ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" 
                               placeholder="123" maxlength="4" required
                               pattern="\d{3,4}">
                    </div>

                    <button type="submit" class="btn-pay">Pay $<?php echo number_format($amount, 2); ?></button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>

    <script>
        // Add spaces after every 4 digits in card number
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = formattedValue;
        });
    </script>
</body>
</html>