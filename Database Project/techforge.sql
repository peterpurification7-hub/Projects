DROP DATABASE IF EXISTS techforge;
CREATE DATABASE IF NOT EXISTS techforge;
USE techforge;

CREATE TABLE tbluser (
    userid INT PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    type VARCHAR(10) NOT NULL
);

CREATE TABLE tblcomponents (
    compid INT PRIMARY KEY AUTO_INCREMENT,
    category VARCHAR(20) NOT NULL,
    compdesc VARCHAR(50) NOT NULL,
    tier VARCHAR(15) NOT NULL,
    price DOUBLE NOT NULL,
    stock_qty INT DEFAULT 10 
);

CREATE TABLE tblorder (
    orderid INT PRIMARY KEY AUTO_INCREMENT,
    userid INT,
    servicetype VARCHAR(20),
    customerprice DOUBLE DEFAULT 0,
    adminprice DOUBLE DEFAULT 0,
    status VARCHAR(15) DEFAULT 'Pending',
    FOREIGN KEY (userid) REFERENCES tbluser(userid) ON DELETE CASCADE
);

CREATE TABLE tblorder_items (
    itemid INT PRIMARY KEY AUTO_INCREMENT,
    orderid INT,
    compid INT,
    FOREIGN KEY (orderid) REFERENCES tblorder(orderid) ON DELETE CASCADE,
    FOREIGN KEY (compid) REFERENCES tblcomponents(compid) ON DELETE CASCADE
);

INSERT INTO tbluser (firstname, lastname, email, phone, password, type) 
VALUES ('System', 'Admin', 'admin', '000-000-0000', 'admin', 'admin');

-- 30 Components 
INSERT INTO tblcomponents (category, compdesc, tier, price, stock_qty) VALUES 
('CPU', 'Intel Core i3-13100', 'Budget', 110.00, 15), ('CPU', 'Intel Core i5-13600K', 'Pro', 320.00, 8), ('CPU', 'Intel Core i9-14900K', 'Ultra', 590.00, 2),
('GPU', 'NVIDIA GTX 1650', 'Budget', 150.00, 12), ('GPU', 'NVIDIA RTX 4060 Ti', 'Pro', 390.00, 5), ('GPU', 'NVIDIA RTX 4090', 'Ultra', 1700.00, 1),
('CPU Cooler', 'Stock Air Cooler', 'Budget', 0.00, 50), ('CPU Cooler', 'Cooler Master Hyper 212', 'Pro', 45.00, 20), ('CPU Cooler', 'NZXT Kraken Elite 360', 'Ultra', 280.00, 4),
('Motherboard', 'MSI H610M-G', 'Budget', 80.00, 10), ('Motherboard', 'ASUS TUF B760-Plus', 'Pro', 190.00, 7), ('Motherboard', 'ROG Maximus Z790', 'Ultra', 630.00, 3),
('RAM', '8GB DDR4 3200MHz', 'Budget', 74.99, 30), ('RAM', '16GB DDR5 6000MHz', 'Pro', 220.99, 25), ('RAM', '64GB DDR5 7200MHz', 'Ultra', 1159.99, 10),
('Storage', '500GB SATA SSD', 'Budget', 40.00, 40), ('Storage', '1TB NVMe Gen4 SSD', 'Pro', 90.00, 35), ('Storage', '4TB NVMe Gen5 SSD', 'Ultra', 450.00, 6),
('PSU', '500W 80+ White', 'Budget', 45.00, 18), ('PSU', '750W 80+ Gold', 'Pro', 125.00, 12), ('PSU', '1200W 80+ Platinum', 'Ultra', 300.00, 5),
('OS', 'Windows 10 Home', 'Budget', 100.00, 100), ('OS', 'Windows 11 Home', 'Pro', 120.00, 100), ('OS', 'Windows 11 Pro', 'Ultra', 180.00, 100),
('Case', 'NZXT H510 (Budget)', 'Budget', 70.00, 14), ('Case', 'Corsair 4000D Airflow', 'Pro', 105.00, 9), ('Case', 'Lian Li PC-O11 Dynamic', 'Ultra', 200.00, 4),
('Monitor', '24" 1080p 60Hz', 'Budget', 100.00, 11), ('Monitor', '27" 1440p 165Hz', 'Pro', 320.00, 6), ('Monitor', '32" 4K 144Hz OLED', 'Ultra', 950.00, 2);