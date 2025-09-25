deposit
CREATE DATABASE IF NOT EXISTS user DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE user;

DROP TABLE IF EXISTS deposits;

DELIMITER $$

CREATE TABLE deposits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10, 2) NOT NULL CHECK (amount > 0),
    session_id VARCHAR(255) NOT NULL UNIQUE,
    status ENUM(
        'Pending',
        'Completed',
        'Failed',
        'Cancelled'
    ) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT deposits_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
$$

CREATE TRIGGER before_deposit_insert
BEFORE INSERT ON deposits
FOR EACH ROW
BEGIN
  IF NEW.amount <= 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Deposit amount must be positive';
  END IF;
END;
$$

CREATE TRIGGER after_deposit_insert
AFTER INSERT ON deposits
FOR EACH ROW
BEGIN
  IF NEW.status = 'Completed' THEN
    UPDATE users SET balance = balance + NEW.amount WHERE id = NEW.user_id;
  END IF;
END;
$$

CREATE TRIGGER after_deposit_update
AFTER UPDATE ON deposits
FOR EACH ROW
BEGIN
 
  IF OLD.status <> 'Completed' AND NEW.status = 'Completed' THEN
    UPDATE users SET balance = balance + NEW.amount WHERE id = NEW.user_id;
  END IF;

  
  IF OLD.status = 'Completed' AND NEW.status <> 'Completed' THEN
    UPDATE users SET balance = balance - OLD.amount WHERE id = OLD.user_id;
  END IF;
END;
$$

DELIMITER;