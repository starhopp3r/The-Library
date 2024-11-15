USE library_db;

INSERT INTO users (name, username, email, password, is_admin) VALUES 
	('Admin User', 'admin', 'admin@library.com', MD5('admin123'), 1),
	('Dominic Teo', 'dominicteo', 'dominicteo@email.com', MD5('Password!123'), 0),
	('Nikhil', 'nikhil', 'nikhil@email.com', MD5('Password123!'), 0);

INSERT INTO books (isbn, title, description, author, genre, availability) VALUES
	('9780141439518', 'Pride and Prejudice', 'A classic novel of manners, marriage, and social status in early 19th-century England.', 'Jane Austen', 'Classic', 'available'),
	('9780316769488', 'The Catcher in the Rye', 'The story of a teenage boy grappling with alienation and loss in New York City.', 'J.D. Salinger', 'Fiction', 'on loan'),
	('9780547928227', 'The Hobbit', 'A fantasy novel about the adventures of Bilbo Baggins in Middle-earth.', 'J.R.R. Tolkien', 'Fantasy', 'available'),
	('9780451524935', '1984', 'A dystopian novel about totalitarian surveillance and control.', 'George Orwell', 'Science Fiction', 'available'),
	('9780743273565', 'The Great Gatsby', 'A story of wealth, love, and the American Dream in the Roaring Twenties.', 'F. Scott Fitzgerald', 'Classic', 'on loan'),
	('9780062315007', 'The Alchemist', 'A philosophical novel about following one\'s dreams.', 'Paulo Coelho', 'Fiction', 'available'),
	('9780545010221', 'Harry Potter and the Sorcerer\'s Stone', 'First book in the magical series about a young wizard.', 'J.K. Rowling', 'Fantasy', 'available'),
	('9780060935467', 'To Kill a Mockingbird', 'A powerful story about racial injustice in the American South.', 'Harper Lee', 'Classic', 'on loan'),
	('9780307474278', 'The Da Vinci Code', 'A thriller involving conspiracy theories and religious mysteries.', 'Dan Brown', 'Mystery', 'available'),
	('9780553380163', 'A Brief History of Time', 'An exploration of cosmology and the universe.', 'Stephen Hawking', 'Science', 'available'),
	('9780143130727', 'Ikigai: The Japanese Secret to a Long and Happy Life', 'Ikigai reveals the secrets to their longevity and happiness.', 'Héctor García', 'Self-Help', 'available'),
	('9781501110368', 'It Ends with Us', 'A contemporary romance dealing with difficult relationships.', 'Colleen Hoover', 'Romance', 'on loan'),
	('9780141988511', 'Sapiens', 'A brief history of humankind and its development.', 'Yuval Noah Harari', 'History', 'available'),
	('9780439554930', 'Harry Potter and the Chamber of Secrets', 'Second book in the Harry Potter series.', 'J.K. Rowling', 'Fantasy', 'available'),
	('9780007525546', 'The Fault in Our Stars', 'A love story between two teenage cancer patients.', 'John Green', 'Young Adult', 'available');

INSERT INTO transactions (username, isbn, loan_date, days_remaining, fines) VALUES
	('dominicteo', '9780316769488', DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), 20, 0.00),
	('dominicteo', '9780743273565', DATE_SUB(CURRENT_DATE, INTERVAL 35 DAY), -5, 5.00),
	('nikhil', '9780060935467', DATE_SUB(CURRENT_DATE, INTERVAL 25 DAY), 5, 0.00),
	('nikhil', '9781501110368', DATE_SUB(CURRENT_DATE, INTERVAL 40 DAY), -10, 10.00);