-- Database schema for RentEasy (basic, no constraints for simplicity)

CREATE DATABASE IF NOT EXISTS property_listing_app;
USE property_listing_app;

CREATE TABLE IF NOT EXISTS users (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(120) NOT NULL,
	email VARCHAR(160) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	role ENUM('tenant','landlord','admin') NOT NULL DEFAULT 'tenant',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS properties (
	id INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(200) NOT NULL,
	description TEXT,
	type ENUM('Apartment','House','Land','Office') NOT NULL,
	purpose ENUM('Rent','Sale') NOT NULL,
	price DECIMAL(12,2) NOT NULL DEFAULT 0,
	bedrooms INT NOT NULL DEFAULT 0,
	bathrooms INT NOT NULL DEFAULT 0,
	size_sqft INT NOT NULL DEFAULT 0,
	location VARCHAR(255) NOT NULL,
	furnished ENUM('Furnished','Unfurnished') NOT NULL DEFAULT 'Unfurnished',
	images TEXT,
	approved TINYINT(1) DEFAULT 1,
	landlord_id INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
	id INT AUTO_INCREMENT PRIMARY KEY,
	sender_id INT NOT NULL,
	receiver_id INT NOT NULL,
	property_id INT NOT NULL,
	message TEXT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS favorites (
	id INT AUTO_INCREMENT PRIMARY KEY,
	user_id INT NOT NULL,
	property_id INT NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed admin user (password: admin)
INSERT IGNORE INTO users (id, name, email, password, role) VALUES (1, 'Admin', 'admin@renteasy.local', 'admin', 'admin');


