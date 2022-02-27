-- Create Database for PHP Calendar
CREATE DATABASE IF NOT EXISTS php_calendar_db;

-- Use Database
USE php_calendar_db;

-- Create Events Table
CREATE TABLE Events (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_title VARCHAR(30) NOT NULL,
  description VARCHAR(255) NOT NULL,
  date_time DATETIME,
  event_owner VARCHAR(30) NOT NULL
);