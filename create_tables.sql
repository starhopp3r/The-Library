USE library;

CREATE TABLE USERS (
    name CHAR(100) NOT NULL,
    username CHAR(50) NOT NULL PRIMARY KEY,
    password CHAR(255) NOT NULL,
    email CHAR(100) NOT NULL UNIQUE
);

CREATE TABLE TRANSACTION (
    transactionID INT UNSIGNED NOT NULL PRIMARY KEY,
    username CHAR(50) NOT NULL,
    isbn CHAR(50) NOT NULL,
    fines FLOAT(4,2),
    daysToOverdue SMALLINT
);

CREATE TABLE BOOKS (
    title CHAR(100) NOT NULL,
    description TEXT,
    publisher CHAR(100) NOT NULL,
    genre CHAR(50) NOT NULL,
    isbn CHAR(50) NOT NULL PRIMARY KEY,
    ratings TINYINT UNSIGNED,
    availability BOOLEAN,
    dateAdded TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);