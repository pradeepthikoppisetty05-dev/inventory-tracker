-- Create Suppliers Table
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Items Table
CREATE TABLE items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    supplier_id INT,
    current_stock INT DEFAULT 0,
    minimum_stock INT DEFAULT 10,
    unit_price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- Create Stock Adjustments Table
CREATE TABLE stock_adjustments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_id INT NOT NULL,
    adjustment_type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL,
    reason VARCHAR(255),
    adjusted_by VARCHAR(100),
    adjustment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- Insert Sample Suppliers
INSERT INTO suppliers (name, contact_person, phone, email) VALUES
('ABC Supplies Co.', 'John Doe', '123-456-7890', 'john@abcsupplies.com'),
('XYZ Electronics', 'Jane Smith', '098-765-4321', 'jane@xyzelectronics.com'),
('Global Parts Ltd.', 'Mike Johnson', '555-123-4567', 'mike@globalparts.com');

-- Insert Sample Items
INSERT INTO items (name, description, supplier_id, current_stock, minimum_stock, unit_price) VALUES
('Laptop', 'Dell Inspiron 15', 2, 15, 5, 45000.00),
('Mouse', 'Wireless Optical Mouse', 2, 50, 10, 500.00),
('Keyboard', 'Mechanical Keyboard', 2, 30, 8, 1500.00),
('Office Chair', 'Ergonomic Office Chair', 1, 8, 3, 8000.00),
('Desk Lamp', 'LED Desk Lamp', 1, 5, 10, 1200.00);

-- Insert Sample Stock Adjustments
INSERT INTO stock_adjustments (item_id, adjustment_type, quantity, reason, adjusted_by) VALUES
(1, 'in', 10, 'New stock arrival', 'Admin'),
(1, 'out', 5, 'Sales', 'Admin'),
(2, 'in', 50, 'Bulk purchase', 'Admin'),
(3, 'in', 30, 'Stock replenishment', 'Admin'),
(4, 'out', 2, 'Office setup', 'Admin');