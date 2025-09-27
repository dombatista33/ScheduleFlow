-- Database schema for Terapia e Bem Estar appointment system

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clients table
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    payment_method VARCHAR(100),
    virtual_room_link VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Available time slots table
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default services
INSERT INTO services (name, description, duration, price) VALUES
('Consulta Inicial', 'Primeira consulta para avaliação e definição do plano terapêutico', 60, 150.00),
('Sessão de Acompanhamento', 'Sessão regular de terapia cognitiva comportamental', 50, 120.00),
('Terapia de Casal', 'Sessão de terapia focada em relacionamentos', 60, 180.00);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password_hash, full_name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dra. Daniela Lima', 'admin@terapiaebemestar.com.br');

-- Generate default time slots for the next 30 days (9 AM to 6 PM, excluding weekends)
DELIMITER //
CREATE PROCEDURE GenerateTimeSlots()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE current_date DATE DEFAULT CURDATE();
    DECLARE end_date DATE DEFAULT DATE_ADD(CURDATE(), INTERVAL 30 DAY);
    DECLARE current_time TIME;
    
    WHILE current_date <= end_date DO
        IF WEEKDAY(current_date) < 5 THEN -- Monday to Friday (0-6, Monday is 0)
            SET current_time = '09:00:00';
            WHILE current_time <= '18:00:00' DO
                INSERT INTO time_slots (date, time) VALUES (current_date, current_time);
                SET current_time = ADDTIME(current_time, '01:00:00');
            END WHILE;
        END IF;
        SET current_date = DATE_ADD(current_date, INTERVAL 1 DAY);
    END WHILE;
END //
DELIMITER ;

CALL GenerateTimeSlots();
DROP PROCEDURE GenerateTimeSlots;