USE library;

INSERT INTO USERS VALUES
    ('admin', 'admin', 'admin_password', 'admin@thelibrary.com'),
    ('DT340', 'Dominic', 'Password123', 'dominic@xyz.com');

INSERT INTO TRANSACTION VALUES
    (1, 'DT340', '123-456', 0, 30),
    (2, 'DT340', '456-789', 5, -3);

INSERT INTO BOOKS VALUES
    ('Harry Potter', 'A book about a young boy as a wizard', 'Popular Publisher', 'Children', '123-456', 9, 0, 
    STR_TO_DATE('10-10-2024', '%d-%m-%Y')),
    ('Lion King', 'A book about a lion', 'Good Publisher', 'Children', '456-789', 8, 0, 
    STR_TO_DATE('12-10-2024', '%d-%m-%Y')),
    ('Star Wars', 'A book about jedi', 'Good Publisher', 'Action', '122-555', 3, 1, 
    STR_TO_DATE('14-10-2024', '%d-%m-%Y'));