-- =====================================================
-- Bliss Park Resort - Database Schema
-- Run this in phpMyAdmin or MySQL CLI
-- =====================================================

CREATE DATABASE IF NOT EXISTS resort_management;
USE resort_management;

-- =====================================================
-- 1. Users Table (Guests & Admins)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('guest', 'admin') DEFAULT 'guest',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 2. Room Types Table
-- =====================================================
CREATE TABLE IF NOT EXISTS room_types (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL,
    capacity INT DEFAULT 1
) ENGINE=InnoDB;

-- =====================================================
-- 3. Rooms Table
-- =====================================================
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(50) NOT NULL,
    type_id INT NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) DEFAULT 'default-room.jpg',
    status ENUM('available', 'booked', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES room_types(type_id)
) ENGINE=InnoDB;

-- =====================================================
-- 4. Bookings Table
-- =====================================================
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT DEFAULT 1,
    special_requests TEXT,
    total_price DECIMAL(10,2) NOT NULL,
    booking_status ENUM('confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
) ENGINE=InnoDB;

-- =====================================================
-- 5. Payments Table
-- =====================================================
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'mpesa', 'card', 'bank_transfer') DEFAULT 'mpesa',
    payment_status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending',
    transaction_ref VARCHAR(100),
    paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
) ENGINE=InnoDB;

-- =====================================================
-- 6. Contact Messages Table
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- INSERT SAMPLE DATA
-- =====================================================

-- Default Admin (password: admin123)
INSERT INTO users (full_name, email, phone, password, role) VALUES
('Admin User', 'admin@blisspark.com', '0700000000', '$2y$10$YourHashedPasswordHere', 'admin');

-- Room Types
INSERT INTO room_types (type_name, description, base_price, capacity) VALUES
('Standard Single', 'Cozy room with a single bed, work desk, and en-suite bathroom.', 3500.00, 1),
('Deluxe Double', 'Spacious room with a double bed, balcony view, and mini-bar.', 6500.00, 2),
('Family Suite', 'Large suite with two bedrooms, living area, and kitchenette.', 12000.00, 5),
('Executive Suite', 'Premium suite with king bed, jacuzzi, and panoramic views.', 18000.00, 2),
('Honeymoon Villa', 'Private villa with pool access, champagne service, and garden.', 25000.00, 2);

-- Rooms
INSERT INTO rooms (room_name, type_id, price_per_night, description, image_url, status) VALUES
('Room 101', 1, 3500.00, 'Ground floor standard room with garden view.', 'room-standard.jpg', 'available'),
('Room 102', 1, 3500.00, 'Ground floor standard room near the lobby.', 'room-standard.jpg', 'available'),
('Room 201', 2, 6500.00, 'Second floor deluxe with lake view balcony.', 'room-deluxe.jpg', 'available'),
('Room 202', 2, 7000.00, 'Second floor deluxe with mountain view balcony.', 'room-deluxe.jpg', 'available'),
('Room 301', 3, 12000.00, 'Third floor family suite with play area.', 'room-family.jpg', 'available'),
('Room 302', 3, 12000.00, 'Third floor family suite with kitchen.', 'room-family.jpg', 'available'),
('Room 401', 4, 18000.00, 'Top floor executive suite with office space.', 'room-executive.jpg', 'available'),
('Room 402', 4, 18000.00, 'Top floor executive suite with lounge.', 'room-executive.jpg', 'available'),
('Villa 1', 5, 25000.00, 'Beachfront honeymoon villa with private pool.', 'room-villa.jpg', 'available'),
('Villa 2', 5, 25000.00, 'Garden honeymoon villa with hot tub.', 'room-villa.jpg', 'available');

-- NOTE: To create the admin user with proper password hash, 
-- visit hash.php in your browser first, then update the admin password in the database.
-- Or use: UPDATE users SET password='$2y$10$...' WHERE email='admin@blisspark.com';
