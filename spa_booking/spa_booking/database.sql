CREATE DATABASE IF NOT EXISTS spa_booking
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE spa_booking;

DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 1) users 

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2) categories 

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    image VARCHAR(255) NULL                       
);

-- 3) services

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL,                           -- minutes
    image VARCHAR(255) NULL,                         -- path of the image file
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 4) bookings 

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    notes VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',  -- pending / confirmed / completed / cancelled
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

--   admin@spa.com     ->  admin123
--   sara@example.com  ->  customer123
INSERT INTO users (full_name, email, phone, password, role) VALUES
('Spa Admin', 'admin@spa.com', '0790000000',
 '$2y$10$6T5KRFwrfvmpAbm1q8UNJeBSi71XLa4uhEsE/iqa8twSw3AwcjS4a', 'admin'),
('Sara Ahmad', 'sara@example.com', '0791111111',
 '$2y$10$OLTm9ZHgvhbmXtdiGvRSse6NT0tkdluMFdRXcRaQXDbxGTk5dXy8y', 'customer');

INSERT INTO categories (id, name, description, image) VALUES
(1, 'Massage & Relaxation', 'Release tension and calm your mind with our signature massages.', 'images/categories/massage.jpg'),
(2, 'Facial Treatments', 'Cleanse, refresh and nourish your skin for a natural glow.', 'images/categories/facial.jpg'),
(3, 'Hair & Scalp Care', 'Nourishing masks and soothing scalp rituals for healthy hair.', 'images/categories/hair.jpg'),
(4, 'Body Treatments', 'Scrubs, wraps and masks using natural Dead Sea minerals.', 'images/categories/body.jpg'),
(5, 'Hand & Foot Care', 'Manicures, pedicures and soft-skin treatments.', 'images/categories/handfoot.jpg'),
(6, 'Steam & Sauna', 'Warm, cleansing heat experiences to detox and unwind.', 'images/categories/sauna.jpg'),
(7, 'Hair & Beard Grooming', 'Haircuts, beard trimming and classic hot towel shaves.', 'images/categories/grooming.jpg');

INSERT INTO services (category_id, name, description, price, duration, image) VALUES
-- Massage & Relaxation
(1, 'Full Body Massage', 'A complete head-to-toe massage with warm oils that relaxes every muscle and improves circulation.', 35.00, 60, 'images/services/full_body_massage.jpg'),
(1, 'Back & Neck Massage', 'A focused massage for the back, neck and shoulders - perfect after long hours at a desk.', 20.00, 30, 'images/services/back_neck_massage.jpg'),
(1, 'Aromatherapy Massage', 'A gentle massage with natural essential oils like lavender and eucalyptus to calm body and mind.', 45.00, 75, 'images/services/aromatherapy.jpg'),
(1, 'Hot Stone Massage', 'Smooth heated stones are placed on the body to melt away deep muscle tension.', 55.00, 90, 'images/services/hot_stone.jpg'),

-- Facial Treatments
(2, 'Deep Cleansing Facial', 'Steam, gentle cleansing and extraction to remove impurities and unclog pores.', 30.00, 60, 'images/services/cleansing_facial.jpg'),
(2, 'Hydrating Facial', 'A moisture-rich treatment that brings back softness and glow to dry skin.', 28.00, 45, 'images/services/hydrating_facial.jpg'),
(2, 'Clay Mask & Exfoliation', 'A natural clay mask with soft exfoliation to refine and brighten the skin.', 22.00, 40, 'images/services/clay_mask.jpg'),

-- Hair & Scalp Care
(3, 'Nourishing Hair Mask', 'A rich mask of argan oil and natural butters that repairs dry and damaged hair.', 20.00, 45, 'images/services/hair_mask.jpg'),
(3, 'Deep Conditioning Treatment', 'A warm conditioning treatment that restores shine, strength and smoothness.', 25.00, 50, 'images/services/deep_conditioning.jpg'),
(3, 'Relaxing Scalp Massage', 'A soothing scalp massage that relieves stress and supports healthy hair growth.', 15.00, 30, 'images/services/scalp_massage.jpg'),

-- Body Treatments
(4, 'Dead Sea Salt Scrub', 'A mineral-rich salt scrub that removes dead skin and leaves the body smooth.', 30.00, 45, 'images/services/salt_scrub.jpg'),
(4, 'Dead Sea Mud Mask', 'A full-body mud mask that detoxes the skin and relaxes tired muscles.', 38.00, 60, 'images/services/mud_mask.jpg'),
(4, 'Moisturizing Body Wrap', 'A warm wrap with shea butter and aloe vera for deeply hydrated skin.', 32.00, 50, 'images/services/body_wrap.jpg'),

-- Hand & Foot Care
(5, 'Classic Manicure', 'Nail shaping, cuticle care, hand massage and polish of your choice.', 12.00, 40, 'images/services/manicure.jpg'),
(5, 'Spa Pedicure', 'A warm foot soak, nail care, callus removal and a relaxing foot massage.', 18.00, 50, 'images/services/pedicure.jpg'),
(5, 'Hand & Foot Scrub', 'A sugar scrub and moisturizing mask that leaves hands and feet silky soft.', 15.00, 35, 'images/services/hand_foot_scrub.jpg'),

-- Steam & Sauna
(6, 'Steam Room Session', 'A eucalyptus steam session that opens the pores and clears the breathing.', 10.00, 30, 'images/services/steam_room.jpg'),
(6, 'Finnish Sauna Session', 'Dry wooden-sauna heat that relaxes the muscles and helps the body detox.', 10.00, 30, 'images/services/sauna.jpg'),
(6, 'Moroccan Bath (Hammam)', 'A traditional hammam with black soap, steam and a full body scrub.', 30.00, 60, 'images/services/hammam.jpg'),

-- Hair & Beard Grooming
(7, 'Classic Haircut', 'A professional haircut and styling that suits your face and your look.', 8.00, 30, 'images/services/haircut.jpg'),
(7, 'Beard Trim & Shaping', 'Precise beard trimming and line-up for a clean, sharp look.', 6.00, 20, 'images/services/beard_trim.jpg'),
(7, 'Hot Towel Shave', 'A classic razor shave with hot towels and a soothing after-shave balm.', 10.00, 30, 'images/services/hot_towel_shave.jpg');

INSERT INTO bookings (user_id, service_id, booking_date, booking_time, notes, status) VALUES
(2, 1, '2026-11-05', '14:00:00', 'Medium pressure please', 'confirmed'),
(2, 11, '2026-11-12', '11:00:00', NULL, 'pending'),
(2, 5, '2026-10-01', '16:00:00', NULL, 'completed');
