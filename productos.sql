-- Crea la base de datos si no existe
CREATE DATABASE IF NOT EXISTS inventario_db;
USE inventario_db;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(60) NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0
);

-- Datos de prueba (opcional, bórralos si no los necesitas)
INSERT INTO productos (nombre, categoria, cantidad, precio) VALUES
('Cuaderno universitario', 'Papelería', 50, 25.00),
('Lápiz HB', 'Papelería', 200, 3.50),
('Marcador permanente', 'Papelería', 80, 8.00),
('Mouse USB', 'Tecnología', 30, 120.00),
('Teclado', 'Tecnología', 20, 250.00),
('Calculadora científica', 'Tecnología', 15, 180.00),
('Borrador', 'Papelería', 150, 2.00),
('Resma de papel', 'Papelería', 40, 95.00),
('USB 32GB', 'Tecnología', 25, 150.00),
('Silla plástica', 'Mobiliario', 10, 300.00),
('Escritorio', 'Mobiliario', 5, 900.00),
('Pizarra acrílica', 'Mobiliario', 8, 450.00);
