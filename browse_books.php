<?php
session_start();
require_once 'config.php';
require_once 'update_loans.php';
require_once 'functions.php';

// Pagination
$records_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search parameters
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : 'title';
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// SQL query based on search parameters
$where_clause = "";
if (!empty($search_query)) {
    $search_query = "%{$search_query}%";
    $where_clause = "WHERE $search_type LIKE '$search_query'";
}

// Get total number of books for pagination
$total_query = "SELECT COUNT(*) as total FROM books $where_clause";
$total_result = $conn->query($total_query);
$total_records = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get books for current page
$query = "SELECT * FROM books $where_clause 
          ORDER BY title 
          LIMIT $offset, $records_per_page";
$result = $conn->query($query);
$books = $result->fetch_all(MYSQLI_ASSOC);

$cart_count = getCartCount($conn, $_SESSION['username']);
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
    <title>Browse Books - The Library</title>
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
            <h2>Browse Books</h2>

            <!-- Search Form -->
            <div class="search-section">
                <form method="GET" class="search-form">
                    <select name="search_type" id="search_type">
                        <option value="title" <?php echo $search_type == 'title' ? 'selected' : ''; ?>>Title</option>
                        <option value="author" <?php echo $search_type == 'author' ? 'selected' : ''; ?>>Author</option>
                        <option value="genre" <?php echo $search_type == 'genre' ? 'selected' : ''; ?>>Genre</option>
                    </select>
                    <input type="text" name="search_query" value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>" placeholder="Search...">
                    <button type="submit" class="btn-search">Search</button>
                </form>
            </div>
            
            <div class="books-grid">
                <?php foreach($books as $book): ?>  
                <div class="book-card">
                    <div class="book-image">
                        <img src="media/img/<?php echo $book['isbn']; ?>.jpg" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    </div>
                    <div class="book-info">
                        <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="book-genre"><?php echo htmlspecialchars($book['genre']); ?></p>
                        <p class="book-description"><?php echo htmlspecialchars($book['description']); ?></p>
                        <p class="book-status <?php echo $book['availability']; ?>">
                            Status: <?php echo ucfirst($book['availability']); ?>
                        </p>
                        <?php if($book['availability'] == 'available'): ?>
                            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="isbn" value="<?php echo $book['isbn']; ?>">
                                <button type="submit" class="btn-add-to-cart">Add to Cart</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

                <div class="pagination">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search_type=<?php echo $search_type; ?>&search_query=<?php echo urlencode($search_query); ?>" 
                           class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 The Library. All rights reserved.</p>
    </footer>
</body>
</html>