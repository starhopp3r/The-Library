USE library_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE books (
    isbn VARCHAR(13) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    author VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    availability ENUM('available', 'on loan') DEFAULT 'available'
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    isbn VARCHAR(13) NOT NULL,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username),
    FOREIGN KEY (isbn) REFERENCES books(isbn)
);

CREATE TABLE transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    isbn VARCHAR(13) NOT NULL,
    loan_date DATE NOT NULL,
    days_remaining INT NOT NULL DEFAULT 30,
    fines DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (username) REFERENCES users(username),
    FOREIGN KEY (isbn) REFERENCES books(isbn)
);