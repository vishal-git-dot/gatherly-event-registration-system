CREATE DATABASE IF NOT EXISTS event_registration CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_registration;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(180) NOT NULL,
    capacity INT UNSIGNED NOT NULL DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(event_date)
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ticket_code VARCHAR(40) NOT NULL UNIQUE,
    status ENUM('confirmed','cancelled','attended') NOT NULL DEFAULT 'confirmed',
    UNIQUE KEY unique_registration (event_id, user_id),
    CONSTRAINT fk_reg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    CONSTRAINT fk_reg_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Demo admin. Password: admin123
INSERT INTO users (name, email, password, role)
VALUES ('Admin User', 'admin@gatherly.test', '$2y$12$Ffkx8oD5eCzmw187jncMbujF2PZsQU8aFWzu5TdSpYRzTLkfrc8di', 'admin')
ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password), role='admin';

INSERT INTO events (title, description, event_date, location, capacity)
SELECT 'Design & Technology Meetup',
       'An evening of practical talks, thoughtful conversations and networking for people who build digital products.',
       DATE_ADD(NOW(), INTERVAL 12 DAY),
       'Kochi Innovation Hub',
       120
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Design & Technology Meetup');

INSERT INTO events (title, description, event_date, location, capacity)
SELECT 'Creative Community Night',
       'Meet local creators, share your work and discover new collaborations in a relaxed community setting.',
       DATE_ADD(NOW(), INTERVAL 21 DAY),
       'The Workshop Hall, Kottayam',
       80
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Creative Community Night');

INSERT INTO events (title, description, event_date, location, capacity)
SELECT 'Python Builders Breakfast',
       'A friendly morning session for Python developers to exchange ideas, projects and career lessons.',
       DATE_ADD(NOW(), INTERVAL 30 DAY),
       'Tech Commons, Ernakulam',
       60
WHERE NOT EXISTS (SELECT 1 FROM events WHERE title='Python Builders Breakfast');
