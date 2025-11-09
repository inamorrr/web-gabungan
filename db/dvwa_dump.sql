CREATE DATABASE IF NOT EXISTS vulnapp;
USE vulnapp;


DROP TABLE IF EXISTS users;
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(100),
password VARCHAR(255),
role VARCHAR(20)
);


INSERT INTO users (username, password, role) VALUES
('admin','admin123','admin'),
('alice','password','user');


DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255),
body TEXT
);


INSERT INTO posts (title, body) VALUES
('Selamat datang','Silakan coba fitur pencarian untuk SQL Injection.'),
('Posting Contoh','Tinggalkan komentar di halaman posting.');


DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
id INT AUTO_INCREMENT PRIMARY KEY,
post_id INT,
author VARCHAR(100),
content TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);