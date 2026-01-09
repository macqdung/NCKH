-- Create user_points table for loyalty program
CREATE TABLE IF NOT EXISTS user_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points DECIMAL(10,2) NOT NULL,
    transaction_type VARCHAR(50) NOT NULL,
    description VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(ID_user) ON DELETE CASCADE
);
