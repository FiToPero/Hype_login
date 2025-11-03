#!/bin/bash

# This script initializes the database with the required schema

echo "Setting up Hype Distributor Portal Database..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Check if database exists and create schema
echo "Creating database schema..."

docker exec -i db_database mysql -u root -proot_password << 'EOF'
CREATE DATABASE IF NOT EXISTS hype_distributor;
USE hype_distributor;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50),
    success BOOLEAN NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_agent TEXT,
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_username_time (username, attempt_time)
);

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_active (user_id, is_active),
    INDEX idx_expires (expires_at)
);

-- Grant permissions
GRANT ALL PRIVILEGES ON hype_distributor.* TO 'hype_user'@'%';
FLUSH PRIVILEGES;

EOF

if [ $? -eq 0 ]; then
    echo "Database schema created successfully!"
else
    echo "Error creating database schema"
    exit 1
fi

echo "Database setup completed!"
echo ""
echo "You can now access the application at: http://localhost:8080"
