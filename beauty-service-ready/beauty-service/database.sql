CREATE DATABASE IF NOT EXISTS beauty_service
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE beauty_service;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS master_services;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'master', 'admin') NOT NULL DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration INT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE master_services (
    master_id INT NOT NULL,
    service_id INT NOT NULL,
    PRIMARY KEY (master_id, service_id),
    FOREIGN KEY (master_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    master_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'rejected', 'cancelled')
        NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (master_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    INDEX (master_id, booking_date, booking_time)
) ENGINE=InnoDB;

INSERT INTO services (name, description, price, duration) VALUES
('Haircut', 'Professional haircut and styling', 25, 60),
('Hair Coloring', 'Hair coloring service', 45, 120),
('Manicure', 'Classic manicure and nail care', 20, 45),
('Pedicure', 'Complete foot and nail care', 25, 60),
('Makeup', 'Professional makeup for your occasion', 35, 60);
